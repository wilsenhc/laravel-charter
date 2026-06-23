<script setup lang="ts">
import { MonitorIcon, MoonIcon, SunIcon } from '@lucide/vue';
import { useColorMode } from '@vueuse/core';
import { computed } from 'vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
} from '@/components/ui/select';

const { store } = useColorMode({
    storageKey: 'vueuse-color-scheme',
    attribute: 'class',
    selector: 'html',
    initialValue: 'auto',
});

const options = [
    { value: 'auto', label: 'System', icon: MonitorIcon },
    { value: 'light', label: 'Light', icon: SunIcon },
    { value: 'dark', label: 'Dark', icon: MoonIcon },
] as const;

const activeIcon = computed(() => {
    const match = options.find((o) => o.value === store.value);

    return match?.icon ?? MonitorIcon;
});
</script>

<template>
    <Select v-model="store">
        <SelectTrigger size="sm" aria-label="Color scheme">
            <component :is="activeIcon" class="size-4" />
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
