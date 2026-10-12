<?php

use function Pest\Laravel\get;

it('redirects to the application page', function () {
    get('/')
        ->assertRedirect('/en/application');
});

it('redirects the locale root to the localized application page', function () {
    get('/en')
        ->assertRedirect('/en/application');

    get('/es')
        ->assertRedirect('/es/application');
});
