<script setup lang="ts">
import { CheckIcon, CopyIcon } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';

defineProps<{
    code: string;
}>();

const copied = ref(false);

const copy = async (code: string) => {
    await navigator.clipboard.writeText(code);

    copied.value = true;

    setTimeout(() => {
        copied.value = false;
    }, 2000);
};
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between gap-2">
            <span class="text-sm font-medium">Start shipping now</span>
            <Button
                variant="outline"
                size="sm"
                class="gap-1.5"
                :aria-label="copied ? 'Copied' : 'Copy command'"
                @click="copy(code)"
            >
                <CheckIcon v-if="copied" class="size-3.5" />
                <CopyIcon v-else class="size-3.5" />
                {{ copied ? 'Copied' : 'Copy' }}
            </Button>
        </div>
        <pre
            class="code-scroll overflow-x-auto rounded-md border border-border bg-secondary p-3 text-sm leading-relaxed"
        ><code>{{ code }}</code></pre>
        <p class="text-xs text-muted-foreground">
            Run this in your terminal to scaffold your project with Laravel
            Sail.
        </p>
    </div>
</template>
