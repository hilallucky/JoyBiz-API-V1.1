<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberShippingAddress extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'member_shipping_addresses';
  protected $primaryKey = 'id';
  protected $fillable = [
    'uuid',
    'member_uuid',
    'receiver_name',
    'receiver_phone',
    'city_uuid',
    'zip_code',
    'province',
    'city',
    'district',
    'village',
    'details',
    'notes',
    'remarks',
    'status',
    'latitude',
    'longitude',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  public $incrementing = false;


  public function member()
  {
    return $this->belongsTo(Member::class, 'uuid', 'member_uuid');
  }
}
