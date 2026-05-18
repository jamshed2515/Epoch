<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfessionalMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'professional') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Professional access only.'], 403);
            }
            abort(403, 'Access denied. This area is for professionals only.');
        }

        return $next($request);
    }
}
