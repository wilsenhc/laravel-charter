<script setup lang="ts">
import { MonitorIcon, MoonIcon, SunIcon } from '@lucide/vue';
import { useColorMode } from '@vueuse/core';
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
} from '@/components/ui/select';

const { t } = useI18n();

withDefaults(defineProps<{ withLabel?: boolean }>(), {
    withLabel: false,
});

const { store } = useColorMode({
    storageKey: 'vueuse-color-scheme',
    attribute: 'class',
    selector: 'html',
    initialValue: 'auto',
});

const mounted = ref(false);

onMounted(() => {
    mounted.value = true;
});

const options = computed(() => [
    { value: 'auto', label: t('appearance.system'), icon: MonitorIcon },
    { value: 'light', label: t('appearance.light'), icon: SunIcon },
    { value: 'dark', label: t('appearance.dark'), icon: MoonIcon },
]);

const activeIcon = computed(() => {
    if (!mounted.value) {
        return MonitorIcon;
    }

    const match = options.value.find((o) => o.value === store.value);

    return match?.icon ?? MonitorIcon;
});

const activeLabel = computed(() => {
    if (!mounted.value) {
        return t('appearance.system');
    }

    const match = options.value.find((o) => o.value === store.value);

    return match?.label ?? t('appearance.system');
});
</script>

<template>
    <Select v-model="store">
        <SelectTrigger
            size="sm"
            :aria-label="t('appearance.label')"
            :class="
                withLabel
                    ? 'w-full border-transparent bg-transparent hover:bg-muted data-[size=sm]:h-9 data-[size=sm]:rounded-[min(var(--radius-md),12px)] dark:bg-transparent dark:hover:bg-muted/50'
                    : ''
            "
        >
            <component :is="activeIcon" class="size-4" />
            <span v-if="withLabel">{{ activeLabel }}</span>
        </SelectTrigger>
        <SelectContent align="end" :side-offset="4">
            <SelectItem
                v-for="option in options"
                :key="option.value"
                :value="option.value"
                :aria-label="option.value"
            >
                <component :is="option.icon" class="size-4" />
                {{ option.label }}
            </SelectItem>
        </SelectContent>
    </Select>
</template>
