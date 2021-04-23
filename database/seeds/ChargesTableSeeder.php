<?php

use Illuminate\Database\Seeder;

class ChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('charges')->delete();

        $charges = array();

    	// COD
        $charges[] = array('store_id' => null, 'product_category_id' => null, 'zone_genre_id' => null, 'charge_model_id' => '1', 'percentage_range_start' => '1', 'percentage_range_end' => '5000', 'additional_range_per_slot' => '1000', 'additional_charge_per_slot' => '15', 'fixed_charge' => '0', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);

        // Television
        for ($i=7; $i <= 12; $i++) {
        	for ($j=1; $j <= 4 ; $j++) {
        		$charges[] = array('store_id' => null, 'product_category_id' => $i, 'zone_genre_id' => $j, 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '250', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        	}
        }
        for ($i=13; $i <= 15; $i++) {
        	for ($j=1; $j <= 4 ; $j++) {
        		$charges[] = array('store_id' => null, 'product_category_id' => $i, 'zone_genre_id' => $j, 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '300', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        	}
        }
        for ($j=1; $j <= 4 ; $j++) {
    		$charges[] = array('store_id' => null, 'product_category_id' => '16', 'zone_genre_id' => $j, 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '400', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
    	}
    	for ($j=1; $j <= 4 ; $j++) {
    		$charges[] = array('store_id' => null, 'product_category_id' => '17', 'zone_genre_id' => $j, 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '450', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
    	}

        // Bank & Others
        $charges[] = array('store_id' => null, 'product_category_id' => '18', 'zone_genre_id' => '1', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '10', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '19', 'zone_genre_id' => '1', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '12', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '18', 'zone_genre_id' => '2', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '12', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '19', 'zone_genre_id' => '2', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '15', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '18', 'zone_genre_id' => '3', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '12', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '19', 'zone_genre_id' => '3', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '15', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '18', 'zone_genre_id' => '4', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '12', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '19', 'zone_genre_id' => '4', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '15', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        
        // Online Ticket
        $charges[] = array('store_id' => null, 'product_category_id' => '20', 'zone_genre_id' => '1', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '35', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '21', 'zone_genre_id' => '1', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '45', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '20', 'zone_genre_id' => '2', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '45', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '21', 'zone_genre_id' => '2', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '55', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '20', 'zone_genre_id' => '3', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '65', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '21', 'zone_genre_id' => '3', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '75', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '20', 'zone_genre_id' => '4', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '75', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '21', 'zone_genre_id' => '4', 'charge_model_id' => '2', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '85', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);

        // Exclusive Product
        for ($i=22; $i <= 26; $i++) {
        	$charges[] = array('store_id' => null, 'product_category_id' => $i, 'zone_genre_id' => '1', 'charge_model_id' => '3', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '1', 'additional_charge_per_slot' => '15', 'fixed_charge' => '100', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        	$charges[] = array('store_id' => null, 'product_category_id' => $i, 'zone_genre_id' => '2', 'charge_model_id' => '3', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '1', 'additional_charge_per_slot' => '15', 'fixed_charge' => '120', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        	$charges[] = array('store_id' => null, 'product_category_id' => $i, 'zone_genre_id' => '3', 'charge_model_id' => '3', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '1', 'additional_charge_per_slot' => '15', 'fixed_charge' => '150', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        	$charges[] = array('store_id' => null, 'product_category_id' => $i, 'zone_genre_id' => '4', 'charge_model_id' => '3', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '1', 'additional_charge_per_slot' => '15', 'fixed_charge' => '200', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        }

        // Others
        $charges[] = array('store_id' => null, 'product_category_id' => '6', 'zone_genre_id' => '1', 'charge_model_id' => '3', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '65', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '6', 'zone_genre_id' => '2', 'charge_model_id' => '3', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '75', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '6', 'zone_genre_id' => '3', 'charge_model_id' => '3', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '110', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '6', 'zone_genre_id' => '4', 'charge_model_id' => '3', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '0', 'additional_charge_per_slot' => '0', 'fixed_charge' => '130', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);

        $charges[] = array('store_id' => null, 'product_category_id' => '6', 'zone_genre_id' => '1', 'charge_model_id' => '4', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '1', 'additional_charge_per_slot' => '20', 'fixed_charge' => '80', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '6', 'zone_genre_id' => '2', 'charge_model_id' => '4', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '1', 'additional_charge_per_slot' => '25', 'fixed_charge' => '100', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '6', 'zone_genre_id' => '3', 'charge_model_id' => '4', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '1', 'additional_charge_per_slot' => '30', 'fixed_charge' => '140', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        $charges[] = array('store_id' => null, 'product_category_id' => '6', 'zone_genre_id' => '4', 'charge_model_id' => '4', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '1', 'additional_charge_per_slot' => '30', 'fixed_charge' => '160', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);

        // Bulk Products
        for ($i=1; $i <= 4; $i++) {
            $charges[] = array('store_id' => null, 'product_category_id' => '5', 'zone_genre_id' => $i, 'charge_model_id' => '3', 'percentage_range_start' => '0', 'percentage_range_end' => '0', 'additional_range_per_slot' => '1', 'additional_charge_per_slot' => '15', 'fixed_charge' => '35', 'created_by' => \App\User::first()->id, 'updated_by' => \App\User::first()->id);
        }

        \Illuminate\Support\Facades\DB::table('charges')->insert($charges);
    }
}
