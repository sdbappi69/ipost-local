<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DelivarySurvey extends Model
{
   protected $table = 'delivary_survey';

   protected $fillable = [
      'delivary_task_id',
      'qus_id',
      'qus_ans',
      'created_by',
      'updated_by'
   ];
}
