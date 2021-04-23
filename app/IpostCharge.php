<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * Description of IpostCharge
 *
 * @author johnny
 */
class IpostCharge extends Model {
    protected $table = 'ipost_charges';
    protected $guarded = ['id'];
    
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    
    public function approvedBy()
    {
        return $this->belongsTo('App\User', 'approved_by', 'id');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id', 'id');
    }

    public function product_category()
    {
        return $this->belongsTo('App\ProductCategory', 'product_category_id', 'id');
    }
}
