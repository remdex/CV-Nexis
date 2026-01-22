<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBearerToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $expected = config('services.api_bearer_token') ?: env('API_BEARER_TOKEN');
     
        if (empty($expected) || ! $token || ! hash_equals($expected, $token)) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
