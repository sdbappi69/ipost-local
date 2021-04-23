<?php

use Illuminate\Database\Seeder;

class UserTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('user_types')->delete();

        $userTypes = [
            ['title' => 'Super Administrator', 'status' => true],
            ['title' => 'System Administrator', 'status' => true],
            ['title' => 'System Moderator', 'status' => true],
            ['title' => 'Accounts Administrator', 'status' => true],
            ['title' => 'Accounts Operator', 'status' => true],
            ['title' => 'Hub Manager', 'status' => true],
            ['title' => 'Vehicle Manager', 'status' => true],
            ['title' => 'Delivery/Pickup Man', 'status' => true],
            ['title' => 'Merchant Admin', 'status' => true],
            ['title' => 'Merchant Accounts', 'status' => true],
            ['title' => 'Merchant Support', 'status' => true],
            ['title' => 'Store Admin', 'status' => true],
            ['title' => 'Guest', 'status' => true],
        ];

        \Illuminate\Support\Facades\DB::table('user_types')->insert( $userTypes );
    }
}
