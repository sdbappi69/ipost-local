<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of PaymentType
 *
 * @author johnny
 */
class PaymentType extends Model {

    protected $table = 'payment_types';
    protected $guarded = ['id'];
    public $timestamps = false;

}
