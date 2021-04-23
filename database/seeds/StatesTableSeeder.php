<?php

use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('states')->delete();

        $countryId = \App\Country::whereName('Bangladesh')->first()->id;

        $states = [
            [ 'name' => 'Dhaka', 'country_id' => $countryId ],
            [ 'name' => 'Chittagong', 'country_id' => $countryId ],
            [ 'name' => 'Khulna', 'country_id' => $countryId ],
            [ 'name' => 'Sylhet', 'country_id' => $countryId ],
            [ 'name' => 'Barisal', 'country_id' => $countryId ],
            [ 'name' => 'Rajshahi', 'country_id' => $countryId ],
            [ 'name' => 'Rangpur', 'country_id' => $countryId ],
        ];

        \Illuminate\Support\Facades\DB::table('states')->insert( $states );
    }
}
