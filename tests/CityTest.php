<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CityTest extends TestCase
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
            ->visit('/city')
            ->see('City List');
    }

    /**
     * Updating a city information test
     *
     * @return void
     */
    public function testEdit()
    {
        $city = \App\City::first();

        $this->actingAs( \App\User::first() )
            ->visit('/city/'.$city->id.'/edit')
            ->see('Update City / District Information')
            ->type('Chittagong', 'name')
            ->select(2, 'state_id')
            ->select(true, 'status')
            ->press('Update Now')
            ->seePageIs('/city/'.$city->id.'/edit')
            ->see('Updated!');
    }

}
