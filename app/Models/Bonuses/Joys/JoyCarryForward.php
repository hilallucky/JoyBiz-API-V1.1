<?php

namespace App\Models\Bonuses\Joys;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoyCarryForward extends Model
{
  use SoftDeletes;

  protected $table = 'joy_carry_forwards';

  protected $primaryKey = 'id';

  protected $guarded = [];

  public function membership()
  {
    return $this->hasOne(Member::class, 'uuid', 'member_uuid');
  }
}
