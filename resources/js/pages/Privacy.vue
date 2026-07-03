<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <main class="mx-auto max-w-2xl px-4 py-12">
        <Link
            href="/"
            class="mb-8 inline-flex text-sm text-muted-foreground underline underline-offset-4 transition-colors hover:text-foreground"
        >
            &larr; Back to Charter
        </Link>

        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            Privacy Policy
        </h1>
        <p class="mb-8 text-sm text-muted-foreground">
            Last updated: July 2025
        </p>

        <section class="space-y-6 text-sm leading-relaxed text-foreground/80">
            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Information We Collect
                </h2>
                <p>
                    Charter for Laravel does not collect, store, or transmit any
                    personal information. All configuration options you select
                    are processed entirely in your browser. No data is sent to
                    our servers.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    How We Use Information
                </h2>
                <p>
                    Since we don't collect any information, there is nothing
                    to use. The command generated from your selections is
                    displayed on screen and executed locally on your machine.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Cookies
                </h2>
                <p>
                    We do not use cookies for tracking or analytics. Your
                    appearance preference (light/dark mode) is stored locally
                    in your browser's localStorage and is never transmitted
                    to our servers.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Third-Party Services
                </h2>
                <p>
                    This site does not embed third-party analytics, tracking
                    scripts, or advertising networks. The only external
                    resources loaded are the application's own CSS and
                    JavaScript assets, and web fonts served from the
                    application's own build pipeline.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Changes to This Policy
                </h2>
                <p>
                    If this policy changes in the future, the "Last updated"
                    date at the top will be revised. Continued use of the
                    site after changes constitutes acceptance of the updated
                    policy.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Contact
                </h2>
                <p>
                    If you have questions about this privacy policy, please
                    open an issue on the
                    <a
                        href="https://github.com/wilsenhc/laravel-charter"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-medium text-foreground underline underline-offset-4"
                    >GitHub repository</a>.
                </p>
            </div>
        </section>
    </main>
</template>
