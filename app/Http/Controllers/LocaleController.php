<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:en,es'],
        ]);

        return redirect()->back()->withCookie(
            cookie('locale', $validated['locale'], 43200)
        );
    }
}
