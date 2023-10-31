<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetTransaction extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'wms_get_transactions';

  protected $primaryKey = 'id';

  protected $casts = [
    'get_date' => 'date',
    'wms_do_date' => 'date',
    'transaction_date' => 'date',
    'is_register' => 'boolean',
    'weight' => 'float',
    'stock_in' => 'integer',
    'stock_out' => 'integer',
    'qty' => 'integer',
    'qty_indent' => 'integer',
    'indent' => 'integer',
  ];

  protected $fillable = [
    'id',
    'uuid',
    'get_date',
    'wms_do_header_uuid',
    'wms_do_date',
    'transaction_type',
    'transaction_date',
    'transaction_header_uuid',
    'transaction_detail_uuid',
    'warehouse_uuid',
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
    'qty',
    'qty_indent',
    'product_status',
    'stock_type',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  public function doHeader()
  {
    return $this->hasOne(DOHeader::class, 'wms_do_header_uuid', 'uuid');
  }

  public function warehouse()
  {
    return $this->hasOne(Warehouse::class, 'warehouse_uuid', 'uuid');
  }
}
