<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryVehicleType extends Model
{
    protected $table = 'product_category_vehicle_types';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }
}