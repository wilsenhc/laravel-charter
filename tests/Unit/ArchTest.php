<?php

arch('controllers have no protected or private methods')
    ->expect('App\Http\Controllers')
    ->not->toHaveProtectedMethods()
    ->not->toHavePrivateMethods();
