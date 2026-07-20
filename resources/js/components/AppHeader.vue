<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';
import AppearanceSwitcher from '@/components/AppearanceSwitcher.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { Button } from '@/components/ui/button';

const { t } = useI18n();

const locale = computed(() => usePage().props.locale as string);

const isScrolled = ref(false);

function onScroll() {
    isScrolled.value = window.scrollY > 0;
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
});
</script>

<template>
    <header
        class="sticky top-0 z-50 flex h-14 items-center justify-between border-b border-border bg-background px-5 transition-[background-color,backdrop-filter] duration-300"
        :class="{ 'bg-background/80 backdrop-blur-xl': isScrolled }"
    >
        <Link
            :href="`/${locale}/application`"
            class="text-base font-bold tracking-tight hover:underline"
            >{{ t('header.app_name') }}</Link
        >
        <div class="flex items-center gap-3">
            <Button
                variant="outline"
                size="sm"
                as="a"
                href="https://github.com/wilsenhc/laravel-charter"
                target="_blank"
                rel="noopener noreferrer"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="size-3.5"
                    aria-hidden="true"
                >
                    <path
                        d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.91.58.11.79-.25.79-.56 0-.27-.01-1-.02-1.96-3.2.7-3.88-1.54-3.88-1.54-.52-1.33-1.28-1.68-1.28-1.68-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.18 1.18a11.05 11.05 0 0 1 5.8 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.84 1.19 3.1 0 4.42-2.69 5.39-5.25 5.68.41.36.78 1.06.78 2.14 0 1.55-.01 2.8-.01 3.18 0 .31.21.68.8.56A11.51 11.51 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5Z"
                    />
                </svg>
                {{ t('nav.github') }}
            </Button>
            <LanguageSwitcher />
            <AppearanceSwitcher />
        </div>
    </header>
</template>
