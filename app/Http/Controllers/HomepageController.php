<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class HomepageController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect('/application');
    }
}
