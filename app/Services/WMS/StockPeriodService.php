<?php

namespace App\Services\WMS;

use app\Libraries\Core;
use App\Models\WMS\StockPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StockPeriodService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  public function generatePeriod($request)
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
    switch ($request->period) {
      case 'monthly':
        $iMin = 0;
        $iMax = 13;
        break;
      case 'yearly':
        $iMin = 0;
        $iMax = 1;
        break;
      default:
        $iMin = 0;
        $iMax = 60;
    }
    for ($i = $iMin; $i < $iMax; $i++) {
      switch ($request->period) {
        case 'monthly':
          $sDate = $date->startOfMonth()->addMonth()->firstOfMonth();
          $startDate = $sDate->toDateString();
          $eDate = $sDate->endOfMonth();
          $interval = $sDate->month($i)->daysInMonth;
          break;
        case 'yearly':
          $startDate = $date->toDateString();
          $eDate = $date->parse("last day of December $request->year");
          $interval = $eDate->diffInDays($startDate);
          break;
        case 'weekly':
          $i > 0 ? $date->addDay() : $date;
          $sDate = $date->weekday(0); //Sunday
          $startDate = $sDate->toDateString();
          $eDate = $sDate->weekday(6); // Saturday
          $interval = 7;
          break;
        default:
          return $this->core->setResponse(
            'error',
            'Invalid data',
            null,
            false,
            422
          );
      }

      StockPeriod::firstOrCreate(
        [
          'stock_period' => $request->period,
          'start_date' => $startDate,
          'start_day_name' => Carbon::parse($startDate)->dayName,
          'end_date' => $eDate->toDateString(),
          'end_day_name' => Carbon::parse($eDate->toDate())->dayName,
          'interval_days' => $interval,
          'name' => 'stock',
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


  public function getActivePeriod($date, $type)
  {
    $period = StockPeriod::where('start_date', '<=', $date)
      ->where('end_date', '>=', $date)
      ->where('stock_period', $type)->first();
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
          'period' => 'required|in:daily,weekly,monthly,yearly',
        ];

        break;

      default:

        $validator = [];
    }

    return Validator::make($request->all(), $validator);
  }
}
