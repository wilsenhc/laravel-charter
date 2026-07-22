<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class McpController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Mcp', [
            'mcpUrl' => $request->root().'/mcp/charter',
        ]);
    }
}
