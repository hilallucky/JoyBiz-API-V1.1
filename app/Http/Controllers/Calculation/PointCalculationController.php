<?php

namespace App\Http\Controllers\Calculation;

use App\Http\Controllers\Controller;
use App\Models\Calculation\MLMData;
use App\Services\Calculation\PointCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointCalculationController extends Controller
{
    private PointCalculationService $calculationService;

    public function __construct(PointCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }
    public function calculationPoint()
    {
        return $this->calculationService->getMlmData();
    }

    public function getMlmData()
    {
        $results = DB::select(DB::raw('
            WITH RECURSIVE RecursivePoints AS (
              SELECT
                id,
                parent_id,
                points,
                points AS akumulasi_points
              FROM
                mlm_data
              WHERE
                parent_id IS NOT NULL

              UNION ALL

              SELECT
                m.id,
                m.parent_id,
                m.points,
                r.akumulasi_points AS akumulasi_points
              FROM
                mlm_data m
              JOIN
                RecursivePoints r ON m.id = r.parent_id
            )
            SELECT
              mlm_data.id,
              mlm_data.parent_id,
              mlm_data.points,
              SUM(COALESCE(rp.akumulasi_points, 0)) AS akumulasi_points
            FROM
              mlm_data
            LEFT JOIN
              RecursivePoints rp ON mlm_data.id = rp.id
            GROUP BY 
                mlm_data.id,
                mlm_data.parent_id,
                mlm_data.points
            ORDER BY
              mlm_data.id;
        '));

        return response()->json($results);
    }

    public function getMlmData_V2()
    {
        $results = MlmData::getMlmDataWithAccumulatedPoints();

        return response()->json($results);
    }
}
