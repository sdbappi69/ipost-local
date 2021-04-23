<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankTransactionDoc extends Model
{
    protected $table = 'bank_transection_docs';

    protected $guarded = ['id', 'created_at', 'updated_at'];


}
