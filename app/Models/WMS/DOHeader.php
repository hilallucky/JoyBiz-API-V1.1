<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DOHeader extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'wms_do_headers';

  protected $primaryKey = 'id';

  protected $casts = [
    'do_date' => 'date',
    'total_weight' => 'float',
    'total_stock_in' => 'integer',
    'total_stock_out' => 'integer',
    'total_transaction' => 'integer',
    'total_qty_order' => 'integer',
    'total_qty_sent' => 'integer',
    'total_qty_indent' => 'integer',
    'total_qty_remain' => 'integer',
  ];

  protected $fillable = [
    'id',
    'uuid',
    'do_date',
    'warehouse_uuid',
    'sent_to',
    'to_uuid',
    'name',
    'remarks',
    'notes',
    'description',
    'total_weight',
    'total_stock_in',
    'total_stock_out',
    'total_transaction',
    'transaction_uuids',
    'total_qty_order',
    'total_qty_sent',
    'total_qty_indent',
    'total_qty_remain',
    'stock_type',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  public function details()
  {
    return $this->hasMany(DODetail::class, 'wms_do_header_uuid', 'uuid');
  }
}
