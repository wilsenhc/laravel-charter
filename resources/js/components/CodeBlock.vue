<script setup lang="ts">
import { CheckIcon, CopyIcon } from '@lucide/vue';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';

const { t } = useI18n();

const props = withDefaults(
    defineProps<{
        commands: {
            curl: string;
            wget: string;
        };
        disabled?: boolean;
        hint?: string;
        summary?: string[];
    }>(),
    { disabled: false, hint: undefined, summary: () => [] },
);

const tools = ['curl', 'wget'] as const;

const activeTool = ref<(typeof tools)[number]>('curl');

const activeCommand = computed(() => props.commands[activeTool.value]);

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
    <div
        class="border border-white/10 bg-[#171717] px-4 py-4 text-white sm:px-5 dark:bg-[#222222]"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div
                role="group"
                :aria-label="t('code_block.heading')"
                class="flex items-center gap-1"
            >
                <button
                    v-for="tool in tools"
                    :key="tool"
                    type="button"
                    :aria-pressed="activeTool === tool"
                    class="min-h-8 rounded-sm px-2.5 text-xs font-medium transition-colors outline-none focus-visible:ring-3 focus-visible:ring-white/40"
                    :class="
                        activeTool === tool
                            ? 'bg-white/10 text-white'
                            : 'text-white/60 hover:text-white'
                    "
                    @click="activeTool = tool"
                >
                    {{ tool }}
                </button>
            </div>

            <div class="flex items-center gap-3">
                <span
                    class="text-[11px] font-medium tracking-wider uppercase"
                    :class="disabled ? 'text-[#ff9f0a]' : 'text-[#30d158]'"
                >
                    [{{
                        disabled
                            ? t('code_block.status_blocked')
                            : t('code_block.status_ready')
                    }}]
                </span>
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
                    {{ copied ? t('code_block.copied') : t('code_block.copy') }}
                </Button>
            </div>
        </div>

        <pre
            class="mt-4 border-t border-white/10 pt-4 text-sm leading-relaxed [overflow-wrap:anywhere] whitespace-pre-wrap select-all"
        ><code><span class="select-none text-white/35" aria-hidden="true">$ </span>{{ activeCommand }}</code></pre>

        <p v-if="summary.length" class="mt-3 text-xs text-white/60">
            <span class="text-white/35" aria-hidden="true"># </span
            >{{ summary.join(' · ') }}
        </p>

        <p
            class="mt-2 text-xs"
            :class="disabled ? 'text-[#ff9f0a]' : 'text-white/60'"
            :role="disabled ? 'status' : undefined"
        >
            {{
                disabled
                    ? t('form_errors.fix_highlighted')
                    : (hint ?? t('code_block.hint'))
            }}
        </p>
    </div>
</template>
