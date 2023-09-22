<?php

namespace App\Services\Calculation;

use app\Libraries\Core;
use App\Models\Calculation\MLMData;
use App\Models\Members\Member;

class PointCalculationService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function calculateRecursivePoints($mlmData)
    {
        $points = $mlmData->points;

        foreach ($mlmData->members as $child) {
            $points += $this->calculateRecursivePoints($child);
        }

        return $points;
    }

    //     public function getMlmData()
    //     {
    //         $results = Member::with('members')
    //             ->whereNull('sponsor_uuid')
    //             ->get();

    //         foreach ($results as $result) {
    //             $result->akumulasi_points = $this->calculateRecursivePoints($result);
    //         }

    //         return response()->json($results);
    //     }

    public function getMlmData()
    {
        $results = MLMData::select('id', 'parent_id', 'points')
            ->get();

        foreach ($results as $result) {
            $result->akumulasi_points = $result->calculateAccumulatedPoints();
        }

        return response()->json($results);
    }
}
