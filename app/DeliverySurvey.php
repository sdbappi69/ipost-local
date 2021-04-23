<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliverySurvey extends Model
{
   protected $table = 'delivery_survey';

   protected $fillable = [
      'delivery_task_id',
      'qus_id',
      'qus_ans',
      'created_by',
      'updated_by'
   ];
}
