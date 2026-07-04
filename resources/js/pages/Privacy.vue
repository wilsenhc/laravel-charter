<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const sectionCount = 7;
</script>

<template>
    <AppHeader />
    <main class="mx-auto max-w-2xl px-4 py-12">
        <Link
            href="/"
            class="mb-8 inline-flex text-sm text-muted-foreground underline underline-offset-4 transition-colors hover:text-foreground"
        >
            {{ t('nav.back_to_charter') }}
        </Link>

        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            {{ t('privacy.title') }}
        </h1>
        <p class="mb-8 text-sm text-muted-foreground">
            {{ t('privacy.last_updated') }}
        </p>

        <section class="space-y-6 text-sm leading-relaxed text-foreground/80">
            <div v-for="i in sectionCount" :key="i">
                <h2 class="mb-2 font-semibold text-foreground">
                    {{ t(`privacy.sections.${i - 1}.heading`) }}
                </h2>

                <template v-if="i === 3">
                    <p>{{ t('privacy.sections.2.content_p1') }}</p>
                    <p class="mt-2">{{ t('privacy.sections.2.content_p2') }}</p>
                </template>

                <template v-else-if="i === 7">
                    <p>
                        {{ t('privacy.sections.6.content', { link: '\x00' }).split('\x00')[0] }}
                        <a
                            href="https://github.com/wilsenhc/laravel-charter"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-foreground underline underline-offset-4"
                        >{{ t('privacy.sections.6.link_text') }}</a>.
                    </p>
                </template>

                <template v-else>
                    <p>{{ t(`privacy.sections.${i - 1}.content`) }}</p>
                </template>
            </div>
        </section>

        <AppFooter />
    </main>
</template>
