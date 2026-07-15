<script setup lang="ts">
import { InfoIcon } from '@lucide/vue';
import { ref } from 'vue';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

defineProps<{
    label: string;
    tooltip: string;
}>();

const open = ref(false);
const clickOpened = ref(false);

function onOpenChange(newOpen: boolean) {
    if (!newOpen && clickOpened.value) {
        return;
    }

    open.value = newOpen;

    if (!newOpen) {
        clickOpened.value = false;
    }
}

function onClick() {
    if (clickOpened.value) {
        open.value = false;
        clickOpened.value = false;
    } else {
        open.value = true;
        clickOpened.value = true;
    }
}
</script>

<template>
    <TooltipProvider>
        <Tooltip :open="open" @update:open="onOpenChange">
            <TooltipTrigger as-child>
                <button
                    type="button"
                    class="inline-flex size-4 cursor-pointer items-center justify-center text-muted-foreground hover:text-foreground"
                    @click.prevent="onClick"
                >
                    <InfoIcon class="size-3.5" />
                    <span class="sr-only">{{ label }}</span>
                </button>
            </TooltipTrigger>
            <TooltipContent class="max-w-xs">
                {{ tooltip }}
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>
</template>
