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
        $localeFromUrl = $request->route('locale');

        if ($localeFromUrl && in_array($localeFromUrl, Locale::codes(), true)) {
            App::setLocale($localeFromUrl);
        } else {
            $locale = $request->cookie('locale', Locale::default()->value);

            if (in_array($locale, Locale::codes(), true)) {
                App::setLocale($locale);
            }
        }

        return $next($request);
    }
}
