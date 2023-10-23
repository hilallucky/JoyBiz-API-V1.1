<?php

namespace App\Services\Configs;

use App\Http\Resources\Configs\CountryResource;
use app\Libraries\Core;
use App\Models\Configs\Country;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CountryService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    //Get all country
    public function index(Request $request)
    {
        DB::enableQueryLog();

        $query = Country::query();

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

        // if ($request->has('name')) {
        //     $query->where(
        //         'name',
        //         'ilike',
        //         '%' . $request->input('name') . '%'
        //     );
        // }

        if ($request->has('name')) {
            $param = $request->input('name');

            $query = $query->where(
                function ($q) use ($param) {
                    $q->orWhere(
                        'name',
                        'ilike',
                        '%' . $param . '%'
                    )->orWhere(
                            'region_name',
                            'ilike',
                            '%' . $param . '%'
                        )->orWhere(
                            'region_name',
                            'ilike',
                            '%' . $param . '%'
                        )->orWhere(
                            'intermediate_region_name',
                            'ilike',
                            '%' . $param . '%'
                        )->orWhere(
                            'capital_city',
                            'ilike',
                            '%' . $param . '%'
                        );
                }
            );
        }

        $countries = $query->get();

        // $query = DB::getQueryLog();
        // dd($query);

        $countryList = CountryResource::collection($countries);

        return $this->core->setResponse(
            'success',
            'Country Found',
            $countryList
        );
    }

    //Create new Country
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

            $countries = $request->all();
            foreach ($countries as $country) {
                if (isset($country['status'])) {
                    $status = $country['status'];
                }

                $newCountry = [
                    'uuid' => Str::uuid(),
                    // 'code' => $country['code'],
                    'name' => $country['name'],
                    'status' => $status,
                    // 'created_by' => $user->uuid,
                ];

                $newCountryAdd = new Country($newCountry);
                $newCountryAdd->save();

                $newCountries[] = $newCountryAdd->uuid;
            }

            $countryList = Country::whereIn(
                'uuid',
                $newCountries
            )->get();

            $countryList = CountryResource::collection($countryList);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Country fail to created. ' . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Country created',
            $countryList,
            false,
            201
        );
    }

    //Get Country by ids
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

        $country = Country::where([
            'uuid' => $uuid,
            'status' => $status
        ])->get();

        if (!isset($country)) {
            return $this->core->setResponse(
                'error',
                'Country Not Found',
                NULL,
                FALSE,
                400
            );
        }

        $countryList = CountryResource::collection($country);

        return $this->core->setResponse(
            'success',
            'Country Found',
            $countryList
        );
    }

    //UpdateBulk Country
    public function updateBulk(Request $request)
    {
        $countries = $request->all();

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

            foreach ($countries as $countryData) {
                if (isset($countryData['status'])) {
                    $status = $countryData['status'];
                }

                $country = Country::lockForUpdate()
                    ->where(
                        'uuid',
                        $countryData['uuid']
                    )->firstOrFail();

                $country->update([
                    'name' => $countryData['name'],
                    'status' => $status,
                    // 'updated_by' => $user->uuid,
                ]);

                $updatedCountries[] = $country->toArray();
            }

            $countryList = Country::whereIn(
                'uuid',
                array_column($updatedCountries, 'uuid')
            )->get();

            $countryList = CountryResource::collection($countryList);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Country fail to updated. ' . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                "Country fail to updated. " . $ex->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Country updated',
            $countryList
        );
    }

    //Delete Country by ids
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
        $countries = null;
        try {
            $countries = Country::lockForUpdate()
                ->whereIn(
                    'uuid',
                    $uuids
                );

            // Compare the count of found UUIDs with the count from the request array
            if (
                !$countries ||
                (count($countries->get()) !== count($uuids))
            ) {
                return response()->json(
                    ['message' => 'Countries fail to deleted, because invalid uuid(s)'],
                    400
                );
            }

            //Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // $countries->deleted_by = $user->uuid;
            // $countries->save();
            // }

            $countries->delete();

        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Error during bulk deletion ' . $e->getMessage()],
                500
            );
        }

        return $this->core->setResponse(
            'success',
            "Countries deleted",
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
                    // 'uuids.*' => 'required|exists:countries,uuid',
                ];

                break;

            case 'create' || 'update':

                $validator = [
                    // '*.code' => 'required|string|max:255|min:2',
                    '*.name' => 'required|string|max:255|min:2',
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
