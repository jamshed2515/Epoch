<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale', 'en'));
        app()->setLocale($locale);

        // Store timezone preference from cookie
        if ($request->cookie('timezone')) {
            session(['timezone' => $request->cookie('timezone')]);
        }

        return $next($request);
    }
}
