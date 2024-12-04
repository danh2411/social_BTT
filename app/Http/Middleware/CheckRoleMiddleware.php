<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Authenticate user using the API guard
        $user = Auth::guard('api')->user();
        // If user is not authenticated or the role is not 1, deny access
        if (!$user || $user->role != 1) {
            return   throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'errors' => 'Unauthorized'
            ], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json; charset=UTF-8'], JSON_UNESCAPED_UNICODE));
        }

        // Allow request to proceed
        return $next($request);
    }
}
