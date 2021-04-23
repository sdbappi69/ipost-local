<?php

use Illuminate\Database\Seeder;

class StoreTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('store_types')->delete();

        $data = array(
            array('title' => 'Test', 'base_url' => 'https//www.sslwireless.com'),
            array('title' => 'Live', 'base_url' => 'https//www.sslwireless.com'),
        );

        \Illuminate\Support\Facades\DB::table('store_types')->insert($data);
    }
}
