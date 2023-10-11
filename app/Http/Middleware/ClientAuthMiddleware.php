<?php

namespace App\Http\Middleware;

use App\Models\Configs\Client;
use app\Libraries\Core;
use Closure;
use Illuminate\Support\Facades\DB;

class ClientAuthMiddleware
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

        // DB::enableQueryLog();
        $core = new Core();

        if (
            !$client = Client::
                where(function ($query) use ($request) {
                    $query->where('ip', $request->ip())
                        ->orWhere('domain_name', parse_url($request->root())['host']);
                })
                ->where('status', '1')
                ->where('client_key', $request->headers->get('client_key'))
                ->first()
        ) {

            // $query = DB::getQueryLog();
            // dd($query);

            return response()->json([
                'error' => 'error',
                'error_info' => 'Unauthorized client connection.',
                'ip' => $request->ip(),
                'domain' => parse_url($request->root())['host'],
            ])->setStatusCode(401);
        }

        return $next($request);
    }
}
