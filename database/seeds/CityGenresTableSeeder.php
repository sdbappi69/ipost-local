<?php

use Illuminate\Database\Seeder;

class CityGenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('city_genres')->delete();

        $genres = array(
            array('title' => 'Inside Dhaka', 'description' => 'Dhaka City'),
            array('title' => 'Dhaka Suburb', 'description' => 'Dhaka sub-districts'),
            array('title' => 'Divisional City', 'description' => 'Divisional City'),
            array('title' => 'District City', 'description' => 'District City'),
        );

        \Illuminate\Support\Facades\DB::table('city_genres')->insert($genres);
    }
}
