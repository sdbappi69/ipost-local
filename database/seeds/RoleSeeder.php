<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('roles')->delete();

        $role = [
            ['name' => 'superadministrator', 'display_name' => 'Super Administrator'],
            ['name' => 'systemadministrator', 'display_name' => 'System Administrator'],
            ['name' => 'systemmoderator', 'display_name' => 'System Moderator'],
            ['name' => 'accountsadministrator', 'display_name' => 'Accounts Administrator'],
            ['name' => 'accountsoperator', 'display_name' => 'Accounts Operator'],
            ['name' => 'hubmanager', 'display_name' => 'Hub Manager'],
            ['name' => 'vehiclemanager', 'display_name' => 'Vehicle Manager'],
            ['name' => 'delivery-pickupman', 'display_name' => 'Delivery/Pickup Man'],
            ['name' => 'merchantadmin', 'display_name' => 'Merchant Admin'],
            ['name' => 'merchantaccounts', 'display_name' => 'Merchant Accounts'],
            ['name' => 'merchantsupport', 'display_name' => 'Merchant Support'],
            ['name' => 'storeadmin', 'display_name' => 'Store Admin'],
            ['name' => 'guest', 'display_name' => 'Guest'],
        ];

        \Illuminate\Support\Facades\DB::table('roles')->insert( $role );
    }
}