<?php

use function Pest\Laravel\get;

it('authorizes the inline theme script with a matching nonce', function () {
    app()->instance('env', 'production');

    $response = get('/en/application');

    preg_match("/'nonce-([0-9a-f]{32})'/", (string) $response->headers->get('Content-Security-Policy'), $header);
    preg_match('/nonce="([0-9a-f]{32})"/', $response->getContent(), $page);

    expect($header)->not->toBeEmpty()
        ->and($page)->not->toBeEmpty()
        ->and($page[1])->toBe($header[1]);
});
