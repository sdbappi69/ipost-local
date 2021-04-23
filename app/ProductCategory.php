<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function parent_cat()
    {
        return $this->belongsTo('App\ProductCategory', 'parent_category_id', 'id');
    }

    public function sub_cats()
    {
        return $this->hasMany('App\ProductCategory', 'parent_category_id', 'id');
    }

    public function sub_cat_charge()
    {
        return $this->hasMany('App\Charge', 'product_category_id', 'id');
    }

    public function vehicles()
    {
        return $this->hasMany(ProductCategoryVehicleType::class, 'product_category_id');
    }
}
