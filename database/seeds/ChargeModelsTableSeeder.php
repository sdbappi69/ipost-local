<?php

use Illuminate\Database\Seeder;

class ChargeModelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('charge_models')->delete();

        $models = array(
            array('title' => 'COD', 'description' => "Cash On Delivery", 'unit' => 'BDT'),
            array('title' => 'Fixed', 'description' => "No calculation needed. It's Fixed.", 'unit' => 'none'),
            array('title' => '0 to 1 Kg', 'description' => "If product weight is bellow 1 Kg.", 'unit' => 'Kg'),
            array('title' => '1 to 2 Kg', 'description' => "If product weight is between 1-2 Kg.", 'unit' => 'Kg'),
        );

        \Illuminate\Support\Facades\DB::table('charge_models')->insert($models);
    }
}
