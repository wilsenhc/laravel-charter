<script setup lang="ts">
import { RefreshCwIcon, XIcon } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { usePwaUpdate } from '@/composables/usePwaUpdate';

const { t } = useI18n();
const { updateAvailable, reloadPage, dismissUpdate } = usePwaUpdate();

function dismiss() {
    dismissUpdate();
}
</script>

<template>
    <Alert
        v-if="updateAvailable"
        class="animate-slide-in-from-bottom fixed right-4 bottom-4 z-50 w-full max-w-sm"
        variant="default"
    >
        <AlertTitle class="flex items-center gap-2 text-sm">
            <RefreshCwIcon class="size-4 animate-spin" aria-hidden="true" />
            {{ t('pwa.update_available_title') }}
        </AlertTitle>
        <AlertDescription class="text-xs">
            {{ t('pwa.update_available_description') }}
        </AlertDescription>
        <div class="mt-3 flex gap-2">
            <Button size="sm" @click="reloadPage">
                {{ t('pwa.update_reload') }}
            </Button>
            <Button size="sm" variant="ghost" @click="dismiss">
                <XIcon class="size-3.5" aria-hidden="true" />
                <span class="hidden sm:inline">{{
                    t('pwa.update_dismiss')
                }}</span>
            </Button>
        </div>
    </Alert>
</template>
