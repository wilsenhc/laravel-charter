<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', implode('; ', [
            'camera=()',
            'display-capture=()',
            'fullscreen=(self)',
            'geolocation=()',
            'microphone=()',
            'usb=()',
        ]));

        return $response;
    }
}
