<?php

use Illuminate\Database\Seeder;

class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('product_categories')->delete();

        $categories = array(
            array('name' => 'Televisions'),
            array('name' => 'Bank & Others'),
            array('name' => 'Online Ticket'),
            array('name' => 'Exclusive Product'),
            array('name' => 'Bulk Product'),
            array('name' => 'Other Products'),
        );

        \Illuminate\Support\Facades\DB::table('product_categories')->insert($categories);

        $categoryId = \App\ProductCategory::lists('id')->toArray();

        $categories = array(
            array('name' => '21 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '22 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '23 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '24 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '27 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '32 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '40 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '42 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '48 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '52 Inch Televisions', 'parent_category_id' => $categoryId[0]),
            array('name' => '65 Inch Televisions', 'parent_category_id' => $categoryId[0]),

            array('name' => 'Without specific customer signature', 'parent_category_id' => $categoryId[1]),
            array('name' => 'With specific customer signature', 'parent_category_id' => $categoryId[1]),

            array('name' => 'Without specific customer signature', 'parent_category_id' => $categoryId[2]),
            array('name' => 'With specific customer signature', 'parent_category_id' => $categoryId[2]),

            array('name' => 'Mobile', 'parent_category_id' => $categoryId[3]),
            array('name' => 'Tab', 'parent_category_id' => $categoryId[3]),
            array('name' => 'PS4', 'parent_category_id' => $categoryId[3]),
            array('name' => 'Laptop', 'parent_category_id' => $categoryId[3]),
            array('name' => 'Monitor', 'parent_category_id' => $categoryId[3]),
        );

        \Illuminate\Support\Facades\DB::table('product_categories')->insert($categories);
    }
}
