<?php

namespace App\Services\Calculations\Bonuses;

use app\Libraries\Core;
use App\Models\Calculations\Bonuses\Period;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PeriodService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  public function generateWeekPeriods($request)
  {
    $userUuid = null;

    if (Auth::check()) {
      $user = Auth::user();
      $userUuid = $user->uuid;
    }

    $validator = $this->validation($request, 'create');

    if ($validator->fails()) {
      return $this->core->setResponse(
        'error',
        $validator->messages()->first(),
        null,
        false,
        422
      );
    }

    $date = Carbon::parse($request->year . '-01-01');

    if ($request->days_interval % 7 !== 0) {
      return $this->core->setResponse(
        'error',
        'Interval should be 7/14/21/28',
        null,
        false,
        422
      );
    }

    for ($i = 0; $i < 60; $i++) {
      $sDate = $date->weekday($request->start_week_day);
      $startDate = $sDate->toDateString();
      $eDate = $sDate->addWeeks($request->days_interval / 7)->weekday($request->end_week_day);

      Period::firstOrCreate(
        [
          'start_date' => $startDate,
          'start_day_name' => Carbon::parse($startDate)->dayName,
          'end_date' => $eDate->toDateString(),
          'end_day_name' => Carbon::parse($eDate->toDate())->dayName,
          'interval_days' => $request->days_interval,
          'name' => 'bonus'
        ]
      );
    }

    return $this->core->setResponse(
      'success',
      'Period created.',
      null,
      false,
      201
    );
  }

  public function getActivePeriod($date = null)
  {
    $period = Period::query();
    $period = $date ? $period->where('start_date', '<=', $date)
      ->where('end_date', '>=', $date)->first() : $period->get();

    return $this->core->setResponse(
      'success',
      'Period active.',
      $period,
      false,
      201
    );
  }

  private function validation($request, $type = null)
  {
    switch ($type) {
      case 'delete':
        $validator = [
          'uuids' => 'required|array',
          'uuids.*' => 'required',
        ];
        break;
      case 'create' || 'update':
        $validator = [
          'year' => 'required|numeric',
          'days_interval' => 'required|in:7,14,21,28',
          'start_week_day' => 'required|in:0,1,2,3,4,5,6',
          'end_week_day' => 'required|in:0,1,2,3,4,5,6',
        ];
        break;
      default:
        $validator = [];
    }

    return Validator::make($request->all(), $validator);
  }
}
