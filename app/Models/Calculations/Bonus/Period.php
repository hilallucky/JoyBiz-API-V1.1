<?php

namespace App\Models\Calculations\Bonus;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $table = 'week_periods';
    protected $fillable = ['sDate', 'sDay_name', 'eDate', 'eDay_name', 'name', 'interval_days'];
    protected $hidden = ['created_at', 'updated_at'];
}
