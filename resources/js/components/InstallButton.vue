<script setup lang="ts">
import { DownloadIcon } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import type { ButtonVariants } from '@/components/ui/button';
import { useInstallPrompt } from '@/composables/useInstallPrompt';

interface Props {
    variant?: ButtonVariants['variant'];
    size?: ButtonVariants['size'];
    withLabel?: boolean;
}

withDefaults(defineProps<Props>(), {
    variant: 'outline',
    size: 'sm',
    withLabel: false,
});

const { t } = useI18n();
const { canInstall, install } = useInstallPrompt();
</script>

<template>
    <Button
        v-if="canInstall"
        :variant="variant"
        :size="size"
        @click="install"
        :aria-label="withLabel ? undefined : t('nav.install')"
    >
        <DownloadIcon class="size-4" aria-hidden="true" />
        <span :class="withLabel ? '' : 'hidden sm:inline'">{{
            t('nav.install')
        }}</span>
    </Button>
</template>
