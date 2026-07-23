<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';

const { t, tm } = useI18n();
const page = usePage();

const locale = computed(() => page.props.locale as string);
const origin = typeof window !== 'undefined' ? window.location.origin : '';
const sectionCount = computed(() => tm('terms.sections').length);

const breadcrumbJsonLd = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: [
            {
                '@type': 'ListItem',
                position: 1,
                name: 'Charter for Laravel',
                item: origin,
            },
            {
                '@type': 'ListItem',
                position: 2,
                name: 'Terms of Service',
                item: `${origin}/${locale.value}/terms`,
            },
        ],
    }),
);
</script>

<template>
    <Head>
        <title>{{ t('terms.title') }} — {{ t('header.app_name') }}</title>
        <meta name="description" :content="t('terms.meta_description')" />
        <link rel="canonical" :href="`${origin}/${locale}/terms`" />
        <component :is="'script'" type="application/ld+json">{{
            breadcrumbJsonLd
        }}</component>
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

        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            {{ t('terms.title') }}
        </h1>
        <p class="mb-8 text-sm text-muted-foreground">
            {{ t('terms.last_updated') }}
        </p>

        <section class="space-y-6 text-sm leading-relaxed text-foreground/80">
            <div v-for="i in sectionCount" :key="i">
                <h2 class="mb-2 font-semibold text-foreground">
                    {{ t(`terms.sections.${i - 1}.heading`) }}
                </h2>

                <template v-if="i === 3">
                    <p>
                        {{
                            t('terms.sections.2.content', {
                                link: '\x00',
                            }).split('\x00')[0]
                        }}
                        <Link
                            :href="`/${locale}/privacy`"
                            prefetch
                            class="font-medium text-foreground underline underline-offset-4"
                            >{{ t('terms.sections.2.link_text') }}</Link
                        >.
                    </p>
                </template>

                <template v-else-if="i === 7">
                    <p>
                        {{
                            t('terms.sections.6.content', {
                                link: '\x00',
                            }).split('\x00')[0]
                        }}
                        <a
                            href="https://github.com/wilsenhc/laravel-charter"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-foreground underline underline-offset-4"
                            >{{ t('terms.sections.6.link_text') }}</a
                        >
                        {{
                            t('terms.sections.6.content', {
                                link: '\x00',
                            }).split('\x00')[1]
                        }}
                    </p>
                </template>

                <template v-else>
                    <p>{{ t(`terms.sections.${i - 1}.content`) }}</p>
                </template>
            </div>
        </section>

        <AppFooter />
    </main>
</template>
