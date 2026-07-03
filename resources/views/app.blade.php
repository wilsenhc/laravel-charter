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
            <title>Laravel Sail Command Builder — Spin Up Apps Visually | Charter for Laravel</title>
            <link rel="canonical" href="{{ url()->current() }}">
            <meta name="description" content="Charter for Laravel helps you spin up a new Laravel app with Sail in one command. Visually pick services, starter kits, and options — no more memorizing CLI flags.">
            <meta property="og:title" content="Laravel Sail Command Builder — Spin Up Apps Visually | Charter for Laravel">
            <meta property="og:description" content="Spin up a Laravel app with Sail in one command. Pick your services and options visually, then copy a single CLI command.">
            <meta property="og:url" content="{{ url()->current() }}">
            <meta property="og:type" content="website">
            <meta property="og:image" content="{{ url('/social-preview.png') }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="Laravel Sail Command Builder — Spin Up Apps Visually | Charter for Laravel">
            <meta name="twitter:description" content="Spin up a Laravel app with Sail in one command. Pick your services and options visually, then copy a single CLI command.">

            <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"FAQPage","mainEntity":[{"@@type":"Question","name":"What is Charter for Laravel?","acceptedAnswer":{"@@type":"Answer","text":"Picking the right options when spinning up a new Laravel app can feel like a lot. Charter gives you a friendly UI where you can pick the services, starter kit, and tools you want, then hands you a ready-to-run command. No more memorizing flags or digging through docs."}},{"@@type":"Question","name":"How do I use the generated command?","acceptedAnswer":{"@@type":"Answer","text":"Just copy the command, paste it into your terminal, and hit enter. It downloads the Laravel installer and runs it with the options you chose. Wait a minute or two and you'll have a fresh project ready to go."}},{"@@type":"Question","name":"What is a Starter Kit?","acceptedAnswer":{"@@type":"Answer","text":"Starter kits give your new app a head start with auth, a frontend setup, and common scaffolding out of the box. Laravel ships kits for Livewire, Vue, React, and Svelte, or you can point it at your own."}},{"@@type":"Question","name":"Can I use a custom starter kit?","acceptedAnswer":{"@@type":"Answer","text":"Absolutely. Pick 'custom' in the Starter Kit dropdown and paste in a URL. You can browse community kits at github.com/tnylea/laravel-new. Heads-up: since these are community-maintained, some may not be fully supported and installation can occasionally hit a snag."}},{"@@type":"Question","name":"What is Laravel Boost?","acceptedAnswer":{"@@type":"Answer","text":"Boost is Laravel's official MCP toolkit that helps you build faster with AI-powered helpers. Toggle it on and your new app comes with it ready to go."}},{"@@type":"Question","name":"What does the 'Teams' option do?","acceptedAnswer":{"@@type":"Answer","text":"It adds team membership and team-based data scoping to your app \u2014 handy if you're building something where users belong to teams or workspaces. It's available for Laravel starter kits with built-in authentication or WorkOS."}},{"@@type":"Question","name":"What is a Devcontainer?","acceptedAnswer":{"@@type":"Answer","text":"It generates a Devcontainer configuration so your app can run in a containerized dev environment like VS Code Dev Containers or GitHub Codespaces. Great if you want a consistent, reproducible setup across machines."}},{"@@type":"Question","name":"Is Charter for Laravel affiliated with Laravel?","acceptedAnswer":{"@@type":"Answer","text":"Nope. Charter is an independent, community-built project and isn't affiliated with, endorsed by, or sponsored by Laravel or Laravel Holdings Inc."}},{"@@type":"Question","name":"Is my data stored or sent anywhere?","acceptedAnswer":{"@@type":"Answer","text":"Not at all. Everything you configure here is generated in your browser \u2014 there's no server-side storage. When you're ready, the command is built entirely client-side and runs locally on your machine."}},{"@@type":"Question","name":"What is Laravel Sail?","acceptedAnswer":{"@@type":"Answer","text":"Laravel Sail is a lightweight command-line interface for managing Laravel's default Docker development environment. It provides a pre-configured Docker Compose setup with services like MySQL, PostgreSQL, Redis, Mailpit, and more \u2014 so you don't need to install PHP, a web server, or other software on your local machine."}},{"@@type":"Question","name":"What services does Charter support?","acceptedAnswer":{"@@type":"Answer","text":"Charter supports all Sail services: MySQL, PostgreSQL, MariaDB, Redis, Typesense, Meilisearch, MinIO, Mailpit, and Selenium. You can toggle each one on or off and the generated command will include only what you need."}},{"@@type":"Question","name":"Can I modify the generated command?","acceptedAnswer":{"@@type":"Answer","text":"Absolutely. The generated command is a standard laravel new invocation with Sail flags. You can edit it before running, or use it as a starting point and tweak the options to match your exact requirements."}}]}</script>

            <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"WebApplication","name":"Charter for Laravel","url":"https://laravelcharter.com","description":"Spin up a new Laravel app with Laravel Sail in one command. Visually pick services, starter kits, and options \u2014 no more memorizing CLI flags.","operatingSystem":"Linux, macOS, Windows","browserRequirements":"Requires a modern web browser","applicationCategory":"DeveloperApplication","offers":{"@@type":"Offer","price":"0","priceCurrency":"USD"}}</script>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
