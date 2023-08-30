<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\Products\ProductCategoryResource;
use App\Models\Products\ProductCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    //Get all product informations
    public function index(Request $request)
    {
        $query = ProductCategory::query();

        // Apply filters based on request parameters
        if ($request->has('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        } else {
            $query->where('status', 1);
        }

        if ($request->has('name')) {
            $query->where(
                'name',
                'ilike',
                '%' . $request->input('name') . '%'
            );
        }

        if ($request->has('description')) {
            $query->where(
                'description',
                'ilike',
                '%' . $request->input('desc') . '%'
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

        $categories = $query->get();

        $categoryList = ProductCategoryResource::collection($categories);

        return $this->core->setResponse(
            'success',
            'Product Category Found',
            $categoryList
        );
    }

    //Create new product information
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

        $status = 1;

        try {
            DB::beginTransaction();

            // Check Auth & update user uuid to deleted_by
            if (Auth::check()) {
                $user = Auth::user();
            }

            $categories = $request->all();
            foreach ($categories as $category) {
                if (isset($category['status'])) {
                    $status = $category['status'];
                }

                $newCategory = [
                    'uuid' => Str::uuid()->toString(),
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'status' => $status,
                    // 'created_by' => $user->uuid,
                ];

                $newCategoryAdd = new ProductCategory($newCategory);
                $newCategoryAdd->save();

                $newCategories[] = $newCategoryAdd->uuid;
            }

            $categoryList = ProductCategory::whereIn(
                'uuid',
                $newCategories
            )->get();

            $categoryList = ProductCategoryResource::collection($categoryList);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Product Category fail to created.',
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Product Category created',
            $categoryList,
            false,
            201
        );
    }

    //Get product category information by ids
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

        $status = $request->input('status', 1);

        $category = ProductCategory::where([
            'uuid' => $uuid,
            'status' => $status
        ])->get();

        if (!isset($category)) {
            return $this->core->setResponse(
                'error',
                'Product Category Not Found',
                NULL,
                FALSE,
                400
            );
        }

        $categoryList = ProductCategoryResource::collection($category);

        return $this->core->setResponse(
            'success',
            'Product Category Found',
            $categoryList
        );
    }

    //UpdateBulk product category information
    public function updateBulk(Request $request)
    {
        $categories = $request->all();

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

        $status = 1;

        try {
            DB::beginTransaction();

            // Check Auth & update user uuid to deleted_by
            if (Auth::check()) {
                $user = Auth::user();
            }

            foreach ($categories as $categoryData) {
                if (isset($categoryData['status'])) {
                    $status = $categoryData['status'];
                }

                $category = ProductCategory::lockForUpdate()
                    ->where(
                        'uuid',
                        $categoryData['uuid']
                    )->firstOrFail();
                $category->update([
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'status' => $status,
                    // 'updated_by' => $user->uuid,
                ]);

                $updatedCategories[] = $category->toArray();
            }

            $categoryList = ProductCategory::whereIn(
                'uuid',
                array_column($updatedCategories, 'uuid')
            )->get();

            $categoryList = ProductCategoryResource::collection($categoryList);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Product Category fail to updated.',
                NULL,
                FALSE,
                500
            );
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                "Product Category fail to updated.",
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Product Category updated',
            $categoryList
        );
    }

    //Delete product information by ids
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
        $categories = null;
        try {
            $categories = ProductCategory::lockForUpdate()->whereIn('uuid', $uuids);

            // Compare the count of found UUIDs with the count from the request array
            if (
                !$categories ||
                (count($categories->get()) !== count($uuids))
            ) {
                return response()->json(
                    ['message' => 'Product Categories fail to deleted, because invalid uuid(s)'],
                    400
                );
            }

            //Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // $categories->deleted_by = $user->uuid;
            // $categories->save();
            // }

            $categories->delete();

        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Error during bulk deletion ' . $e->getMessage()],
                500
            );
        }

        return $this->core->setResponse(
            'success',
            "Product Categories deleted",
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
                    // 'uuids.*' => 'required|exists:product_categories,uuid',
                ];

                break;

            case 'create' || 'update':

                $validator = [
                    '*.name' => 'required|string|max:255|min:2',
                    '*.description' => 'required|max:140|min:5',
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
