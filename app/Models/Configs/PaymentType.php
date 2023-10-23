<?php

namespace App\Models\Configs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{

  use HasFactory, SoftDeletes;

  protected $table = 'payment_types';
  protected $primaryKey = 'id';

  protected $cast = [
    'charge_percent' => 'float',
    'charge_amount' => 'float',
    'is_voucher' => 'boolean',
  ];

  protected $fillable = [
    'uuid',
    'ref_uuid',
    'code',
    'name',
    'description',
    'charge_percent',
    'charge_amount',
    'effect',
    'status_web',
    'is_voucher',
    'status',
    'remarks',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  public $incrementing = false;
}
