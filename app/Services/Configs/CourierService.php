<?php

namespace App\Services\Configs;

use App\Http\Resources\Configs\CourierResource;
use app\Libraries\Core;
use App\Models\Configs\Courier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourierService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    //Get all courier
    public function index(Request $request)
    {
        $query = Courier::query();

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
                            'short_name',
                            'ilike',
                            '%' . $param . '%'
                        );
                }
            );
        }

        $couriers = $query->get();

        $courierList = CourierResource::collection($couriers);

        return $this->core->setResponse(
            'success',
            'Courier Found',
            $courierList
        );
    }

    //Create new Courier
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

            $couriers = $request->all();
            foreach ($couriers as $courier) {
                if (isset($courier['status'])) {
                    $status = $courier['status'];
                }

                $newCourier = [
                    'uuid' => Str::uuid()->toString(),
                    'code' => $courier['code'],
                    'name' => $courier['name'],
                    'short_name' => $courier['short_name'],
                    'status' => $status,
                    // 'created_by' => $user->uuid,
                ];

                $newCourierAdd = new Courier($newCourier);
                $newCourierAdd->save();

                $newCouriers[] = $newCourierAdd->uuid;
            }

            $courierList = Courier::whereIn(
                'uuid',
                $newCouriers
            )->get();

            $courierList = CourierResource::collection($courierList);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Courier fail to created. ' . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Courier created',
            $courierList,
            false,
            201
        );
    }

    //Get Courier by ids
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

        $courier = Courier::where([
            'uuid' => $uuid,
            'status' => $status
        ])->get();

        if (!isset($courier)) {
            return $this->core->setResponse(
                'error',
                'Courier Not Found',
                NULL,
                FALSE,
                400
            );
        }

        $courierList = CourierResource::collection($courier);

        return $this->core->setResponse(
            'success',
            'Courier Found',
            $courierList
        );
    }

    //UpdateBulk Courier
    public function updateBulk(Request $request)
    {
        $couriers = $request->all();

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

            foreach ($couriers as $courierData) {
                if (isset($courierData['status'])) {
                    $status = $courierData['status'];
                }

                $courier = Courier::lockForUpdate()
                    ->where(
                        'uuid',
                        $courierData['uuid']
                    )->firstOrFail();

                $courier->update([
                    'code' => $courierData['code'],
                    'name' => $courierData['name'],
                    'short_name' => $courierData['short_name'],
                    'status' => $status,
                    // 'updated_by' => $user->uuid,
                ]);

                $updatedCountries[] = $courier->toArray();
            }

            $courierList = Courier::whereIn(
                'uuid',
                array_column($updatedCountries, 'uuid')
            )->get();

            $courierList = CourierResource::collection($courierList);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Courier fail to updated. ' . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                "Courier fail to updated. " . $ex->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Courier updated',
            $courierList
        );
    }

    //Delete Courier by ids
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
        $couriers = null;
        try {
            $couriers = Courier::lockForUpdate()
                ->whereIn(
                    'uuid',
                    $uuids
                );

            // Compare the count of found UUIDs with the count from the request array
            if (
                !$couriers ||
                (count($couriers->get()) !== count($uuids))
            ) {
                return response()->json(
                    ['message' => 'Couriers fail to deleted, because invalid uuid(s)'],
                    400
                );
            }

            //Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // $couriers->deleted_by = $user->uuid;
            // $couriers->save();
            // }

            $couriers->delete();

        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Error during bulk deletion ' . $e->getMessage()],
                500
            );
        }

        return $this->core->setResponse(
            'success',
            "Couriers deleted",
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
                    // 'uuids.*' => 'required|exists:couriers,uuid',
                ];

                break;

            case 'create' || 'update':

                $validator = [
                    '*.code' => 'required|string|max:255|min:2',
                    '*.name' => 'required|string|max:255|min:2',
                    '*.short_name' => 'required|string|max:255|min:2',
                    '*.status' => 'in:0,1,2,3',
                    // '*.created_by' => 'required|string|min:4',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
