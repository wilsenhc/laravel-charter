<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';

const { t } = useI18n();

const locale = computed(() => usePage().props.locale as string);
const origin = typeof window !== 'undefined' ? window.location.origin : '';

const props = defineProps<{
    terms: { slug: string; category: string; title: string; summary: string }[];
}>();

const categoryOrder: Record<string, number> = {
    'Concept': 0,
    'Service': 1,
    'Database': 2,
    'Starter Kit': 3,
    'Testing': 4,
    'Auth': 5,
    'Runtime': 6,
};

const grouped = computed(() => {
    const groups: Record<string, { slug: string; category: string }[]> = {};

    for (const term of props.terms) {
        if (!groups[term.category]) {
            groups[term.category] = [];
        }
        groups[term.category].push(term);
    }

    return Object.entries(groups)
        .sort(([a], [b]) => (categoryOrder[a] ?? 99) - (categoryOrder[b] ?? 99))
        .map(([category, items]) => ({ category, items }));
});
</script>

<template>
    <Head>
        <title>{{ t('glossary.page_title') }} — {{ $t('header.app_name') }}</title>
        <meta name="description" :content="t('glossary.meta_description')">
        <link rel="canonical" :href="`${origin}/${locale}/glossary`">
    </Head>
    <AppHeader />
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            {{ t('glossary.page_title') }}
        </h1>
        <p class="mb-8 text-sm text-muted-foreground">
            {{ t('glossary.meta_description') }}
        </p>

        <div v-for="group in grouped" :key="group.category" class="mb-10">
            <h2 class="mb-4 text-lg font-semibold">
                {{ group.category }}
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="term in group.items"
                    :key="term.slug"
                    :href="`/${locale}/glossary/${term.slug}`"
                    class="rounded-lg border border-border bg-card p-4 transition-colors hover:bg-accent"
                >
                    <h3 class="font-medium">{{ term.title }}</h3>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ term.summary }}
                    </p>
                </Link>
            </div>
        </div>
    </main>
    <AppFooter />
</template>
