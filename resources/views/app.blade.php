<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @head

        @if (config('services.webmcp.origin_trial_token'))
            <meta http-equiv="origin-trial" content="{{ config('services.webmcp.origin_trial_token') }}">
        @endif

        <script nonce="{{ request()->attributes->get('csp-nonce') }}">
            (function () {
                const stored = localStorage.getItem('vueuse-color-scheme') || 'auto';
                const isDark = stored === 'dark' ||
                    (stored === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', isDark);
            })();
        </script>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])

        <x-inertia::head />
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
