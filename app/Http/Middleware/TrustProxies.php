<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class TrustProxies extends Middleware
{
    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_AWS_ELB;

    public function handle(Request $request, Closure $next): Response
    {
        $appUrl = config('app.url');
        if (str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        return parent::handle($request, $next);
    }
}