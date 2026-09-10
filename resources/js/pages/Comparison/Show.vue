<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
import { buttonVariants } from '@/components/ui/button';

interface ComparisonShowPageProps {
    locale: string;
    comparison: string;
    entry: {
        first: string;
        second: string;
        first_title: string;
        second_title: string;
        category: string;
        translations: {
            page_title: string;
            meta_description: string;
            overview: { first: string; second: string };
            performance: { first: string; second: string };
            ease_of_use: { first: string; second: string };
            ecosystem: { first: string; second: string };
            learning_curve: { first: string; second: string };
            verdict: string;
        };
    };
    related: { slug: string; page_title: string; meta_description: string }[];
}

const { t } = useI18n();
defineProps<ComparisonShowPageProps>();

const aspects = [
    'overview',
    'performance',
    'ease_of_use',
    'ecosystem',
    'learning_curve',
] as const;

type Aspect = (typeof aspects)[number];
</script>

<template>
    <AppHeader />
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <div class="flex flex-col items-start">
            <Link
                :href="`/${locale}/glossary`"
                prefetch
                class="mb-6 inline-flex text-sm text-muted-foreground underline underline-offset-4 transition-colors hover:text-foreground"
            >
                &larr; {{ t('comparison.back_to_comparisons') }}
            </Link>

            <span
                class="mb-2 inline-block rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
            >
                {{ entry.category }}
            </span>
        </div>

        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            {{ entry.translations.page_title }}
        </h1>
        <p class="mb-8 text-sm text-muted-foreground">
            {{ entry.translations.meta_description }}
        </p>

        <div
            v-for="aspect in aspects"
            :key="aspect"
            class="mb-6 rounded-lg border border-border bg-card p-5"
        >
            <h2 class="mb-3 text-base font-semibold">
                {{ t(`comparison.aspects.${aspect}`) }}
            </h2>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="mb-1 text-sm font-medium text-foreground/80">
                        {{ entry.first_title }}
                    </h3>
                    <p class="text-sm leading-relaxed text-muted-foreground">
                        {{ entry.translations[aspect as Aspect].first }}
                    </p>
                </div>
                <div>
                    <h3 class="mb-1 text-sm font-medium text-foreground/80">
                        {{ entry.second_title }}
                    </h3>
                    <p class="text-sm leading-relaxed text-muted-foreground">
                        {{ entry.translations[aspect as Aspect].second }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mb-8 rounded-lg border border-primary/20 bg-primary/5 p-5">
            <h2 class="mb-2 text-base font-semibold">
                {{ t('comparison.verdict_title') }}
            </h2>
            <p class="text-sm leading-relaxed text-foreground/80">
                {{ entry.translations.verdict }}
            </p>
        </div>

        <div class="rounded-lg border border-border bg-card p-6">
            <h2 class="mb-2 text-base font-semibold">
                {{ t('glossary.cta_title') }}
            </h2>
            <p class="mb-4 text-sm text-muted-foreground">
                {{ t('glossary.cta_description') }}
            </p>
            <Link
                prefetch
                :href="`/${locale}/application`"
                :class="buttonVariants()"
            >
                {{ t('glossary.cta_button') }}
            </Link>
        </div>
    </main>
    <AppFooter />
</template>
