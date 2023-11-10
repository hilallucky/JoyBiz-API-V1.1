<?php

namespace App\Services\WMS;

use app\Libraries\Core;
use App\Models\WMS\DODetail;
use App\Models\WMS\DOHeader;
use App\Models\WMS\GetTransaction;
use App\Repositories\WMS\DORepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DOService
{
  private DORepository $doRepository;
  public $core;

  public function __construct(DORepository $doRepository)
  {
    $this->core = new Core();
    $this->doRepository = $doRepository;
  }

  //Get DOs
  public function index(Request $request)
  {
    DB::enableQueryLog();
    $query = new DOHeader;

    if ($request->input('start') && $request->input('end')) {
      $start = $request->input('start');
      $end = $request->input('end');

      $query = $query->whereBetween(DB::raw('do_date::date'), [$start, $end]);
    }

    if ($request->input('do_no')) {
      $query->where('uuid', $request->input('uuid'));
    }

    $query = $query->orderBy('do_date', 'asc')->get();

    return $this->core->setResponse('success', 'Get DOs', $query);
  }

  // Create new DO
  public function store($request)
  {
    $validator = $this->validation($request, 'create');

    if ($validator->fails()) {
      return $this->core->setResponse(
        'error',
        $validator->messages()->first(),
        null,
        false,
        422
      );
    }

    return $this->doRepository->createDO($request->start, $request->end);
  }

  private function validation($request, $type = null)
  {
    switch ($type) {
      case 'delete':
        $validator = [
          'uuids' => 'required|array',
          'uuids.*' => 'required|uuid',
        ];
        break;
      case 'create' || 'update':
        $validator = [
          'start' => 'required|date_format:Y-m-d',
          'end' => 'required|date_format:Y-m-d',
        ];
        break;
      default:
        $validator = [];
    }

    return Validator::make($request->all(), $validator);
  }
}
