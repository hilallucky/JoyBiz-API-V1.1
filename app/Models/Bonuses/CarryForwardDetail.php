<?php

namespace App\Models\Bonuses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarryForwardDetail extends Model
{
  use SoftDeletes;

  protected $table = 'carry_forward_details';
  protected $primaryKey = 'id';

  protected $casts = [
      'gpvj' => 'integer',
      'gbvj' => 'gbvj',
  ];

  protected $fillable = [
      'id',
      'uuid',
      'wid',
      'member_uuid',
      'gpvj',
      'gbvj',
  ];

  public function user()
  {
      return $this->hasOne(User::class, 'uuid', 'member_uuid');
  }

  public function member()
  {
      return $this->hasOne(Member::class, 'uuid', 'member_uuid');
  }
}
