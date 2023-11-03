<?php

namespace App\Services\Products;

use App\Http\Resources\Products\ProductAttributeResource;
use app\Libraries\Core;
use App\Models\Products\ProductAttribute;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductAttributeService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  //Get all product attribute informations
  public function index(Request $request)
  {
    $query = ProductAttribute::query();

    // Apply filters based on request parameters
    if ($request->has('status')) {
      $query->where(
        'status',
        $request->input('status')
      );
    } else {
      $query->where('status', "1");
    }

    if ($request->has('product_uuid')) {
      $query->where(
        'product_uuid',
        $request->input('product_uuid')
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

    $productAttributes = $query->get();

    $productAttributeList = ProductAttributeResource::collection($productAttributes);

    return $this->core->setResponse(
      'success',
      'Product Attribute Founded',
      $productAttributeList
    );
  }

  //Create new product attribute information
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

      $productAttributes = $request->all();
      foreach ($productAttributes as $productAttribute) {
        if (isset($productAttribute['status'])) {
          $status = $productAttribute['status'];
        }

        $newAttribute = [
          'uuid' => Str::uuid(),
          'product_uuid' => $productAttribute['product_uuid'],
          'name' => $productAttribute['name'],
          'description' => $productAttribute['description'],
          'status' => $status,
          // 'created_by' => $user->uuid,
        ];

        $newProductAttributeAdd = new ProductAttribute($newAttribute);
        $newProductAttributeAdd->save();

        $newProductAttributes[] = $newProductAttributeAdd->uuid;
      }

      $productAttributeList = ProductAttribute::whereIn(
        'uuid',
        $newProductAttributes
      )->get();

      $productAttributeList = ProductAttributeResource::collection(
        $productAttributeList
      );

      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        'Product Attribute fail to created. ' . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    }

    return $this->core->setResponse(
      'success',
      'Product Attribute created',
      $productAttributeList,
      false,
      201
    );
  }

  //Get product attribute information by ids
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

    $productAttribute = ProductAttribute::where([
      'uuid' => $uuid,
      'status' => $status
    ])->get();

    if (!isset($productAttribute)) {
      return $this->core->setResponse(
        'error',
        'Product Attribute Not Founded',
        NULL,
        FALSE,
        400
      );
    }

    $productAttributeList = ProductAttributeResource::collection($productAttribute);

    return $this->core->setResponse(
      'success',
      'Product Attribute Founded',
      $productAttributeList
    );
  }

  //UpdateBulk product attribute information
  public function updateBulk(Request $request)
  {
    $productAttributes = $request->all();

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

      foreach ($productAttributes as $productAttributeData) {
        if (isset($productAttributeData['status'])) {
          $status = $productAttributeData['status'];
        }

        $productAttribute = ProductAttribute::lockForUpdate()
          ->where('uuid', $productAttributeData['uuid'])
          ->firstOrFail();

        $productAttribute->update([
          'name' => $productAttributeData['name'],
          'product_uuid' => $productAttribute['product_uuid'],
          'description' => $productAttributeData['description'],
          'status' => $status,
          // 'updated_by' => $user->uuid,
        ]);

        $updatedProductAttributes[] = $productAttribute->toArray();
      }

      $productAttributeList = ProductAttribute::whereIn(
        'uuid',
        array_column($updatedProductAttributes, 'uuid')
      )->get();

      $productAttributeList = ProductAttributeResource::collection(
        $productAttributeList
      );

      DB::commit();
    } catch (QueryException $e) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        'Product Attribute fail to updated. ' . $e->getMessage(),
        NULL,
        FALSE,
        500
      );
    } catch (\Exception $ex) {
      DB::rollback();
      return $this->core->setResponse(
        'error',
        "Product Attribute fail to updated. " . $ex->getMessage(),
        NULL,
        FALSE,
        500
      );
    }

    return $this->core->setResponse(
      'success',
      'Product Attribute updated',
      $productAttributeList
    );
  }

  //Delete product attribute information by ids
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
    $productAttributes = null;
    try {
      $productAttributes = ProductAttribute::lockForUpdate()
        ->whereIn(
          'uuid',
          $uuids
        );

      // Compare the count of found UUIDs with the count from the request array
      if (
        !$productAttributes ||
        (count($productAttributes->get()) !== count($uuids))
      ) {
        return response()->json(
          ['message' => 'Product Attributes fail to deleted, because invalid uuid(s)'],
          400
        );
      }

      //Check Auth & update user uuid to deleted_by
      // if (Auth::check()) {
      //     $user = Auth::user();
      // $productAttributes->deleted_by = $user->uuid;
      // $productAttributes->save();
      // }

      $productAttributes->delete();
    } catch (\Exception $e) {
      return response()->json(
        ['message' => 'Error during bulk deletion ' . $e->getMessage()],
        500
      );
    }

    return $this->core->setResponse(
      'success',
      "Product Attributes deleted",
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
          // 'uuids.*' => 'required|exists:product_attributes,uuid',
        ];
        break;
      case 'create' || 'update':
        $validator = [
          '*.product_uuid' => 'required|uuid',
          '*.name' => 'required|string|max:255|min:2',
          '*.description' => 'required|max:140|min:3',
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
