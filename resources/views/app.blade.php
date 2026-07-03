<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <script>
            (function () {
                const stored = localStorage.getItem('vueuse-color-scheme') || 'auto';
                const isDark = stored === 'dark' ||
                    (stored === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', isDark);
            })();
        </script>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }} — Spin Up Laravel Apps with Sail in One Command</title>
            <link rel="canonical" href="{{ url()->current() }}">
            <meta name="description" content="Charter for Laravel helps you spin up a new Laravel app with Sail in one command. Visually pick services, starter kits, and options — no more memorizing CLI flags.">
            <meta property="og:title" content="{{ config('app.name', 'Laravel') }}">
            <meta property="og:description" content="Spin up a Laravel app with Sail in one command. Pick your services and options visually, then copy a single CLI command.">
            <meta property="og:url" content="{{ url()->current() }}">
            <meta property="og:type" content="website">
            <meta property="og:image" content="{{ url('/social-preview.png') }}">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="{{ config('app.name', 'Laravel') }}">
            <meta name="twitter:description" content="Spin up a Laravel app with Sail in one command. Pick your services and options visually, then copy a single CLI command.">
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
