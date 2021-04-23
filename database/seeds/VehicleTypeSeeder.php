<?php

use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        \Illuminate\Support\Facades\DB::table('vehicle_types')->delete();

        $types = [
            [
                'title'         =>  'Bicycle',
                'details'       =>  $faker->paragraph(3),
                'status'        =>  true,
                'created_at'    =>  \Carbon\Carbon::now(),
                'updated_at'    =>  \Carbon\Carbon::now(),
                'created_by'    =>  \App\User::first()->id,
                'updated_by'    =>  \App\User::first()->id
            ],
            [
                'title'         =>  'Motorcycle',
                'details'       =>  $faker->paragraph(3),
                'status'        =>  true,
                'created_at'    =>  \Carbon\Carbon::now(),
                'updated_at'    =>  \Carbon\Carbon::now(),
                'created_by'    =>  \App\User::first()->id,
                'updated_by'    =>  \App\User::first()->id
            ],
            [
                'title'         =>  'Medium Sized Pickup',
                'details'       =>  $faker->paragraph(3),
                'status'        =>  true,
                'created_at'    =>  \Carbon\Carbon::now(),
                'updated_at'    =>  \Carbon\Carbon::now(),
                'created_by'    =>  \App\User::first()->id,
                'updated_by'    =>  \App\User::first()->id
            ],
            [
                'title'         =>  'Container Truck',
                'details'       =>  $faker->paragraph(3),
                'status'        =>  true,
                'created_at'    =>  \Carbon\Carbon::now(),
                'updated_at'    =>  \Carbon\Carbon::now(),
                'created_by'    =>  \App\User::first()->id,
                'updated_by'    =>  \App\User::first()->id
            ]
        ];

        \Illuminate\Support\Facades\DB::table('vehicle_types')->insert( $types );
    }
}
