<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockProcesses extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'wms_stock_processes';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'uuid',
    'processed_date',
    'processed_by_uuid',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  public function stockSumHeader()
  {
    return $this->hasMany(StockSummaryHeader::class, 'stock_process_uuid', 'uuid');
  }

  public function stockSumDetail()
  {
    return $this->hasMany(StockSummaryDetail::class, 'stock_process_uuid', 'uuid');
  }

  public function stockSumWeeklyHeader()
  {
    return $this->hasMany(StockSummaryWeeklyHeader::class, 'stock_process_uuid', 'uuid');
  }
  
  public function stockSumMonthlyHeader()
  {
    return $this->hasMany(StockSummaryMonthlyHeader::class, 'stock_process_uuid', 'uuid');
  }
  
  public function stockSumYearlyHeader()
  {
    return $this->hasMany(StockSummaryYearlyHeader::class, 'stock_process_uuid', 'uuid');
  }
}
