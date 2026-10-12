<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { buttonVariants } from '@/components/ui/button';

interface GuidesShowPageProps {
    locale: string;
    guide: string;
    entry: {
        category: string;
        translations: {
            title: string;
            meta_description: string;
            heading: string;
            problem: string;
            notes: string[];
            steps: string[];
        };
    };
    related: { slug: string; title: string; description: string }[];
    cta: { type: 'application' | 'package'; href: string };
}

const { t } = useI18n();
defineProps<GuidesShowPageProps>();
</script>

<template>
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <Link
            :href="`/${locale}/guides`"
            prefetch
            class="mb-6 inline-flex text-sm text-muted-foreground underline underline-offset-4 transition-colors hover:text-foreground"
        >
            &larr; {{ t('guides.back_to_guides') }}
        </Link>

        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            {{ entry.translations.heading }}
        </h1>
        <p class="mb-6 text-sm leading-relaxed text-muted-foreground">
            {{ entry.translations.problem }}
        </p>

        <div class="mb-8 space-y-3 text-sm leading-relaxed text-foreground/80">
            <h2 class="text-base font-semibold">
                {{ t('guides.notes_title') }}
            </h2>
            <p v-for="(note, index) in entry.translations.notes" :key="index">
                {{ note }}
            </p>
        </div>

        <div class="mb-8 rounded-lg border border-primary/20 bg-primary/5 p-5">
            <h2 class="mb-2 text-base font-semibold">
                {{ t('guides.cta_title') }}
            </h2>
            <p class="mb-4 text-sm text-muted-foreground">
                {{ t('guides.cta_description') }}
            </p>
            <Link prefetch :href="cta.href" :class="buttonVariants()">
                {{
                    cta.type === 'package'
                        ? t('guides.cta_button_package')
                        : t('guides.cta_button_application')
                }}
            </Link>
        </div>

        <div class="mb-8 text-sm leading-relaxed text-foreground/80">
            <h2 class="mb-2 text-base font-semibold">
                {{ t('guides.steps_title') }}
            </h2>
            <ol class="ml-4 list-decimal space-y-1">
                <li
                    v-for="(step, index) in entry.translations.steps"
                    :key="index"
                >
                    {{ step }}
                </li>
            </ol>
        </div>

        <div v-if="related.length > 0">
            <h2 class="mb-4 text-base font-semibold">
                {{ t('guides.related_title') }}
            </h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <Link
                    v-for="relatedGuide in related"
                    :key="relatedGuide.slug"
                    :href="`/${locale}/guides/${relatedGuide.slug}`"
                    prefetch
                    class="rounded-lg border border-border bg-card p-3 transition-colors hover:bg-accent"
                >
                    <h3 class="text-sm font-medium">
                        {{ relatedGuide.title }}
                    </h3>
                    <p class="mt-0.5 text-xs text-muted-foreground">
                        {{ relatedGuide.description }}
                    </p>
                </Link>
            </div>
        </div>
    </main>
</template>
