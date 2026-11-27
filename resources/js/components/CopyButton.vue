<script setup lang="ts">
import { CheckIcon, CopyIcon } from '@lucide/vue';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    value: string;
}>();

const { t } = useI18n();
const copied = ref(false);

function copy() {
    navigator.clipboard.writeText(props.value);
    copied.value = true;

    setTimeout(() => {
        copied.value = false;
    }, 2000);
}
</script>

<template>
    <Button
        variant="outline"
        size="sm"
        class="gap-1.5"
        :aria-label="copied ? t('code_block.copied') : t('code_block.copy')"
        @click="copy"
    >
        <CheckIcon v-if="copied" class="size-3.5" />
        <CopyIcon v-else class="size-3.5" />
        {{ copied ? t('code_block.copied') : t('code_block.copy') }}
    </Button>
</template>
