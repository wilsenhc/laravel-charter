<?php

use Inertia\Testing\AssertableInertia as Assert;

test('renders the mcp documentation page', function () {
    $this->get(route('mcp.index', ['locale' => 'en']))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Mcp')
                ->has('mcpUrl')
                ->where('mcpUrl', fn (string $url) => str_ends_with($url, '/mcp/charter')),
        );
});
