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

const { locale } = useI18n();
const page = usePage<{ locales: LocaleOption[] }>();

function switchLanguage(code: string) {
    if (code === locale.value) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/locale';
    form.innerHTML = `<input name="_token" value="${document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')!.content}"><input name="locale" value="${code}">`;
    document.body.appendChild(form);
    form.submit();
}
</script>

<template>
    <Select :model-value="locale" @update:model-value="switchLanguage">
        <SelectTrigger size="sm" aria-label="Language">
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
