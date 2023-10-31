<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'wms_warehouses';
  protected $primaryKey = 'id';
  protected $fillable = [
    'id',
    'uuid',
    'upline_uuid',
    'upline_id',
    'code',
    'name',
    'phone',
    'mobile_phone',
    'email',
    'province',
    'city',
    'district',
    'village',
    'zip_code',
    'details',
    'description',
    'notes',
    'remarks',
    'warehouse_type',
    'status',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  public function upline()
  {
    return $this->belongsTo(Warehouse::class, 'upline_uuid', 'uuid');
  }

  public function child()
  {
    return $this->hasMany(Warehouse::class, 'upline_uuid', 'uuid');
  }

  public function uplines($uuid)
  {
    $warehouse = Warehouse::where('uuid', $uuid)->first();

    if (!$warehouse) {
      return null; // warehouse not found
    }

    // Initialize an empty array to store the upline codes
    $uplines = [];

    // Start from the warehouse and traverse up the hierarchy until the root
    while ($warehouse) {
      $uplines[] = $warehouse;
      $warehouse = Warehouse::where('uuid', $warehouse->upline_uuid)->first();
    }

    // Reverse the array to get the upline codes from bottom to top
    $uplines = array_reverse($uplines);

    return $uplines;
  }
}
