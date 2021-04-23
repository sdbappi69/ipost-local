<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DriverTest extends TestCase
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
            ->visit('driver')
            ->see('Driver Listing');
    }

    /**
     * Adding a new driver test
     *
     * @return void
     */
    public function testAddDriverTest()
    {
        $vehicleIds = \App\Vehicle::lists('id')->toArray();
        $faker = Faker\Factory::create();

        $this->actingAs( \App\User::first() )
            ->visit('/driver')
            ->see('Add New Driver')
//            ->select( $faker->randomElement( $vehicleIds ), 'vehicle_id[]' )
            ->type($faker->name, 'name')
            ->type($faker->phoneNumber, 'contact_msisdn')
            ->type($faker->date('Y-m-d'), 'date_of_birth')
            ->type($faker->streetName, 'driving_license_no')
            ->type($faker->name, 'reference_name')
            ->type($faker->phoneNumber, 'reference_msisdn')
            ->select(1, 'status')
            ->press('Add Now')
            ->seePageIs('/driver')
            ->see('Added!');
    }

    /**
     * View a driver details test
     *
     * @return void
     */
    public function testViewDriverTest()
    {
        $driver = \App\Driver::first();

        $this->actingAs( \App\User::first() )
            ->visit('/driver/'.$driver->id)
            ->see($driver->name);
    }

    /**
     * Updating a driver information test
     *
     * @return void
     */
    public function testUpdateDriverInformation()
    {
        $vehicleIds = \App\Vehicle::lists('id')->toArray();
        $driver = \App\Driver::first();
        $faker = Faker\Factory::create();

        $this->actingAs( \App\User::first() )
            ->visit('/driver/'.$driver->id.'/edit')
            ->see('Update Driver Information')
//            ->select( $faker->randomElement( $vehicleIds ), 'vehicle_id[]' )
            ->type($faker->name, 'name')
            ->type($faker->phoneNumber, 'contact_msisdn')
            ->type($faker->date('Y-m-d'), 'date_of_birth')
            ->type($faker->streetName, 'driving_license_no')
            ->type($faker->name, 'reference_name')
            ->type($faker->phoneNumber, 'reference_msisdn')
            ->select(1, 'status')
            ->press('Update Now')
            ->seePageIs('/driver/'.$driver->id.'/edit')
            ->see('Updated!');
    }

}
