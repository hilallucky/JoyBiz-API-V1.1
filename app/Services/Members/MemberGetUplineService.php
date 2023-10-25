<?php

namespace App\Services\Members;

use app\Libraries\Core;
use App\Models\Members\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MemberGetUplineService
{
  public $core;

  public function __construct()
  {
    $this->core = new Core();
  }

  public function login($request)
  {
    $login = Auth::Attempt($request->all());
    if ($login) {
      $user = Auth::user();
      $user->api_token = Str::random(100);
      $user->save();
      // $user->makeVisible('api_token');

      return response()->json([
        'response_code' => 200,
        'message' => 'Login Berhasil',
        'conntent' => $user
      ]);
    } else {
      return response()->json([
        'response_code' => 404,
        'message' => 'Username atau Password Tidak Ditemukan!'
      ]);
    }
  }


  public function getUpline($uuid, $type = 'placement')
  {
    if (!in_array($type, ['placement', 'sponsor'])) {
      return $this->core->setResponse(
        'error',
        'Type only placement or sponsor.',
        NULL,
        FALSE,
        400
      );
    }

    // Create an instance of the MemberController
    $member = new Member();

    // Call the getUplineCode function and pass the $memberId
    $uplines = $member->getUplineCode($uuid, $type);

    return $this->core->setResponse(
      'success',
      "Get Upline By $type.",
      $uplines,
      null,
      200
    );
  }

  public function checkNetwork($request)
  {
    $placementUUID = $request->input('placement_uuid');
    $sponsorUUID = $request->input('sponsor_uuid');

    // Create an instance of the MemberController
    $member = new Member();

    // Call the getUplineCode function and pass the $memberId
    $checkNetwork = $member->checkNetwork($placementUUID, $sponsorUUID);

    if ($checkNetwork) {
      return $this->core->setResponse(
        'success',
        "The placement and sponsor are in the same network.",
        $checkNetwork,
        null,
        200
      );
    } else {
      return $this->core->setResponse(
        'success',
        "The placement and sponsor are NOT in the same network.",
        $checkNetwork,
        null,
        200
      );
    }
  }
}
