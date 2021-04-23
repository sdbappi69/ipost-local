<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VehicleTest extends TestCase
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
            ->visit('/vehicle')
            ->see('Vehicle Listing');
    }

    /**
     * Adding a new vehicle test
     *
     * @return void
     */
    public function testAddVehicleTest()
    {
        $vehicleTypes = \App\VehicleType::lists('id')->toArray();
        $faker = Faker\Factory::create();

        $this->actingAs( \App\User::first() )
            ->visit('/vehicle')
            ->see('Add New Vehicle')
            ->select( $faker->randomElement( $vehicleTypes ), 'vehicle_type_id' )
            ->type($faker->name, 'name')
            ->type($faker->email, 'contact_email')
            ->type($faker->phoneNumber, 'contact_msisdn')
            ->type($faker->name, 'license_no')
            ->type($faker->name, 'brand')
            ->type($faker->name, 'model')
            ->type($faker->latitude, 'latitude')
            ->type($faker->longitude, 'longitude')
            ->select(1, 'status')
            ->press('Add Now')
            ->seePageIs('/vehicle')
            ->see('Added!');
    }

    /**
     * Updating a vehicle information test
     *
     * @return void
     */
    public function testUpdateVehicleTest()
    {
        $vehicleTypes = \App\VehicleType::lists('id')->toArray();
        $vehicle = \App\Vehicle::first();
        $faker = Faker\Factory::create();

        $this->actingAs( \App\User::first() )
            ->visit('/vehicle/'.$vehicle->id.'/edit')
            ->see('Update Vehicle Information')
            ->select( $faker->randomElement( $vehicleTypes ), 'vehicle_type_id' )
            ->type($faker->name, 'name')
            ->type($faker->email, 'contact_email')
            ->type($faker->phoneNumber, 'contact_msisdn')
            ->type($faker->name, 'license_no')
            ->type($faker->name, 'brand')
            ->type($faker->name, 'model')
            ->type($faker->latitude, 'latitude')
            ->type($faker->longitude, 'longitude')
            ->select(1, 'status')
            ->press('Update Now')
            ->seePageIs('/vehicle/'.$vehicle->id.'/edit')
            ->see('Updated!');
    }

}
