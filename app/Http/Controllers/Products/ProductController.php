<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Products\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Nonstandard\Uuid;

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

        if ($request->has('product_category_uuid')) {
            $query->where('product_category_uuid', $request->input('product_category_uuid'));
        }

        if ($request->has('created_at')) {
            $dateRange = explode(',', $request->input('created_at'));
            if (count($dateRange) === 2) {
                $query->whereBetween('created_at', $dateRange);
            }
        }

        $products = $query->get();

        $productList = ProductResource::collection($products);

        return $this->core->setResponse('success', 'Product Found', $productList);
    }

    public function store(Request $request)
    {
        $validator = $this->validation('create', $request);

        if ($validator->fails()) {
            return $this->core->setResponse('error', $validator->messages()->first(), NULL, false, 400);
        }

        $status = 1;

        try {
            DB::beginTransaction();

            // Check Auth & update user uuid to deleted_by
            if (Auth::check()) {
                $user = Auth::user();
            }

            $products = $request->all();
            foreach ($products as $product) {

                $newProduct = [
                    'uuid' => Str::uuid()->toString(),
                    'product_category_uuid' => $product['product_category_uuid'],
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'status' => $status,
                    // 'created_by' => $user->uuid,
                ];

                $newProductAdd = new Product($newProduct);
                $newProductAdd->save();

                $newProducts[] = $newProduct;
            }

            $productList = Product::with('category')->whereIn('uuid', array_column($newProducts, 'uuid'))->get();

            $productList = ProductResource::collection($productList);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse('error', 'Product fail to created.' . $e->getMessage(), NULL, FALSE, 500);
        }

        return $this->core->setResponse('success', 'Product created', $productList, false, 201);

    }

    //Get product information by ids
    public function show(Request $request, $uuid)
    {
        if (!Str::isUuid($uuid)) {
            return $this->core->setResponse('error', 'Invalid UUID format', NULL, FALSE, 400);
        }

        $status = $request->input('status', 1);

        $product = Product::with('category')->where(['uuid' => $uuid, 'status' => $status])->get();

        if (!isset($product)) {
            return $this->core->setResponse('error', 'Product Not Founded', NULL, FALSE, 200);
        }

        $productList = ProductResource::collection($product);

        return $this->core->setResponse('success', 'Product Founded', $productList);
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

            // Check Auth & update user uuid to deleted_by
            if (Auth::check()) {
                $user = Auth::user();
            }

            $error_info = null;

            foreach ($products as $productData) {
                if (isset($productData['status'])) {
                    $status = $productData['status'];
                }

                $error_info = 'Product uuid = ' . $productData['uuid'] . ' doesn\'t exist';

                $product = Product::lockForUpdate()->where('uuid', $productData['uuid'])->firstOrFail();

                $product->update([
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'status' => $status,
                    'product_category_uuid' => $productData['product_category_uuid'],
                    // 'updated_by' => $user->uuid,
                ]);

                $updatedProducts[] = $product->toArray();
            }

            $productList = Product::with('category')->whereIn('uuid', array_column($updatedProducts, 'uuid'))->get();

            $productList = ProductResource::collection($productList);


            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return $this->core->setResponse('error', "Product fail to updated. $error_info" . $e->getMessage(), NULL, FALSE, 500);
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->core->setResponse('error', "Product fail to updated. $error_info" . $ex->getMessage(), NULL, FALSE, 500);
        }

        return $this->core->setResponse('success', 'Product updated', $productList);
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
                return $this->core->setResponse('error', "Product fail to deleted, because invalid uuid(s).", NULL, FALSE, 400);
            }

            //Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // $products->deleted_by = $user->uuid;
            // $products->save();
            // }

            $products->delete();

        } catch (\Exception $e) {
            return $this->core->setResponse('error', "Error during bulk deletion " . $e->getMessage(), NULL, FALSE, 500);
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
                    '*.product_category_uuid' => 'required|uuid',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
