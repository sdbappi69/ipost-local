<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of FastbazzarOrderUpdate
 *
 * @author johnny
 */
class FastbazzarOrderUpdate extends Model {

    protected $table = 'fastbazzar_order_updates';
    protected $guarded = ['id', 'created_at', 'updated_at'];

}
