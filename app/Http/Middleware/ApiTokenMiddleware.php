<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $expectedToken = config('services.api.token');
        $providedToken = $request->bearerToken();

        if (!$expectedToken) {
            return response()->json([
                'status' => false,
                'message' => 'API Token is not configured on the server.'
            ], 500);
        }

        if (!$providedToken || $providedToken !== $expectedToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Invalid or missing API token.'
            ], 401);
        }

        return $next($request);
    }
}
