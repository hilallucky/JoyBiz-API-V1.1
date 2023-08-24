<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
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
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', 1);
        }

        if ($request->has('name')) {
            $query->where('name', 'ilike', '%' . $request->input('name') . '%');
        }

        if ($request->has('description')) {
            $query->where('description', 'ilike', '%' . $request->input('desc') . '%');
        }

        if ($request->has('created_at')) {
            $dateRange = explode(',', $request->input('created_at'));
            if (count($dateRange) === 2) {
                $query->whereBetween('created_at', $dateRange);
            }
        }

        $categories = $query->get();

        return $this->core->setResponse('success', 'Product Category Found', $categories);
    }

    //Create new product information
    public function store(Request $request)
    {
        $validator = $this->validation('create', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), NULL, false, 400);
        }

        $status = 1;

        $categories = $request->all();

        try {
            DB::beginTransaction();

            foreach ($categories as $categoryData) {
                if (isset($categoryData['status'])) {
                    $status = $categoryData['status'];
                }

                $categories = ProductCategory::create([
                    'uuid' => Str::uuid(),
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'status' => $status,
                    'created_by' => $categoryData['created_by'],
                ]);

                $newCategories[] = $categories->toArray();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse('error', 'Product Category fail to created.', NULL, FALSE, 500);
        }

        return $this->core->setResponse('success', 'Product Category created', $newCategories, false, 201);
    }

    //Get product information by ids
    public function show(Request $request, $uuid)
    {
        if (!Str::isUuid($uuid)) {
            return $this->core->setResponse('error', 'Invalid UUID format', NULL, FALSE, 400);
        }

        $status = $request->input('status', 1);

        if (!$category = ProductCategory::where(['uuid' => $uuid, 'status' => $status])->first()) {
            return $this->core->setResponse('error', 'Product Category Not Found', NULL, FALSE, 400);
        }

        return $this->core->setResponse('success', 'Product Category Found', $category);
    }

    //Update product information by ids
    public function update(Request $request, $uuid)
    {
        $category = ProductCategory::where('uuid', $uuid)->first();
        $category->update($request->all());
        return response()->json($category);
    }

    //UpdateBulk product category information
    public function updateBulk(Request $request)
    {
        $categories =  $request->all();

        $validator = $this->validation('create', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), NULL, false, 400);
        }

        $status = 1;

        try {
            DB::beginTransaction();

            foreach ($categories as $categoryData) {
                if (isset($categoryData['status'])) {
                    $status = $categoryData['status'];
                }

                $category = ProductCategory::where('uuid', $categoryData['uuid'])->firstOrFail();
                $category->update([
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'status' => $status,
                    'created_by' => $categoryData['created_by'],
                ]);


                $updatedCategories[] = $category->toArray();
            }

            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return $this->core->setResponse('error', 'Product Category fail to updated.', NULL, FALSE, 500);
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->core->setResponse('error', "Product Category fail to updated. $ex", NULL, FALSE, 500);
        }

        return $this->core->setResponse('success', 'Product Category updated', $updatedCategories);
    }

    //Delete product information by ids
    public function destroy($uuid)
    {
        ProductCategory::find($uuid)->delete();
        return response()->json(['message' => 'Category deleted']);
    }

    public function destroyBulk(Request $request)
    {
        print_r($request->all());
        return $request->all;
        // $validator = $this->validation('delete', $request);

        // if ($validator->fails()) {

        //     return $this->core->setResponse('error', $validator->messages()->first(), NULL, false, 400);
        // }

        // $uuids = $request->input('uuids');

        // try {
        //     $deletedCategories = ProductCategory::whereIn('uuid', $uuids)->delete();
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'Error during bulk deletion'], 500);
        // }

        // return $this->core->setResponse('success', 'Product Categories deleted', $deletedCategories);
    }


    private function validation($type = null, $request)
    {

        switch ($type) {

            case 'create' || 'update':

                $validator = [
                    '*.name' => 'required|string|max:255|min:2',
                    '*.description' => 'required|max:140|min:5',
                    '*.status' => 'in:1,2,3',
                    '*.created_by' => 'required|string|min:4',
                ];

                break;

            case 'delete':

                $validator = [
                    '*.uuids' => 'required|exists:product_categories,uuid',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
