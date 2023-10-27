<?php

namespace App\Models\Calculations\Bonuses;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $table = 'week_periods';
    protected $fillable = [
        'start_date',
        'start_day_name',
        'end_date',
        'end_day_name',
        'name',
        'interval_days'
    ];
    protected $hidden = ['created_at', 'updated_at'];
}
