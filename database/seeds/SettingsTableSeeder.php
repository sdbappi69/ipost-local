<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('settings')->delete();

        $faker = Faker\Factory::create();

        $zone = \App\Zone::with('city.state.country')->whereZipCode('1217')->first();

        $settings = [
            'title'         =>  'Logistics',
            'logo'          =>  'http://placehold.it/250x250',
            'email'         =>  $faker->email,
            'website'       =>  $faker->domainName,
            'msisdn'        =>  $faker->phoneNumber,
            'country_id'    =>  $zone->city->state->country->id,
            'state_id'      =>  $zone->city->state->id,
            'city_id'       =>  $zone->city->id,
            'zone_id'       =>  $zone->id,
            'address1'      =>  $faker->streetAddress,
            'latitude'      =>  $faker->latitude,
            'longitude'     =>  $faker->longitude,
            'created_at'    =>  \Carbon\Carbon::now(),
            'updated_at'    =>  \Carbon\Carbon::now()
        ];

        \Illuminate\Support\Facades\DB::table('settings')->insert( $settings );
    }
}
