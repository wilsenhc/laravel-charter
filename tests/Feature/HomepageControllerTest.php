<?php

use function Pest\Laravel\get;

it('redirects to the application page', function () {
    get('/')
        ->assertRedirect('/application');
});
