<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

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

        if ($request->has('product_products_uuid')) {
            $query->where('product_products_uuid', $request->input('product_products_uuid'));
        }

        if ($request->has('created_at')) {
            $dateRange = explode(',', $request->input('created_at'));
            if (count($dateRange) === 2) {
                $query->whereBetween('created_at', $dateRange);
            }
        }

        $products = $query->get();

        return $this->core->setResponse('success', 'Product Found', $products);
    }

    public function store(Request $request)
    {

        $validator = $this->validation('create', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), NULL, false, 400);
        }

        $status = 1;

        $products = $request->all();

        try {
            DB::beginTransaction();

            // // Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // }

            foreach ($products as $productsData) {
                if (isset($productsData['status'])) {
                    $status = $productsData['status'];
                }

                $products = Product::create([
                    'uuid' => Str::uuid(),
                    'name' => $productsData['name'],
                    'description' => $productsData['description'],
                    'status' => $status,
                    'product_products_uuid' => $productsData['product_products_uuid'],
                ]);

                $newProducts[] = $products->toArray();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse('error', 'Product fail to created.', NULL, FALSE, 500);
        }

        return $this->core->setResponse('success', 'Product created', $newProducts, false, 201);

    }

    //Get product information by ids
    public function show(Request $request, $uuid)
    {
        if (!Str::isUuid($uuid)) {
            return $this->core->setResponse('error', 'Invalid UUID format', NULL, FALSE, 400);
        }

        $status = $request->input('status', 1);

        if (!$product = Product::with('category')->where(['uuid' => $uuid, 'status' => $status])->firstOrFail()) {
            return $this->core->setResponse('error', 'Product Not Founded', NULL, FALSE, 400);
        }

        return $this->core->setResponse('success', 'Product Founded', $product);
    }


    public function update(Request $request, $uuid)
    {
        $product = Product::with('category')->where('uuid', $uuid)->firstOrFail();
        $product->update($request->all());
        return response()->json($product);
    }

    //UpdateBulk product information
    public function updateBulk(Request $request)
    {
        $products = $request->all();

        $validator = $this->validation('update', $request);

        if ($validator->fails()) {

            return $this->core->setResponse('error', $validator->messages()->first(), NULL, false, 400);
        }

        $status = 1;

        try {
            DB::beginTransaction();

            // // Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // }

            foreach ($products as $productData) {
                if (isset($productData['status'])) {
                    $status = $productData['status'];
                }

                $product = Product::with('category')->where('uuid', $productData['uuid'])->firstOrFail();
                $product->update([
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'status' => $status,
                    'product_products_uuid' => $productData['product_products_uuid'],
                    // 'updated_by' => $user->uuid,
                ]);

                $updatedProducts[] = $product->toArray();
            }

            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return $this->core->setResponse('error', 'Product fail to updated.', NULL, FALSE, 500);
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->core->setResponse('error', "Product fail to updated.", NULL, FALSE, 500);
        }

        return $this->core->setResponse('success', 'Product updated', $updatedProducts);
    }

    public function destroy($uuid)
    {
        Product::findOrFail($uuid)->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function destroyBulk(Request $request)
    {
        $validator = $this->validation('delete', $request);

        if ($validator->fails()) {
            return $this->core->setResponse('error', $validator->messages()->first(), NULL, false, 400);
        }

        $uuids = $request->input('uuids');
        $products = null;
        try {
            $products = Product::lockForUpdate()->whereIn('uuid', $uuids);

            // Compare the count of found UUIDs with the count from the request array
            if (!$products || (count($products->get()) !== count($uuids))) {
                return response()->json(['message' => 'Product fail to deleted, because invalid uuid(s)'], 400);
            }

            //Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // $products->deleted_by = $user->uuid;
            // $products->save();
            // }

            $products->delete();

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error during bulk deletion ' . $e->getMessage()], 500);
        }

        return $this->core->setResponse('success', "Product deleted", null, 200);
    }

    private function validation($type = null, $request)
    {

        switch ($type) {

            case 'delete':

                $validator = [
                    'uuids' => 'required|array',
                    'uuids.*' => 'required|uuid',
                    // 'uuids.*' => 'required|exists:product_products,uuid',
                ];

                break;

            case 'create' || 'update':

                $validator = [
                    '*.name' => 'required|string|max:255|min:2',
                    '*.description' => 'required|max:140|min:5',
                    '*.status' => 'in:1,2,3',
                    // '*.created_by' => 'required|string|min:4',
                    '*.product_products_uuid' => 'required|uuid',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
