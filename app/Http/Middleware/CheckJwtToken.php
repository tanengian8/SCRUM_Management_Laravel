<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckJwtToken
{
    public function handle($request, Closure $next)
    {
        // Check if the Authorization header exists
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Extract the Bearer token from the Authorization header
        $authorizationHeader = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authorizationHeader);

        // Check if the token is empty
        if (empty($token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Attempt to decode and verify the JWT token
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            // Token validation failed
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Continue with the request
        return $next($request);
    }
}
