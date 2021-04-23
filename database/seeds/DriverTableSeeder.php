<?php

use Illuminate\Database\Seeder;

class DriverTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $vehicleIds = \App\Vehicle::lists('id')->toArray();

        for( $i = 0; $i < 50; $i++ ) {
            \App\Driver::create([
                'name'                  =>  $faker->name,
                'photo'                 =>  'http://placehold.it/600x600',
                'contact_email'         =>  $faker->email,
                'contact_msisdn'        =>  $faker->phoneNumber,
                'date_of_birth'         =>  $faker->date('Y-m-d'),
                'driving_license_no'    =>  $faker->bankAccountNumber,
                'reference_name'        =>  $faker->name,
                'reference_email'       =>  $faker->email,
                'reference_msisdn'      =>  $faker->phoneNumber,
                'status'                =>  true
            ])->vehicles()->attach( $faker->randomElement( $vehicleIds ) );
        }
    }
}
