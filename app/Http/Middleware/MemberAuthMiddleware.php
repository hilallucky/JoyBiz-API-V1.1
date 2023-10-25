<?php

namespace App\Http\Middleware;

use app\Libraries\Core;
use App\Models\Users\User;
use Closure;
use Illuminate\Support\Facades\DB;

class MemberAuthMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    DB::enableQueryLog();
    $core = new Core();
    $member = User::where([
      ['email', $request->email()],
      ['password', $request->password()],
      ['status', '1'],
    ])->first();

    $query = DB::getQueryLog();
    dd($query);


    if (!$member) {

      return response()->json([
        'error' => 'error',
        'error_info' => 'Unauthorized member.',
      ])->setStatusCode(401);
    }

    return $next($request);
  }
}
