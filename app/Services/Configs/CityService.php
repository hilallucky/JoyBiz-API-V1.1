<?php

namespace App\Services\Configs;

use App\Http\Resources\Configs\CityResource;
use app\Libraries\Core;
use App\Models\Configs\City;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CityService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    //Get all city
    public function index(Request $request)
    {
        // DB::enableQueryLog();

        $query = City::query();

        // Apply filters based on request parameters
        if ($request->has('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        } else {
            $query->where(
                'status',
                "1"
            );
        }

        if ($request->has('name')) {
            $param = $request->input('name');

            $query = $query->where(
                function ($q) use ($param) {
                    $q->orWhere(
                        'province',
                        'ilike',
                        '%' . $param . '%'
                    )->orWhere(
                        'city',
                        'ilike',
                        '%' . $param . '%'
                    )->orWhere(
                        'district',
                        'ilike',
                        '%' . $param . '%'
                    )->orWhere(
                        'village',
                        'ilike',
                        '%' . $param . '%'
                    );
                }
            );
        }

        $cities = $query->get()->take(10);

        // $query = DB::getQueryLog();
        // dd($query);

        $cityList = CityResource::collection($cities);

        return $this->core->setResponse(
            'success',
            'City Found',
            $cityList
        );
    }

    //Create new City
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
                null,
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

            $cities = $request->all();
            foreach ($cities as $city) {
                if (isset($city['status'])) {
                    $status = $city['status'];
                }

                $newCity = [
                    'uuid' => Str::uuid()->toString(),
                    // 'country_uuid' => $city['country_uuid'],
                    'area_code' => $city['area_code'],
                    'zip_code' => $city['zip_code'],
                    'province' => $city['province'],
                    'city' => $city['city'],
                    'district' => $city['district'],
                    'village' => $city['village'],
                    'latitude' => $city['latitude'],
                    'longitude' => $city['longitude'],
                    'elevation' => $city['elevation'],
                    'status' => $status,
                    // 'created_by' => $user->uuid,
                ];

                $newCityAdd = new City($newCity);
                $newCityAdd->save();

                $newCities[] = $newCityAdd->uuid;
            }

            $cityList = City::whereIn(
                'uuid',
                $newCities
            )->get();

            $cityList = CityResource::collection($cityList);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'City fail to created. ' . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'City created',
            $cityList,
            false,
            201
        );
    }

    //Get City by ids
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

        $city = City::where([
            'uuid' => $uuid,
            'status' => $status
        ])->get();

        if (!isset($city)) {
            return $this->core->setResponse(
                'error',
                'City Not Found',
                NULL,
                FALSE,
                400
            );
        }

        $cityList = CityResource::collection($city);

        return $this->core->setResponse(
            'success',
            'City Found',
            $cityList
        );
    }

    //UpdateBulk City
    public function updateBulk(Request $request)
    {
        $cities = $request->all();

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

            foreach ($cities as $cityData) {
                if (isset($cityData['status'])) {
                    $status = $cityData['status'];
                }

                $city = City::lockForUpdate()
                    ->where(
                        'uuid',
                        $cityData['uuid']
                    )->firstOrFail();

                $city->update([
                    // 'country_uuid' => $cityData['country_uuid'],
                    'area_code' => $cityData['area_code'],
                    'zip_code' => $cityData['zip_code'],
                    'province' => $cityData['province'],
                    'city' => $cityData['city'],
                    'district' => $cityData['district'],
                    'village' => $cityData['village'],
                    'latitude' => $cityData['latitude'],
                    'longitude' => $cityData['longitude'],
                    'elevation' => $cityData['elevation'],
                    'status' => $status,
                    // 'updated_by' => $user->uuid,
                ]);

                $updatedCountries[] = $city->toArray();
            }

            $cityList = City::whereIn(
                'uuid',
                array_column($updatedCountries, 'uuid')
            )->get();

            $cityList = CityResource::collection($cityList);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'City fail to updated. ' . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                "City fail to updated. " . $ex->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'City updated',
            $cityList
        );
    }

    //Delete City by ids
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
        $cities = null;
        try {
            $cities = City::lockForUpdate()
                ->whereIn(
                    'uuid',
                    $uuids
                );

            // Compare the count of found UUIDs with the count from the request array
            if (
                !$cities ||
                (count($cities->get()) !== count($uuids))
            ) {
                return response()->json(
                    ['message' => 'Cities fail to deleted, because invalid uuid(s)'],
                    400
                );
            }

            //Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // $cities->deleted_by = $user->uuid;
            // $cities->save();
            // }

            $cities->delete();
        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Error during bulk deletion ' . $e->getMessage()],
                500
            );
        }

        return $this->core->setResponse(
            'success',
            "Cities deleted",
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
                    // 'uuids.*' => 'required|exists:cities,uuid',
                ];

                break;

            case 'create' || 'update':

                $validator = [
                    '*.country_uuid' => 'string|max:255|min:2',
                    '*.area_code' => 'string|max:30|min:2',
                    '*.zip_code' => 'string|max:10|min:2',
                    '*.province' => 'required|string|max:100|min:2',
                    '*.city' => 'required|string|max:100|min:2',
                    '*.district' => 'required|string|max:100|min:2',
                    '*.village' => 'required|string|max:100|min:2',
                    '*.latitude' => 'string|max:100|min:2',
                    '*.longitude' => 'string|max:100|min:2',
                    '*.elevation' => 'string|max:100|min:2',
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
