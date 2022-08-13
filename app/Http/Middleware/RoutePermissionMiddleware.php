<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RoutePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // $permission = str($request->getMethod() . '.' . Route::getCurrentRoute()->getName())
        //   ->lower()
        //   ->toString();
        // var_dump($permission);//,$request->getPathInfo(), $request->getMethod());
        $permission = Route::getCurrentRoute()->getName();
        if (! auth()->user() || ! auth()->user()->can($permission)) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => [
                        'status_code' => 401,
                        'code' => 'INSUFFICIENT_PERMISSIONS',
                        'description' => 'You are not authorized to access this resource.',
                    ],
                ], 401);
            }

            abort(401, 'You are not authorized to access this resource.');
        }

        return $next($request);
    }
}
