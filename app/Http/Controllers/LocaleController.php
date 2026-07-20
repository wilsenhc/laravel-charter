<?php

namespace App\Http\Controllers;

use App\Enums\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:'.implode(',', Locale::codes())],
        ]);

        $redirect = redirect()->back()->withCookie(
            cookie('locale', $validated['locale'], 43200)
        );

        $referer = $request->header('Referer');

        if ($referer) {
            $refererPath = parse_url($referer, PHP_URL_PATH);
            $refererSegments = array_values(array_filter(explode('/', $refererPath)));

            if (count($refererSegments) > 0 && in_array($refererSegments[0], Locale::codes(), true)) {
                $refererSegments[0] = $validated['locale'];
                $redirect->setTargetUrl(url('/'.implode('/', $refererSegments)));
            }
        }

        return $redirect;
    }
}
