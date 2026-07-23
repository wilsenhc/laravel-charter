<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

const { t } = useI18n();

const locale = computed(() => usePage().props.locale as string);
const origin = typeof window !== 'undefined' ? window.location.origin : '';
const mcpUrl = computed(() => usePage().props.mcpUrl as string);

const claudeConfig = computed(() => JSON.stringify({
    mcpServers: {
        'charter-for-laravel': {
            type: 'http',
            url: mcpUrl.value,
        },
    },
}, null, 4));

const cursorConfig = computed(() => JSON.stringify({
    mcpServers: {
        'charter-for-laravel': {
            type: 'http',
            url: mcpUrl.value,
        },
    },
}, null, 4));

const codexConfig = computed(() => JSON.stringify({
    mcpServers: {
        'charter-for-laravel': {
            type: 'http',
            url: mcpUrl.value,
        },
    },
}, null, 4));

const opencodeConfig = computed(() => JSON.stringify({
    mcp: {
        'charter-for-laravel': {
            type: 'http',
            url: mcpUrl.value,
            enabled: true,
        },
    },
}, null, 4));

const breadcrumbJsonLd = computed(() => JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
        { '@type': 'ListItem', position: 1, name: 'Charter for Laravel', item: origin },
        { '@type': 'ListItem', position: 2, name: 'MCP', item: `${origin}/${locale.value}/mcp` },
    ],
}));
</script>

<template>
    <Head>
        <title>{{ t('mcp.title') }} — {{ t('header.app_name') }}</title>
        <meta :content="t('mcp.meta_description')" name="description">
        <link rel="canonical" :href="`${origin}/${locale}/mcp`">
        <component :is="'script'" type="application/ld+json" v-text="breadcrumbJsonLd" />
    </Head>
    <AppHeader />
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <Link
            :href="`/${locale}/application`"
            prefetch
            class="mb-8 inline-flex text-sm text-muted-foreground underline underline-offset-4 transition-colors hover:text-foreground"
        >
            &larr; {{ t('nav.back_to_charter') }}
        </Link>

        <h1 class="mb-2 text-3xl font-bold tracking-tight">
            {{ t('mcp.title') }}
        </h1>
        <p class="mb-8 text-muted-foreground">
            {{ t('mcp.description') }}
        </p>

        <section class="mb-10">
            <h2 class="mb-3 text-xl font-semibold tracking-tight">{{ t('mcp.available_tools') }}</h2>
            <div class="space-y-4">
                <div class="rounded-lg border border-border p-4">
                    <h3 class="mb-1 font-semibold">build-application</h3>
                    <p class="mb-2 text-sm text-muted-foreground">
                        {{ t('mcp.tool_application_desc') }}
                    </p>
                    <pre class="mb-3 text-xs text-foreground font-mono">build-application --name my-app --services '["pgsql","redis"]' --frontend vue --php 8.5</pre>
                    <Link :href="`/${locale}/application`" class="text-xs text-muted-foreground underline underline-offset-4 hover:text-foreground">
                        {{ t('nav.build_application') }}
                    </Link>
                </div>
                <div class="rounded-lg border border-border p-4">
                    <h3 class="mb-1 font-semibold">build-package</h3>
                    <p class="mb-2 text-sm text-muted-foreground">
                        {{ t('mcp.tool_package_desc') }}
                    </p>
                    <pre class="mb-3 text-xs text-foreground font-mono">build-package --name my-package --features '["config","routes"]' --author_name "Your Name"</pre>
                    <Link :href="`/${locale}/package`" class="text-xs text-muted-foreground underline underline-offset-4 hover:text-foreground">
                        {{ t('nav.build_package') }}
                    </Link>
                </div>
            </div>
        </section>

        <section class="mb-10">
            <h2 class="mb-3 text-xl font-semibold tracking-tight">{{ t('mcp.configuration') }}</h2>
            <p class="mb-4 text-sm text-muted-foreground">
                {{ t('mcp.configuration_description') }}
            </p>

            <Tabs default-value="claude" class="w-full">
                <TabsList class="mb-4">
                    <TabsTrigger value="claude">{{ t('mcp.tab_claude') }}</TabsTrigger>
                    <TabsTrigger value="cursor">{{ t('mcp.tab_cursor') }}</TabsTrigger>
                    <TabsTrigger value="codex">{{ t('mcp.tab_codex') }}</TabsTrigger>
                    <TabsTrigger value="opencode">{{ t('mcp.tab_opencode') }}</TabsTrigger>
                </TabsList>

                <TabsContent value="claude">
                    <p class="mb-2 text-xs text-muted-foreground">{{ t('mcp.client_claude') }} <code class="rounded bg-muted px-1 py-0.5 font-mono text-xs">claude.json</code>:</p>
                    <pre class="overflow-x-auto rounded-lg border border-border bg-muted p-4 font-mono text-xs"><code>{{ claudeConfig }}</code></pre>
                </TabsContent>

                <TabsContent value="cursor">
                    <p class="mb-2 text-xs text-muted-foreground">{{ t('mcp.client_cursor') }}</p>
                    <pre class="overflow-x-auto rounded-lg border border-border bg-muted p-4 font-mono text-xs"><code>{{ cursorConfig }}</code></pre>
                </TabsContent>

                <TabsContent value="codex">
                    <p class="mb-2 text-xs text-muted-foreground">{{ t('mcp.client_codex') }}</p>
                    <pre class="overflow-x-auto rounded-lg border border-border bg-muted p-4 font-mono text-xs"><code>{{ codexConfig }}</code></pre>
                </TabsContent>

                <TabsContent value="opencode">
                    <p class="mb-2 text-xs text-muted-foreground">{{ t('mcp.client_opencode') }} <code class="rounded bg-muted px-1 py-0.5 font-mono text-xs">opencode.json</code>:</p>
                    <pre class="overflow-x-auto rounded-lg border border-border bg-muted p-4 font-mono text-xs"><code>{{ opencodeConfig }}</code></pre>
                </TabsContent>
            </Tabs>
        </section>

        <section>
            <h2 class="mb-3 text-xl font-semibold tracking-tight">{{ t('mcp.how_to_use') }}</h2>
            <p class="mb-4 text-sm text-muted-foreground">
                {{ t('mcp.how_to_use_prefix') }} <code class="rounded bg-muted px-1 py-0.5 font-mono text-xs">build-application</code> {{ t('mcp.how_to_use_or') }} <code class="rounded bg-muted px-1 py-0.5 font-mono text-xs">build-package</code> {{ t('mcp.how_to_use_suffix') }}
            </p>
        </section>
    </main>
    <AppFooter />
</template>
