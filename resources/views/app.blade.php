<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="index, follow">

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
        <meta name="description" content="{{ __('meta.description') }}">
        <meta property="og:title" content="{{ __('meta.og_title') }}">
        <meta property="og:description" content="{{ __('meta.og_description') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <link rel="icon" href="{{ url('/favicon.svg') }}" type="image/svg+xml">
        <meta property="og:image" content="{{ url('/social-preview.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ __('meta.twitter_title') }}">
        <meta name="twitter:description" content="{{ __('meta.twitter_description') }}">

        @php
            $segments = request()->segments();
            if (count($segments) > 0 && in_array($segments[0], App\Enums\Locale::codes())) {
                array_shift($segments);
            }
            $relativePath = implode('/', $segments) ?: 'application';
        @endphp
        <link rel="canonical" href="{{ url(app()->getLocale().'/'.$relativePath) }}">
        @foreach (App\Enums\Locale::cases() as $locale)
            <link rel="alternate" hreflang="{{ $locale->value }}" href="{{ url($locale->value.'/'.$relativePath) }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ url(App\Enums\Locale::default()->value.'/'.$relativePath) }}">

        <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"FAQPage","mainEntity":[{"@@type":"Question","name":{{ Illuminate\Support\Js::from(__('faq.0.question')) }},"acceptedAnswer":{"@@type":"Answer","text":{{ Illuminate\Support\Js::from(__('faq.0.answer')) }}}},{"@@type":"Question","name":{{ Illuminate\Support\Js::from(__('faq.1.question')) }},"acceptedAnswer":{"@@type":"Answer","text":{{ Illuminate\Support\Js::from(__('faq.1.answer')) }}}},{"@@type":"Question","name":{{ Illuminate\Support\Js::from(__('faq.2.question')) }},"acceptedAnswer":{"@@type":"Answer","text":{{ Illuminate\Support\Js::from(__('faq.2.answer')) }}}},{"@@type":"Question","name":{{ Illuminate\Support\Js::from(__('faq.3.question')) }},"acceptedAnswer":{"@@type":"Answer","text":{{ Illuminate\Support\Js::from(__('faq.3.answer')) }}}},{"@@type":"Question","name":{{ Illuminate\Support\Js::from(__('faq.4.question')) }},"acceptedAnswer":{"@@type":"Answer","text":{{ Illuminate\Support\Js::from(__('faq.4.answer')) }}}},{"@@type":"Question","name":{{ Illuminate\Support\Js::from(__('faq.5.question')) }},"acceptedAnswer":{"@@type":"Answer","text":{{ Illuminate\Support\Js::from(__('faq.5.answer')) }}}}]}</script>

        <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"WebApplication","name":{{ Illuminate\Support\Js::from(__('wa.name')) }},"url":"https://laravelcharter.com","description":{{ Illuminate\Support\Js::from(__('wa.description')) }},"operatingSystem":"Linux, macOS, Windows","browserRequirements":"Requires a modern web browser","applicationCategory":"DeveloperApplication","offers":{"@@type":"Offer","price":"0","priceCurrency":"USD"}}</script>

        <title>{{ __('meta.title') }}</title>

        <x-inertia::head />
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
