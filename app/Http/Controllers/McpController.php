<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Laravel\Head\Facades\Head;
use Laravel\Head\Facades\Schema;

class McpController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $locale = app()->getLocale();

        Head::title(__('mcp.title'))
            ->description(__('mcp.meta_description'))
            ->canonical('/'.$locale.'/mcp');

        Head::schema(Schema::breadcrumbs()->items([
            __('meta.app_name') => url($locale),
            __('mcp.title') => url($locale.'/mcp'),
        ]));

        return Inertia::render('Mcp', [
            'mcpUrl' => config('app.url').'/mcp/charter',
        ]);
    }
}
