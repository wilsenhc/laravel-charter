<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { LanguagesIcon } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
} from '@/components/ui/select';
import type { LocaleOption } from '@/types';

const { locale, t } = useI18n();
const page = usePage<{ locales: LocaleOption[] }>();

function switchLanguage(code: string) {
    if (code === locale.value) {
        return;
    }

    const token = document.querySelector<HTMLMetaElement>(
        'meta[name="csrf-token"]',
    )?.content;

    if (!token) {
        throw new Error('CSRF token meta tag not found.');
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/locale';

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = token;

    const localeInput = document.createElement('input');
    localeInput.type = 'hidden';
    localeInput.name = 'locale';
    localeInput.value = code;

    form.append(tokenInput, localeInput);
    document.body.appendChild(form);
    form.submit();
}
</script>

<template>
    <Select :model-value="locale" @update:model-value="switchLanguage">
        <SelectTrigger size="sm" :aria-label="t('nav.language')">
            <LanguagesIcon class="size-4" />
        </SelectTrigger>
        <SelectContent align="end" :side-offset="4">
            <SelectItem
                v-for="loc in page.props.locales"
                :key="loc.code"
                :value="loc.code"
            >
                {{ loc.label }}
            </SelectItem>
        </SelectContent>
    </Select>
</template>
