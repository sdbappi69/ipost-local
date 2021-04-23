<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      	// $this->call(UserTypesTableSeeder::class);
      	// $this->call(CountriesTableSeeder::class);
      	// $this->call(StatesTableSeeder::class);
      	// $this->call(CitiesAndZonesTableSeeder::class);
      	// $this->call(UsersTableSeeder::class);
      	// $this->call(SettingsTableSeeder::class);
      	// $this->call(VehicleTypeSeeder::class);
      	// $this->call(VehicleSeeder::class);
      	// $this->call(DriverTableSeeder::class);
      	// $this->call(CityGenresTableSeeder::class);
      	// $this->call(ChargeModelsTableSeeder::class);
      	// $this->call(ProductCategoriesTableSeeder::class);
      	// $this->call(ChargesTableSeeder::class);
      	// $this->call(ZoneGenresSeeder::class);
        // $this->call(StoreTypesTableSeeder::class);
      // $this->call(RoleSeeder::class);
      // $this->call(PermissionSeeder::class);
      $this->call(PickingTimeSeeder::class);
    }
}
