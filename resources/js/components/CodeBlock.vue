<script setup lang="ts">
import { CheckIcon, CopyIcon } from '@lucide/vue';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

const { t } = useI18n();

const props = withDefaults(
    defineProps<{
        commands: {
            curl: string;
            wget: string;
        };
        disabled?: boolean;
        hint?: string;
    }>(),
    { disabled: false, hint: undefined },
);

const activeTool = ref<string>('curl');

const activeCommand = computed(
    () => props.commands[activeTool.value as 'curl' | 'wget'],
);

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
</script>

<template>
    <div class="space-y-2">
        <Tabs v-model="activeTool">
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm font-medium">{{
                    t('code_block.heading')
                }}</span>
                <div class="flex items-center gap-2">
                    <TabsList>
                        <TabsTrigger value="curl">curl</TabsTrigger>
                        <TabsTrigger value="wget">wget</TabsTrigger>
                    </TabsList>
                    <Button
                        variant="default"
                        size="sm"
                        class="gap-1.5"
                        :disabled="disabled"
                        :aria-label="
                            copied
                                ? t('code_block.copied')
                                : t('code_block.aria_label')
                        "
                        @click="copy(activeCommand)"
                    >
                        <CheckIcon v-if="copied" class="size-3.5" />
                        <CopyIcon v-else class="size-3.5" />
                        {{
                            copied
                                ? t('code_block.copied')
                                : t('code_block.copy')
                        }}
                    </Button>
                </div>
            </div>
            <TabsContent
                v-for="(command, tool) in commands"
                :key="tool"
                :value="tool"
            >
                <pre
                    class="code-scroll overflow-x-auto rounded-md border border-border bg-secondary p-3 text-sm leading-relaxed select-all"
                ><code>{{ command }}</code></pre>
            </TabsContent>
        </Tabs>
        <p v-if="disabled" class="text-xs text-destructive" role="status">
            {{ t('form_errors.fix_highlighted') }}
        </p>
        <p v-else class="text-xs text-muted-foreground">
            {{ hint ?? t('code_block.hint') }}
        </p>
    </div>
</template>
