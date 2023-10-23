<?php

namespace App\Models\Bonuses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VitalSign extends Model
{
  use SoftDeletes;
  
  protected $guarded = [];

  protected $table = 'vital_signs';
  protected $primaryKey = 'id';
}
