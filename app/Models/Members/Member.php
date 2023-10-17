<?php

namespace App\Models\Members;

use App\Models\Bonuses\BonusRank;
use App\Models\Bonuses\Ranks\EffectiveRank;
use App\Models\Bonuses\Ranks\SRank;
use App\Models\Calculations\Transactions\CalculationPointMember;
use App\Models\Orders\Production\OrderHeader;
use App\Models\Orders\Temporary\OrderHeaderTemp;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'members';
  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'uuid',
    'first_name',
    'last_name',
    'id_no',
    'phone',
    'placement_id',
    'placement_uuid',
    'sponsor_id',
    'sponsor_uuid',
    'user_uuid',
    'user_id',
    'country_uuid',
    'status',
    'will_dormant_at',
    'min_bv',
    'activated_at',
    'remarks',
    'created_by',
    'updated_by',
    'deleted_by',
    'position',
  ];
  protected $hidden = [
    'password',
    // 'created_at',
    // 'updated_at'
  ];

  public function user()
  {
    return $this->belongsTo(User::class,  'user_uuid', 'uuid');
  }

  public function sponsor()
  {
    return $this->belongsTo(Member::class, 'sponsor_id');
  }

  public function placement()
  {
    return $this->belongsTo(Member::class, 'placement_id');
  }

  public function leftLegMembers()
  {
    return $this->hasMany(Member::class, 'sponsor_id', 'id')->where('position', 'left');
  }

  public function rightLegMembers()
  {
    return $this->hasMany(Member::class, 'sponsor_id', 'id')->where('position', 'right');
  }

  public function members()
  {
    return $this->hasMany(Member::class, 'sponsor_id');
  }

  public function address()
  {
    return $this->hasMany(MemberAddress::class, 'member_uuid');
  }

  function getUplineCode($uuid, $type)
  {
    $member = Member::where('uuid', $uuid)->first();

    if (!$member) {
      return null; // Member not found
    }

    // Initialize an empty array to store the upline codes
    $uplines = [];

    // Start from the member and traverse up the hierarchy until the root
    while ($member) {
      $uplines[] = $member;
      $member = Member::where(
        'uuid',
        $type == 'placement' ? $member->placement_uuid : $member->sponsor_uuid
      )
        ->first();
    }

    // Reverse the array to get the upline codes from bottom to top
    $uplines = array_reverse($uplines);

    return $uplines;
  }

  function checkNetwork($uuid1, $uuid2)
  {
    $member = Member::where('uuid', $uuid1)->first();

    if (!$member || $member->placement_uuid) {
      return false; // Member not found
    }

    if ($member->uuid == $uuid2) {
      return true;
    }

    // Start from the member and traverse up the hierarchy until the root
    while ($member) {
      $member = Member::where('uuid', $member->placement_uuid)->first();

      if (!$member || $member->placement_uuid) {
        return false; // Member not found
      }

      if ($member->uuid == $uuid2) {
        return true;
      }
    }
  }

  function transactionTemps()
  {
    return $this->hasMany(OrderHeaderTemp::class, 'member_uuid', 'uuid');
  }

  function transactions()
  {
    return $this->hasMany(OrderHeader::class, 'member_uuid', 'uuid');
  }

  function transaction_sumaries($start, $end)
  {
    DB::enableQueryLog();

    $data = OrderHeader::select(
      'member_uuid',
      DB::raw('SUM(total_discount_value) as total_discount_value'),
      DB::raw('SUM(total_discount_value_amount) as total_discount_value_amount'),
      DB::raw('SUM(total_price_after_discount) as total_price_after_discount'),
      DB::raw('SUM(total_amount) as total_amount'),
      DB::raw('SUM(total_shipping_charge) as total_shipping_charge'),
      DB::raw('SUM(total_payment_charge) as total_payment_charge'),
      DB::raw('SUM(total_amount_summary) as total_amount_summary'),
      DB::raw('SUM(total_pv) as total_pv'),
      DB::raw('SUM(total_xv) as total_xv'),
      DB::raw('SUM(total_bv) as total_bv'),
      DB::raw('SUM(total_rv) as total_rv'),
    )
      ->whereBetween(
        DB::raw('created_at::date'),
        [$start, $end]
      )
      ->groupBy('member_uuid')
      ->get();

    // $query = DB::getQueryLog();
    // dd($query);

    return $data;
  }



  function transaction_sumary_by_memberuuid($memberUuid, $start, $end)
  {
    DB::enableQueryLog();

    $data = OrderHeader::select(
      'member_uuid',
      DB::raw('SUM(total_discount_value) as total_discount_value'),
      DB::raw('SUM(total_discount_value_amount) as total_discount_value_amount'),
      DB::raw('SUM(total_price_after_discount) as total_price_after_discount'),
      DB::raw('SUM(total_amount) as total_amount'),
      DB::raw('SUM(total_shipping_charge) as total_shipping_charge'),
      DB::raw('SUM(total_payment_charge) as total_payment_charge'),
      DB::raw('SUM(total_amount_summary) as total_amount_summary'),
      DB::raw('SUM(total_pv) as total_pv'),
      DB::raw('SUM(total_xv) as total_xv'),
      DB::raw('SUM(total_bv) as total_bv'),
      DB::raw('SUM(total_rv) as total_rv'),
    )
      ->whereBetween(
        DB::raw('created_at::date'),
        [$start, $end]
      )
      ->where('member_uuid', $memberUuid)
      ->groupBy('member_uuid')
      ->get();

    // $query = DB::getQueryLog();
    // dd($query);

    return $data;
  }


  public function calculateAccumulatedPoints($pv)
  {
    $accumulated_total_pv = $pv;

    foreach ($this->members as $member) {
      $accumulated_total_pv += $member->calculateAccumulatedPoints($pv);
    }

    return $accumulated_total_pv;
  }

  public static function getAccumulatedPoints($start, $end)
  {
    $results = self::with('members')->get();

    $formattedResults = [];

    $acc_pv = 0;

    foreach ($results as $result) {

      $pv = 0;
      $bv = 0;
      $xv = 0;
      $rv = 0;

      $dataPoint = $result->transaction_sumary_by_memberuuid(
        $result->uuid,
        $start,
        $end
      );

      foreach ($dataPoint as $dp) {
        $pv = $dp->total_pv;
        $bv = $dp->total_bv;
        $xv = $dp->total_xv;
        $rv = $dp->total_rv;
      }

      $formattedResults[] = [
        'uuid' => $result->uuid,
        'sponsor_uuid' => $result->sponsor_uuid,
        'pv' => $pv,
        'acc_pv' => $result->calculateAccumulatedPoints($pv),
      ];
    }

    return $formattedResults;
  }


  public function effectiveRank()
  {
    return $this->hasMany(BonusRank::class, 'member_uuid', 'uuid');
  }


  // ==========================================================================================================


  public function srank()
  {
    return $this->hasOne(SRank::class, 'member_uuid', 'uuid');
  }

  public function effective_rank()
  {
    return $this->hasMany(EffectiveRank::class, 'member_uuid', 'uuid');
  }

  public function erank()
  {
    return $this->hasOne(E_RECOVERABLE_ERRORRank::class, 'member_uuid', 'uuid');
  }

  public function vital_signs()
  {
    return $this->hasMany(VitalSign::class, 'member_uuid', 'uuid');
  }



  // ==========================================================================================================


}
