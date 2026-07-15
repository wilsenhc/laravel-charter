<script setup lang="ts">
import { CheckIcon, CopyIcon } from '@lucide/vue';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';

const { t } = useI18n();

defineProps<{
    code: string;
}>();

const codeEl = ref<HTMLElement | null>(null);
const copied = ref(false);

function showCopiedFeedback() {
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
}

const copy = (text: string) => {
    navigator.clipboard.writeText(text);
    showCopiedFeedback();
};

function selectAndCopy() {
    if (codeEl.value) {
        const range = document.createRange();
        range.selectNodeContents(codeEl.value);
        const selection = window.getSelection();

        if (selection) {
            selection.removeAllRanges();
            selection.addRange(range);
        }

        document.execCommand('copy');
        showCopiedFeedback();
    }
}
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between gap-2">
            <span class="text-sm font-medium">{{
                t('code_block.heading')
            }}</span>
            <Button
                variant="outline"
                size="sm"
                class="gap-1.5"
                :aria-label="
                    copied ? t('code_block.copied') : t('code_block.aria_label')
                "
                @click="copy(code)"
            >
                <CheckIcon v-if="copied" class="size-3.5" />
                <CopyIcon v-else class="size-3.5" />
                {{ copied ? t('code_block.copied') : t('code_block.copy') }}
            </Button>
        </div>
        <pre
            class="code-scroll cursor-pointer overflow-x-auto rounded-md border border-border bg-secondary p-3 text-sm leading-relaxed"
            @click="selectAndCopy"
        ><code ref="codeEl">{{ code }}</code></pre>
        <p class="text-xs text-muted-foreground">
            {{ t('code_block.hint') }}
        </p>
    </div>
</template>
