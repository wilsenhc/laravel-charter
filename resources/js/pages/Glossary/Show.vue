<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
import { buttonVariants } from '@/components/ui/button';

const { t } = useI18n();

const locale = computed(() => usePage().props.locale as string);
const origin = typeof window !== 'undefined' ? window.location.origin : '';

defineProps<{
    term: string;
    entry: {
        category: string;
        builder_params: Record<string, string[] | string>;
        translations: {
            title: string;
            question: string;
            summary: string;
            definition: string;
            sail_integration: string;
        };
    };
    related: { slug: string; title: string; summary: string }[];
}>();
</script>

<template>
    <Head>
        <title>{{ entry.translations.question }} — {{ $t('header.app_name') }}</title>
        <meta name="description" :content="entry.translations.summary">
        <link rel="canonical" :href="`${origin}/${locale}/glossary/${term}`">
    </Head>
    <AppHeader />
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <div class="flex flex-col items-start">
            <Link
                :href="`/${locale}/glossary`"
                prefetch
                class="mb-6 inline-flex text-sm text-muted-foreground underline underline-offset-4 transition-colors hover:text-foreground"
            >
                &larr; {{ $t('nav.back_to_glossary') }}
            </Link>

            <span class="mb-2 inline-block rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">
                {{ entry.category }}
            </span>
        </div>

        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            {{ entry.translations.question }}
        </h1>
        <p class="mb-6 text-sm leading-relaxed text-muted-foreground">
            {{ entry.translations.summary }}
        </p>

        <div class="mb-8 space-y-4 text-sm leading-relaxed text-foreground/80">
            <p>{{ entry.translations.definition }}</p>
            <p>{{ entry.translations.sail_integration }}</p>
        </div>

        <div class="mb-8 rounded-lg border border-border bg-card p-6">
            <h2 class="mb-2 text-base font-semibold">
                {{ t('glossary.cta_title') }}
            </h2>
            <p class="mb-4 text-sm text-muted-foreground">
                {{ t('glossary.cta_description') }}
            </p>
            <Link prefetch :href="`/${locale}/application`" :class="buttonVariants()">
                {{ t('glossary.cta_button') }}
            </Link>
        </div>

        <div v-if="related.length > 0">
            <h2 class="mb-4 text-base font-semibold">
                {{ t('glossary.related_title') }}
            </h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <Link
                    v-for="term in related"
                    :key="term.slug"
                    :href="`/${locale}/glossary/${term.slug}`"
                    prefetch
                    class="rounded-lg border border-border bg-card p-3 transition-colors hover:bg-accent"
                >
                    <h3 class="text-sm font-medium">{{ term.title }}</h3>
                    <p class="mt-0.5 text-xs text-muted-foreground">
                        {{ term.summary }}
                    </p>
                </Link>
            </div>
        </div>
    </main>
    <AppFooter />
</template>
