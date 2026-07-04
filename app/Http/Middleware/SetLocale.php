<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('locale', Locale::default()->value);

        if (in_array($locale, Locale::codes(), true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
