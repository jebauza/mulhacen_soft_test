<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use App\Common\Responses\ApiResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            return ApiResponse::error(
                __('Unauthorized'),
                401,
                ['auth' => [__('Authentication token is invalid or expired')]]
            );
        }

        return $next($request);
    }
}
