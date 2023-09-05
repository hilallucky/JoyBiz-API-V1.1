<?php

namespace App\Services\Products;

use App\Http\Resources\Products\ProductResource;
use app\Libraries\Core;
use App\Models\Products\Product;
use App\Models\Products\ProductGroupComposition;
use App\Models\Products\ProductPrice;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Nonstandard\Uuid;

class ProductService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function index(Request $request)
    {
        DB::enableQueryLog();

        $query = Product::with([
            'category',
            'prices.priceCode',
            'composition_by_header.product_source',
            'images',
        ]);

        // Apply filters based on request parameters
        $status = "1";
        if ($request->has('status')) {
            $status = $request->input('status');
        }
        $query->where('status', $status);

        if ($request->has('is_product_group')) {
            $is_product_group = $request->input('is_product_group');

            $query->where(
                'is_product_group',
                $is_product_group
            );
        }

        if ($request->has('name')) {
            $param = $request->input('name');

            $query = $query->where(
                function ($q) use ($param) {
                    $q->orWhere(
                        'name',
                        'ilike',
                        '%' . $param . '%'
                    )->orWhere(
                            'description',
                            'ilike',
                            '%' . $param . '%'
                        );
                }
            );
        }

        if ($request->has('category_uuid')) {
            $query->where(
                'category_uuid',
                $request->input('category_uuid')
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

        $products = $query->get();

        $query = DB::getQueryLog();
        // dd($query);

        $productList = ProductResource::collection($products);

        return $this->core->setResponse(
            'success',
            'Product Found',
            $productList
        );
    }

    //Strore data product with prices and variants
    public function storeIncludePrices(Request $request)
    {
        $validator = $this->validation(
            'createWithPrices',
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
            DB::enableQueryLog();

            // Check Auth & update user uuid to deleted_by
            if (Auth::check()) {
                $user = Auth::user();
            }

            $products = $request->all();

            foreach ($products as $product) {
                // Product
                $newProduct = [
                    'uuid' => Str::uuid()->toString(),
                    'category_uuid' => $product['category_uuid'],
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'is_product_group' => $product['is_product_group'],
                    'status' => $status,
                    // 'created_by' => $user->uuid,
                ];

                $newProductAdd = new Product($newProduct);
                $newProductAdd->save();
                $newProducts[] = $newProduct;

                // Price
                $prices = $product['prices'];
                foreach ($prices as $price) {
                    $newPrice = [
                        'uuid' => Str::uuid()->toString(),
                        'product_uuid' => $newProduct['uuid'],
                        'price_code_uuid' => $price['price_code_uuid'],
                        'price' => $price['price'],
                        'discount_type' => $price['discount_type'],
                        'discount_value' => $price['discount_value'],
                        'discount_value_amount' => $price['discount_value_amount'],
                        'price_after_discount' => $price['price_after_discount'],
                        'pv' => $price['pv'],
                        'xv' => $price['xv'],
                        'bv' => $price['bv'],
                        'rv' => $price['rv'],
                        // 'created_by' => $user->uuid,
                    ];

                    $newPriceAdd = new ProductPrice($newPrice);
                    $newPriceAdd->save();
                    $newPrices[] = $newPrice;
                }

                // Product Group Composition
                if (
                    $product['is_product_group'] == 1 &&
                    empty($product['composition'])
                ) {
                    DB::rollback();
                    return $this->core->setResponse(
                        'error',
                        'Product composition cannot be empty while set to group.',
                        NULL,
                        FALSE,
                        400
                    );
                } else if (
                    $product['is_product_group'] === 1 &&
                    !empty($product['composition'])
                ) {
                    $compositions = $product['composition'];
                    foreach ($compositions as $composition) {
                        $newComposition = [
                            'uuid' => Str::uuid()->toString(),
                            'product_group_header_uuid' => $newProduct['uuid'],
                            'product_uuid' => $composition['product_uuid'],
                            'qty' => $composition['qty'],
                            'status' => $composition['status'],
                            // 'created_by' => $user->uuid,
                        ];

                        $newCompositionAdd = new ProductGroupComposition($newComposition);
                        $newCompositionAdd->save();

                        $newComposition[] = $newComposition;
                    }
                }
            }

            $productList = Product::with([
                'category',
                'prices.priceCode',
                'composition_by_header.product_source',
                'images',
            ])
                ->whereIn('uuid', array_column($newProducts, 'uuid'))
                ->get();

            $productList = ProductResource::collection($productList);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                'Product fail to created.' . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Product created',
            $productList,
            false,
            201
        );

    }

    //Get product information by ids
    public function show(
        $uuid,
        Request $request
    ) {
        DB::enableQueryLog();

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

        try {
            $product = Product::with([
                'category',
                'prices.priceCode',
                'composition_by_header.product_source',
                'images',
            ]);

            if ($request->has('is_product_group')) {
                $product->where(
                    'is_product_group',
                    $request->input('is_product_group')
                );
            }

            $product = $product->where([
                'uuid' => $uuid,
                'status' => $status,
            ])->get();


            // $query = DB::getQueryLog();
            // dd($query);
        } catch (\Exception $e) {
            return $this->core->setResponse(
                'error',
                "Error during bulk deletion " . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        if (!isset($product)) {
            return $this->core->setResponse(
                'error',
                'Product Not Founded',
                NULL,
                FALSE,
                200
            );
        }

        $productList = ProductResource::collection($product);

        return $this->core->setResponse(
            'success',
            'Product Founded',
            $productList
        );
    }

    //UpdateBulk product information
    public function updateBulk(Request $request)
    {
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
            DB::enableQueryLog();

            // Check Auth & update user uuid to deleted_by
            if (Auth::check()) {
                $user = Auth::user();
            }

            $error_info = null;

            $products = $request->all();
            foreach ($products as $productData) {

                if ($request->has('status')) {
                    $status = $productData['status'];
                }

                $error_info = 'Product uuid = ' . $productData['uuid'] . ' doesn\'t exist';

                // Update Product
                $product = Product::lockForUpdate()
                    ->where(
                        'uuid',
                        $productData['uuid']
                    )->firstOrFail();

                $product->update([
                    'category_uuid' => $productData['category_uuid'],
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'is_product_group' => $productData['is_product_group'],
                    'status' => $status,
                    // 'updated_by' => $user->uuid,
                ]);

                $updatedProducts[] = $product->toArray();


                // Price
                $prices = $productData['prices'];
                foreach ($prices as $price) {

                    $priceStatus = 1;
                    if ($request->has('prices.status')) {
                        $priceStatus = $price['status'];
                    }

                    $priceRemarks = null;
                    if ($request->has('prices.remarks')) {
                        $priceRemarks = $price['remarks'];
                    }

                    // Check data from table product price
                    $priceFromDB = ProductPrice::where([
                        'product_uuid' => $productData['uuid'],
                        'price_code_uuid' => $price['price_code_uuid']
                    ])->first();

                    // Set value for product price
                    $priceData = [
                        'product_uuid' => $productData['uuid'],
                        'price_code_uuid' => $price['price_code_uuid'],
                        'price' => $price['price'],
                        'discount_type' => $price['discount_type'],
                        'discount_value' => $price['discount_value'],
                        'discount_value_amount' => $price['discount_value_amount'],
                        'price_after_discount' => $price['price_after_discount'],
                        'pv' => $price['pv'],
                        'xv' => $price['xv'],
                        'bv' => $price['bv'],
                        'rv' => $price['rv'],
                        'status' => $priceStatus,
                        'remarks' => $priceRemarks,
                        // 'created_by' => $user->uuid,
                    ];

                    // Update value if record doesn't exit
                    if (!empty($priceFromDB)) {
                        $newPrice = $priceFromDB->update($priceData);
                    } else { // Add new record if doesn't exit
                        $priceData['uuid'] = Str::uuid()->toString();

                        $newPrice = ProductPrice::create($priceData);
                    }

                    $newPrices[] = $newPrice;
                }


                // Product Group Composition
                $compositions = $productData['composition'];
                if (
                    $productData['is_product_group'] == 1 &&
                    empty($productData['composition'])
                ) {
                    DB::rollback();
                    return $this->core->setResponse(
                        'error',
                        'Product composition cannot be empty while set to group.',
                        NULL,
                        FALSE,
                        400
                    );
                } else if (
                    $productData['is_product_group'] === 1 &&
                    !empty($productData['composition'])
                ) {
                    foreach ($compositions as $composition) {
                        $compositionStatus = 1;
                        if ($request->has('composition.status')) {
                            $compositionStatus = $composition['status'];
                        }

                        $compositionRemarks = null;
                        if ($request->has('composition.remarks')) {
                            $compositionRemarks = $composition['remarks'];
                        }


                        // Check data from table product price
                        $compositionFromDB = ProductGroupComposition::where([
                            'product_group_header_uuid' => $productData['uuid'],
                            'product_uuid' => $productData['uuid'],
                        ])->first();

                        $compositions = $product['composition'];
                        $compositionData = [
                            'product_group_header_uuid' => $productData['uuid'],
                            'product_uuid' => $composition['product_uuid'],
                            'qty' => $composition['qty'],
                            'status' => $compositionStatus,
                            'remarks' => $compositionRemarks,
                            // 'created_by' => $user->uuid,
                        ];

                        // Update value if record doesn't exit
                        if (!empty($compositionFromDB)) {
                            $newComposition = $compositionFromDB
                                ->update($compositionData)
                                ->where('uuid', $compositionData['uuid']);

                        } else { // Add new record if doesn't exit
                            $compositionData['uuid'] = Str::uuid()->toString();

                            $newComposition = ProductGroupComposition::create($compositionData);
                        }

                        $newCompositions[] = $newComposition;
                    }
                } else if (
                    $productData['is_product_group'] === 0
                ) {
                    // Delete in Product Composition if product not group
                    ProductGroupComposition::where([
                        'product_group_header_uuid' => $productData['uuid'],
                    ])->delete();
                }


            }

            $productList = Product::with([
                'category',
                'prices.priceCode',
                'composition_by_header.product_source',
                'images',
            ])
                ->whereIn(
                    'uuid',
                    array_column($updatedProducts, 'uuid')
                )
                ->get();

            $productList = ProductResource::collection($productList);


            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return $this->core->setResponse(
                'error'
                , "Product fail to updated. $error_info" . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->core->setResponse(
                'error',
                "Product fail to updated. $error_info" . $ex->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            'Product updated',
            $productList
        );
    }

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
        $products = null;
        try {
            $products = Product::lockForUpdate()
                ->whereIn(
                    'uuid',
                    $uuids
                );

            // Compare the count of found UUIDs with the count from the request array
            if (
                !$products ||
                (count($products->get()) !== count($uuids))
            ) {
                return $this->core->setResponse(
                    'error',
                    "Product fail to deleted, because invalid uuid(s).",
                    NULL,
                    FALSE,
                    400
                );
            }

            //Check Auth & update user uuid to deleted_by
            // if (Auth::check()) {
            //     $user = Auth::user();
            // $products->deleted_by = $user->uuid;
            // $products->save();
            // }

            $products->delete();

        } catch (\Exception $e) {
            return $this->core->setResponse(
                'error',
                "Error during bulk deletion " . $e->getMessage(),
                NULL,
                FALSE,
                500
            );
        }

        return $this->core->setResponse(
            'success',
            "Product deleted",
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
                    // 'uuids.*' => 'required|exists:product_products,uuid',
                ];

                break;

            case 'create' || 'update':

                $validator = [
                    '*.name' => 'required|string|max:255|min:2',
                    '*.description' => 'required|max:140|min:5',
                    '*.is_product_group' => 'in:0,1,2',
                    '*.composition' => '*.is_product_group' == 1
                    ? ['required', 'array', 'min:0']
                    : ['array'],
                    //'required|array',
                    'composition.product_uuid' => 'uuid',
                    'composition.qty' => 'numeric',
                    'composition.status' => 'integer|in:0,1,2,3',
                    '*.status' => 'in:0,1,2,3',
                    // '*.created_by' => 'required|string|min:4',
                    '*.category_uuid' => 'required|uuid',
                ];

                break;

            case 'createWithPrices':

                $validator = [
                    '*.name' => 'required|string|max:255|min:2',
                    '*.description' => 'required|max:140|min:5',
                    '*.is_product_group' => 'in:0,1,2',
                    '*.composition' => '*.is_product_group' == 1
                    ? ['required', 'array', 'min:0']
                    : ['array'],
                    //'required|array',
                    'composition.product_uuid' => 'uuid',
                    'composition.qty' => 'numeric',
                    'composition.status' => 'integer|in:0,1,2,3',
                    '*.status' => 'in:0,1,2,3',
                    // '*.created_by' => 'required|string|min:4',
                    '*.category_uuid' => 'required|uuid',
                    '*.prices' => 'required|array',
                    'prices.price_code_uuid' => 'required|uuid',
                    'prices.price' => 'required|numeric',
                    'prices.discount_type' => 'required|in:percentage,amount',
                    'prices.discount_value' => 'required|numeric',
                    'prices.discount_value_amount' => 'required|numeric',
                    'prices.price_after_discount' => 'required|numeric',
                    'prices.pv' => 'required|numeric',
                    'prices.xv' => 'required|numeric',
                    'prices.bv' => 'required|numeric',
                    'prices.rv' => 'required|numeric',
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make(
            $request->all(),
            $validator
        );
    }
}
