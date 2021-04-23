<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('permissions')->delete();

        $role = [
            ['name' => 'create-merchant', 'display_name' => 'Create Merchant'],
            ['name' => 'edit-merchant', 'display_name' => 'Edit Merchant'],
            ['name' => 'view-merchant', 'display_name' => 'View Merchant'],
            ['name' => 'create-store', 'display_name' => 'Create Store'],
            ['name' => 'edit-store', 'display_name' => 'Edit Store'],
            ['name' => 'view-store', 'display_name' => 'View Store'],
            ['name' => 'vehicle', 'display_name' => 'Vehicle'],
            ['name' => 'vehicle-type', 'display_name' => 'Vehicle Type'],
            ['name' => 'driver', 'display_name' => 'Driver'],
            ['name' => 'create-user', 'display_name' => 'Create User'],
            ['name' => 'edit-user', 'display_name' => 'Edit User'],
            ['name' => 'view-user', 'display_name' => 'View User'],
            ['name' => 'location', 'display_name' => 'Location'],
            ['name' => 'create-charge', 'display_name' => 'Create Charge'],
            ['name' => 'edit-charge', 'display_name' => 'Edit Charge'],
            ['name' => 'view-charge', 'display_name' => 'View Charge'],
            ['name' => 'company', 'display_name' => 'Company'],
        ];

        \Illuminate\Support\Facades\DB::table('permissions')->insert( $role );
    }
}
