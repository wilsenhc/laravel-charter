<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

interface GuidesIndexPageProps {
    locale: string;
    guides: {
        slug: string;
        category: string;
        title: string;
        description: string;
    }[];
}

const { t } = useI18n();
const props = defineProps<GuidesIndexPageProps>();

const grouped = computed(() => {
    const groups: Record<
        string,
        { slug: string; category: string; title: string; description: string }[]
    > = {};

    for (const guide of props.guides) {
        if (!groups[guide.category]) {
            groups[guide.category] = [];
        }

        groups[guide.category].push(guide);
    }

    return Object.entries(groups)
        .sort(([a], [b]) =>
            a === 'Application' ? -1 : b === 'Application' ? 1 : 0,
        )
        .map(([category, items]) => ({ category, items }));
});
</script>

<template>
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            {{ t('guides.page_title') }}
        </h1>
        <p class="mb-8 text-sm text-muted-foreground">
            {{ t('guides.page_description') }}
        </p>

        <div v-for="group in grouped" :key="group.category" class="mb-10">
            <h2 class="mb-4 text-lg font-semibold">
                {{ t(`guides.categories.${group.category}`) }}
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <Link
                    v-for="guide in group.items"
                    :key="guide.slug"
                    :href="`/${locale}/guides/${guide.slug}`"
                    prefetch
                    class="rounded-lg border border-border bg-card p-4 transition-colors hover:bg-accent"
                >
                    <h3 class="font-medium">{{ guide.title }}</h3>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ guide.description }}
                    </p>
                </Link>
            </div>
        </div>
    </main>
</template>
