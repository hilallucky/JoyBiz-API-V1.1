<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MemberAuthController extends Controller
{

  protected $maxAttempts = 3;
  protected $decayMinutes = 2;

  public function __construct()
  {
        parent::__construct();

        $this->middleware('auth:member', [
          'except' => [
              'register',
              'verify',
              'login',
          ],

      ]);
  }

  // public function getLogin()
  // {
  //   return view('auth.admin.login');
  // }

  public function login(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email',
      'password' => 'required|min:5'
    ]);

    /* validation requirement */
    $validator = $this->validation(
      'login',
      request()
    );

    if ($validator->fails()) {

      return $this->core->setResponse(
        'error',
        $validator->messages()->first(),
        NULL,
        false,
        400
      );
    }

    $credentials = request(['email', 'password']);

    // return $credentials;

    if (!$member = Auth::guard('member')->attempt($credentials)) {

      return $this->core->setResponse(
        'error',
        'Please check your email or password !',
        NULL,
        false,
        400
      );
    }

    return $this->core->setResponse(
      'success',
      "Successfully Login;",
      $member
    );
  }

  // public function postLogout()
  // {
  //   auth()->guard('admin')->logout();
  //   session()->flush();

  //   return redirect()->route('admin.login');
  // }

  private function validation($type = null, $request)
  {

    switch ($type) {

      case 'registration':

        $validator = [
          'first_name' => 'required|max:50|min:2',
          'last_name' => 'required|max:100|min:2',
          'email' => 'required|email|unique:users',
          'password' => 'required|min:6|max:100',
        ];

        break;

      case 'login':

        $validator = [
          'email' => 'required|string',
          'password' => 'required|string',
        ];

        break;

      default:

        $validator = [];
    }

    return Validator::make($request->all(), $validator);
  }
}
