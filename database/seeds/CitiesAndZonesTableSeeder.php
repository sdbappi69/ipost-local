<?php

use Illuminate\Database\Seeder;

class CitiesAndZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('cities')->delete();
        \Illuminate\Support\Facades\DB::table('zones')->delete();

        $jsonFiles = scandir(public_path(). '/json');

        foreach($jsonFiles as $divisionFileName) {
            $isValidJsonFile = ( substr($divisionFileName, 0, 1) == '.' ) ? false : true;

            if( $isValidJsonFile ) {
                $explodedDivisionName = explode('.', $divisionFileName);
                $divisionName = strtoupper( $explodedDivisionName[0] );
                $divisionId = \App\State::whereRaw( "LOWER(name) = LOWER('$divisionName')" )->first()->id;

                $cityData = file_get_contents( public_path(). '/json/' .$divisionFileName );
                $cityArrayData = json_decode( $cityData, true );

                foreach( $cityArrayData as $data) {

                    $city = [
                        'name'      =>  $data['city'],
                        'state_id'  =>  $divisionId,
                        'status'    =>  true
                    ];

                    $thisCity = \App\City::firstOrCreate( $city );

                    $zone = [
                        'name'      =>  $data['zone'],
                        'city_id'   =>  $thisCity->id,
                        'zip_code'  =>  $data['zip'],
                        'status'    =>  true
                    ];

                    \App\Zone::create( $zone );

                }

            }
        }
    }
}
