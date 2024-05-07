<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        // Define the allowed origin(s) for your frontend application
        $allowedOrigins = [
            'http://localhost:3000',

        ];

        // Check if the request origin is allowed
        if (in_array($request->header('Origin'), $allowedOrigins)) {
            // Handle preflight OPTIONS request
            if ($request->isMethod('OPTIONS')) {
                return response('', 200)
                    ->header('Access-Control-Allow-Origin', $request->header('Origin'))
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }

            // Handle actual request
            return $next($request)
                ->header('Access-Control-Allow-Origin', $request->header('Origin'))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        // Origin not allowed, return error response
        return response()->json(['error' => 'Unauthorized request origin'], 403);
    }
}
