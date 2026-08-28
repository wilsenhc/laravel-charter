<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $appUrl = config('app.url');

        if (str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'locale' => app()->getLocale(),
            'locales' => Locale::supported(),
        ];
    }

    /**
     * Force HTTPS URLs for Inertia page props.
     */
    public function urlResolver(): ?Closure
    {
        $appUrl = config('app.url');

        if (str_starts_with($appUrl, 'https://')) {
            return fn (Request $request) => str_replace('http://', 'https://', $request->fullUrl());
        }

        return null;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = parent::handle($request, $next);

        if (! $request->header('X-Inertia') && $request->isMethod('GET')) {
            $response->headers->set('Cache-Control', 'private, s-maxage=0, max-age=300');
        }

        return $response;
    }
}
