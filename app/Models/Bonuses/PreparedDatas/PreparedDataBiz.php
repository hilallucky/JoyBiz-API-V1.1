<?php

namespace App\Models\Bonuses\PreparedDatas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreparedDataBiz extends Model
{
  use SoftDeletes;

  protected $table = 'prepared_data_bizs';
  protected $primaryKey = 'id';

  protected $casts = [
    'ppv' => 'integer',
    'pbv' => 'integer',
    'pro' => 'integer',
    'tpvj' => 'integer',
    'tbvj' => 'integer',
    'gpvj' => 'integer',
    'gbvj' => 'integer',
    'srank' => 'integer',
    'erank' => 'integer',
  ];

  protected $fillable = [
    'mid',
    'member_uuid', //jbid;
    'sponsor_uuid', //'spid',
    'placement_uuid', //'upid',
    'ppv',
    'pbv',
    'ppvb',
    'pbvb',
    'gpvb',
    'gbvb',
    'gpvb_under_100',
    'gbvb_under_100',
    'srank',
    'srank_uuid',
    'erank',
    'erank_uuid'
  ];

  public function user()
  {
    return $this->hasOne(User::class, 'uuid', 'member_uuid');
  }

  public function membership()
  {
    return $this->hasOne(Membership::class, 'uuid', 'member_uuid');
  }

  public function effectiveRank()
  {
    return $this->hasMany(ERank::class, 'member_uuid', 'member_uuid');
  }

  public function sRank()
  {
    return $this->hasMany(SRank::class, 'member_uuid', 'member_uuid');
  }
}
