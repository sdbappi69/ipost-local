<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('users')->delete();

        $faker = Faker\Factory::create();

        $zone = \App\Zone::with('city.state.country')->whereZipCode('1217')->first();

        $users = [
            [
                'name'          =>  $faker->name,
                'photo'         =>  'http://placehold.it/180x180',
                'email'         =>  'superadmin@logistics.com',
                'password'      =>  bcrypt('qwerty'),
                'msisdn'        =>  $faker->phoneNumber,
                'user_type_id'  =>  \App\UserType::whereTitle("Super Administrator")->first()->id,
                'country_id'    =>  $zone->city->state->country->id,
                'state_id'      =>  $zone->city->state->id,
                'city_id'       =>  $zone->city->id,
                'zone_id'       =>  $zone->id,
                'address1'      =>  $faker->streetAddress,
                'latitude'      =>  $faker->latitude,
                'longitude'     =>  $faker->longitude,
                'status'        =>  true
            ],
            [
                'name'          =>  $faker->name,
                'photo'         =>  'http://placehold.it/180x180',
                'email'         =>  'systemadmin@logistics.com',
                'password'      =>  bcrypt('qwerty'),
                'msisdn'        =>  $faker->phoneNumber,
                'user_type_id'  =>  \App\UserType::whereTitle("System Administrator")->first()->id,
                'country_id'    =>  $zone->city->state->country->id,
                'state_id'      =>  $zone->city->state->id,
                'city_id'       =>  $zone->city->id,
                'zone_id'       =>  $zone->id,
                'address1'      =>  $faker->streetAddress,
                'latitude'      =>  $faker->latitude,
                'longitude'     =>  $faker->longitude,
                'status'        =>  true
            ]
        ];

        \Illuminate\Support\Facades\DB::table('users')->insert( $users );
    }
}
