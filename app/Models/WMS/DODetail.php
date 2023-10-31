<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DODetail extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'wms_do_details';

  protected $primaryKey = 'id';

  protected $casts = [
    'do_date' => 'date',
    'total_weight' => 'float',
    'stock_in' => 'integer',
    'stock_out' => 'integer',
    'total_transaction' => 'integer',
    'total_qty' => 'integer',
    'total_qty_sent' => 'integer',
    'total_qty_indent' => 'integer',
    'total_qty_remain' => 'integer',
  ];

  protected $fillable = [
    'id',
    'uuid',
    'wms_do_header_uuid',
    'product_uuid',
    'product_attribute_uuid',
    'product_header_uuid',
    'name',
    'attribute_name',
    'description',
    'is_register',
    'product_status',
    'weight',
    'stock_type',
    'qty',
    'qty_sent',
    'qty_indent',
    'qty_remain',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  public function header()
  {
    return $this->hasOne(DOHeader::class, 'uuid', 'wms_do_header_uuid');
  }
}
