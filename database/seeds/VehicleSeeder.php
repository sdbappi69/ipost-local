<?php

use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        \Illuminate\Support\Facades\DB::table('vehicles')->delete();

        for( $i = 0; $i < 25; $i++ ) {
            \App\Vehicle::create([
                'name'              =>  $faker->companySuffix,
                'photo'             =>  'http://placehold.it/480x320',
                'contact_msisdn'    =>  $faker->phoneNumber,
                'vehicle_type_id'   =>  $faker->randomElement( \Illuminate\Support\Facades\DB::table('vehicle_types')->lists('id') ),
                'license_no'        =>  $faker->numberBetween(10000,99999),
                'brand'             =>  $faker->company,
                'model'             =>  $faker->companySuffix.'-'.$faker->company,
                'latitude'          =>  $faker->latitude,
                'longitude'         =>  $faker->longitude,
                'status'            =>  true
            ]);
        }
    }
}
