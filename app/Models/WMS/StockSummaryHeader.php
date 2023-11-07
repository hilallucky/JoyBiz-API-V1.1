<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockSummaryHeader extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'wms_stock_summary_headers';

  protected $primaryKey = 'id';

  protected $casts = [
    'stock_date' => 'date',
    'is_register' => 'boolean',
    'weight' => 'float',
    'stock_in' => 'integer',
    'stock_out' => 'integer',
    'stock_previous' => 'integer',
    'stock_current' => 'integer',
    'stock_to_sale' => 'integer',
    'indent' => 'integer',
  ];

  protected $fillable = [
    'id',
    'uuid',
    'stock_process_uuid',
    'warehouse_uuid',
    'stock_date',
    'product_uuid',
    'product_attribute_uuid',
    'product_header_uuid',
    'name',
    'attribute_name',
    'description',
    'is_register',
    'weight',
    'stock_in',
    'stock_out',
    'stock_previous',
    'stock_current',
    'stock_to_sale',
    'indent',
    'stock_type',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  // public function process()
  // {
  //   return $this->hasOne(StockProcesses::class, 'stock_process_uuid', 'uuid');
  // }

  // public function detail()
  // {
  //   return $this->hasMany(StockSummaryDetail::class, 'stock_process_uuid', 'uuid');
  // }
}
