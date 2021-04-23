<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VehicleTypeTest extends TestCase
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
            ->visit('/vehicle-type')
            ->see('Vehicle Type Listing');
    }

    /**
     * Adding a new vehicle type test
     *
     * @return void
     */
    public function testAddNewVehicleType()
    {
        $this->actingAs( \App\User::first() )
            ->visit('/vehicle-type')
            ->see('Add New Vehicle Type')
            ->type('Small Sized Pickup', 'title')
            ->type('Lorem ipsum dolor sit amet...', 'details')
            ->select(1, 'status')
            ->press('Add Now')
            ->seePageIs('/vehicle-type')
            ->see('Added!');
    }

    /**
     * Updating a new vehicle type test
     *
     * @return void
     */
    public function testUpdateVehicleType()
    {
        $vehicleType = \App\VehicleType::first();

        $this->actingAs( \App\User::first() )
            ->visit('/vehicle-type/'.$vehicleType->id.'/edit')
            ->see('Update Vehicle Type')
            ->type('Small Sized Pickup', 'title')
            ->type('Lorem ipsum dolor sit amet...', 'details')
            ->select(1, 'status')
            ->press('Update Now')
            ->seePageIs('/vehicle-type/'.$vehicleType->id.'/edit')
            ->see('Updated!');
    }

}
