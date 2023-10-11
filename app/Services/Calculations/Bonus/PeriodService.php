<?php

namespace App\Services\Calculations\Bonus;

use app\Libraries\Core;
use App\Models\Calculations\Bonus\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class PeriodService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function generate_week_periods(Request $request)
    {
        $validator = $this->validation(
            'create',
            $request
        );

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
                    'sDate' => $startDate,
                    'sDay_name' => Carbon::parse($startDate)->dayName,
                    'eDate' => $eDate->toDateString(),
                    'eDay_name' => Carbon::parse($eDate->toDate())->dayName,
                    'interval_days' => $request->days_interval
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

    private function validation($type = null, $request)
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
