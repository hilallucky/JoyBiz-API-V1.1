<?php

namespace App\Services\Products;

use App\Http\Resources\Products\PriceCodeResource;
use app\Libraries\Core;
use App\Models\Products\PriceCode;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PriceCodeService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    //Get all product informations
    public function index(Request $request)
    {
        $query = PriceCode::query();

        // Apply filters based on request parameters
        if ($request->has('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        } else {
            $query->where(
                'status',
                1
            );
        }

        if ($request->has('code')) {
            $query->where(
                'code',
                'ilike',
                '%' . $request->input('code') . '%'
            );
        }

        if ($request->has('name')) {
            $param = $request->input('name');

            $query = $query->where(
                function ($q) use ($param) {
                    $q->orWhere(
                        'code',
                        'ilike',
                        '%' . $param . '%'
                    )->orWhere(
                            'name',
                            'ilike',
                            '%' . $param . '%'
                        )->orWhere(
                            'description',
                            'ilike',
                            '%' . $param . '%'
                        )->orWhere(
                            'remarks',
                            'ilike',
                            '%' . $param . '%'
                        );
                }
            );
        }



        if ($request->has('created_at')) {
            $dateRange = explode(',', $request->input('created_at'));
            if (count($dateRange) === 2) {
                $query->whereBetween(
                    'created_at',
                    $dateRange
                );
            }
        }

        $priceCodes = $query->get();

        $priceCodeList = PriceCodeResource::collection($priceCodes);

        return $this->core->setResponse(
            'success',
            'Price Code Found',
            $priceCodeList
        );
    }

    //Create new product price information
    public function store(Request $request)
    {
        $validator = $this->validation(
            'create',
            $request
        );

        if ($validator->fails()) {

            return $this->core->setResponse(
                'error',
                $validator->messages()->first(),
                NULL,
                false,
                422
            );
        }

        $status = "1";

        try {
            DB::beginTransaction();

            // Check Auth & update user uuid to deleted_by
            if (Auth::check()) {
                $user = Auth::user();
            }

            $priceCodes = $request->all();
            foreach ($priceCodes as $priceCode) {
                if (isset($priceCode['status'])) {
                    $status = $priceCode['status'];
                }

                $newPriceCode = [
                    'uuid' => Str::uuid()->toString(),
                    'code' => $priceCode['code'],
                    'name' => $priceCode['name'],
                    'description' => $priceCode['description'],
                    'status' => $status,
                    // 'created_by' => $user->uuid,
                ];

                $newPriceCodeAdd = new PriceCode($newPriceCode);
                $newPriceCodeAdd->save();

                $newPriceCodes[] = $newPriceCodeAdd->uuid;
            }

            $priceCodeList = PriceCode::whereIn(
                'uuid',
                $newPriceCodes
            )->get();

            $priceCodeList = PriceCodeResource::collection($priceCodeList);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Price Code fail to created. ' . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Price Code created',
            $priceCodeList,
            false,
            201
        );
    }

    //Get product price information by ids
    public function show(Request $request, $uuid)
    {
        if (!Str::isUuid($uuid)) {
            return $this->core->setResponse(
                'error',
                'Invalid UUID format',
                NULL,
                FALSE,
                400
            );
        }

        $status = $request->input('status', "1");

        $priceCode = PriceCode::where([
            'uuid' => $uuid,
            'status' => $status
        ])->get();

        if (!isset($priceCode)) {
            return $this->core->setResponse(
                'error',
                'Price Code Not Found',
                NULL,
                FALSE,
                400
            );
        }

        $priceCodeList = PriceCodeResource::collection($priceCode);

        return $this->core->setResponse(
            'success',
            'Price Code Found',
            $priceCodeList
        );
    }

    //UpdateBulk product price information
    public function updateBulk(Request $request)
    {
        $priceCodes = $request->all();

        $validator = $this->validation(
            'update',
            $request
        );

        if ($validator->fails()) {
            return $this->core->setResponse(
                'error',
                $validator->messages()->first(),
                NULL,
                false,
                422
            );
        }

        $status = "1";

        try {
            DB::beginTransaction();

            // Check Auth & update user uuid to deleted_by
            if (Auth::check()) {
                $user = Auth::user();
            }

            foreach ($priceCodes as $priceCodeData) {
                if (isset($priceCodeData['status'])) {
                    $status = $priceCodeData['status'];
                }

                $priceCode = PriceCode::lockForUpdate()
                    ->where(
                        'uuid',
                        $priceCodeData['uuid']
                    )->firstOrFail();

                $priceCode->update([
                    'name' => $priceCodeData['name'],
                    'description' => $priceCodeData['description'],
                    'status' => $status,
                    // 'updated_by' => $user->uuid,
                ]);

                $updatedPriceCodes[] = $priceCode->toArray();
            }

            $priceCodeList = PriceCode::whereIn(
                'uuid',
                array_column($updatedPriceCodes, 'uuid')
            )->get();

            $priceCodeList = PriceCodeResource::collection($priceCodeList);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Price Code fail to updated. ' . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                "Price Code fail to updated. " . $ex->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Price Code updated',
            $priceCodeList
        );
    }

    //Delete product price information by ids
    public function destroyBulk(Request $request)
    {

        $validator = $this->validation(
            'delete',
            $request
        );

        if ($validator->fails()) {
            return $this->core->setResponse(
                'error',
                $validator->messages()->first(),
                NULL,
                false,
                422
            );
        }

        $uuids = $request->input('uuids');
        $priceCodes = null;
        try {
            $priceCodes = PriceCode::lockForUpdate()
                ->whereIn(
                    'uuid',
                    $uuids
                );

            // Compare the count of found UUIDs with the count from the request array
            if (
                !$priceCodes ||
                (count($priceCodes->get()) !== count($uuids))
            ) {
                return response()->json(
                    ['message' => 'Price Codes fail to deleted, because invalid uuid(s)'],
                    400
                );
            }

            //Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // $priceCodes->deleted_by = $user->uuid;
            // $priceCodes->save();
            // }

            $priceCodes->delete();

        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Error during bulk deletion ' . $e->getMessage()],
                500
            );
        }

        return $this->core->setResponse(
            'success',
            "Price Codes deleted",
            null,
            200
        );
    }

    private function validation($type = null, $request)
    {

        switch ($type) {

            case 'delete':

                $validator = [
                    'uuids' => 'required|array',
                    'uuids.*' => 'required|uuid',
                    // 'uuids.*' => 'required|exists:price_codes,uuid',
                ];

                break;

            case 'create' || 'update':

                $validator = [
                    '*.code' => 'required|string|max:255|min:2',
                    '*.name' => 'required|string|max:255|min:2',
                    '*.description' => 'required|max:140|min:5',
                    '*.status' => 'in:0,1,2,3',
                    '*.remarks' => 'string|min:4',
                    // '*.created_by' => 'required|string|min:4',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
