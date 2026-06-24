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
    <div class="rounded-none border border-border p-4">
        <h2 class="text-base font-semibold">Start your new project</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            Run the command below in your terminal to scaffold a fresh Laravel
            application with your selected services, starter kit, and tooling.
        </p>

        <div
            class="mt-4 flex items-center gap-2 rounded-sm border border-border bg-secondary p-3 px-4"
        >
            <pre
                class="code-scroll min-w-0 flex-1 overflow-x-auto text-sm leading-relaxed"
            ><code>{{ code }}</code></pre>

            <Button
                variant="ghost"
                size="icon-sm"
                class="shrink-0"
                :aria-label="copied ? 'Copied' : 'Copy command'"
                @click="copy(code)"
            >
                <CheckIcon v-if="copied" class="size-3.5" />
                <CopyIcon v-else class="size-3.5" />
            </Button>
        </div>
    </div>
</template>
