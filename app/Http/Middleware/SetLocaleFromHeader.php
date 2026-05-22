<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('app.supported_locales'));
        $locale = $request->getPreferredLanguage($supported);

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
