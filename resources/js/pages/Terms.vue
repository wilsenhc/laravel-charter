<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppFooter from '@/components/AppFooter.vue';
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
            Terms of Service
        </h1>
        <p class="mb-8 text-sm text-muted-foreground">
            Last updated: July 2026
        </p>

        <section class="space-y-6 text-sm leading-relaxed text-foreground/80">
            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Acceptance of Terms
                </h2>
                <p>
                    By using Charter for Laravel ("the Service"), you agree to
                    these Terms of Service. If you do not agree, do not use
                    the Service.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Description of Service
                </h2>
                <p>
                    Charter for Laravel is a free, open-source tool that
                    provides a visual interface for generating Laravel Sail
                    installation commands. The Service is provided "as is"
                    without any warranty, express or implied.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Anonymous Usage Statistics
                </h2>
                <p>
                    The Service collects anonymous, non-personal usage
                    statistics when the build endpoint is used. This includes
                    selected configuration options only — no IP addresses,
                    user agents, application names, or other identifying
                    information is stored. For full details, see our
                    <Link
                        href="/privacy"
                        class="font-medium text-foreground underline underline-offset-4"
                    >Privacy Policy</Link>.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    No Affiliation
                </h2>
                <p>
                    Charter for Laravel is an independent, community-built
                    project. It is not affiliated with, endorsed by, or
                    sponsored by Laravel or Laravel Holdings Inc.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Limitation of Liability
                </h2>
                <p>
                    The Service is provided free of charge. In no event shall
                    the creators or contributors be liable for any damages
                    arising from the use or inability to use the Service,
                    including but not limited to data loss or system
                    configuration issues resulting from commands generated
                    by the Service.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    User Responsibility
                </h2>
                <p>
                    You are responsible for reviewing the generated command
                    before executing it in your terminal. The Service
                    generates standard <code>laravel new</code> commands, but
                    you should verify they match your intended configuration.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Open Source License
                </h2>
                <p>
                    The source code for Charter for Laravel is available on
                    <a
                        href="https://github.com/wilsenhc/laravel-charter"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-medium text-foreground underline underline-offset-4"
                    >GitHub</a>
                    and is released under the MIT License. You are free to
                    view, fork, and modify it in accordance with that license.
                </p>
            </div>

            <div>
                <h2 class="mb-2 font-semibold text-foreground">
                    Changes to Terms
                </h2>
                <p>
                    We reserve the right to update these terms at any time.
                    Continued use of the Service after changes constitutes
                    acceptance of the new terms.
                </p>
            </div>
        </section>

        <AppFooter />
    </main>
</template>
