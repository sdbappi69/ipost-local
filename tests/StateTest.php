<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StateTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->actingAs( \App\User::first() )
            ->visit('/state')
            ->see('State List');
    }

    /**
     * Updating a state information test
     *
     * @return void
     */
    public function testEdit()
    {
        $state = \App\State::first();

        $this->actingAs( \App\User::first() )
            ->visit('/state/'.$state->id.'/edit')
            ->see('Update State Information')
            ->type('Dhaka', 'name')
            ->select(20, 'country_id')
            ->select(true, 'status')
            ->press('Update Now')
            ->seePageIs('/state/'.$state->id.'/edit')
            ->see('Success!');
    }

}
