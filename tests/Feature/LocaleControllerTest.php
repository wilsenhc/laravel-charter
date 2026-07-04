<?php

use function Pest\Laravel\post;

describe('update', function () {
    test('sets the locale cookie to es', function () {
        post(route('locale.update'), ['locale' => 'es'])
            ->assertRedirect()
            ->assertCookie('locale', 'es');
    });

    test('sets the locale cookie to en', function () {
        post('/locale', ['locale' => 'en'])
            ->assertRedirect()
            ->assertCookie('locale', 'en');
    });

    test('rejects invalid locale', function () {
        post(route('locale.update'), ['locale' => 'fr'])
            ->assertSessionHasErrors('locale');
    });

    test('rejects empty locale', function () {
        post(route('locale.update'), ['locale' => ''])
            ->assertSessionHasErrors('locale');
    });
});
