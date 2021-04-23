<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CountryTest extends TestCase
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
            ->visit('/country')
            ->see('Country List');
    }

    /**
     * Updating a country information
     *
     * @return void
     */
    public function testEdit()
    {
        $country = \App\Country::first();

        $this->actingAs( \App\User::first() )
            ->visit('/country/'.$country->id.'/edit')
            ->see('Update Country Information')
            ->type('Bangladesh', 'name')
            ->type('BD', 'code')
            ->type('+880', 'prefix')
            ->select(true, 'status')
            ->press('Update Now')
            ->seePageIs('/country/'.$country->id.'/edit')
            ->see('Success!');
    }

}
