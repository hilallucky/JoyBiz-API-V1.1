<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class StockPeriod extends Model
{
  protected $table = 'stock_periods';
  protected $fillable = [
    'stock_period',
    'start_date',
    'start_day_name',
    'end_date',
    'end_day_name',
    'processed_count',
    'name',
    'interval_days'
  ];
  protected $hidden = ['created_at', 'updated_at'];
}
