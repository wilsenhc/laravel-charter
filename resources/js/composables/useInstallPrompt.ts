import { onMounted, readonly, ref } from 'vue';

interface BeforeInstallPromptEvent extends Event {
    prompt: () => Promise<{ outcome: 'accepted' | 'dismissed' }>;
}

const canInstall = ref(true);
let installPrompt: BeforeInstallPromptEvent | null = null;
let dismissTimer: ReturnType<typeof setTimeout> | null = null;

function handleBeforeInstallPrompt(event: Event) {
    const promptEvent = event as BeforeInstallPromptEvent;

    promptEvent.preventDefault();
    installPrompt = promptEvent;

    if (dismissTimer !== null) {
        clearTimeout(dismissTimer);
        dismissTimer = null;
    }

    canInstall.value = true;
}

function handleAppInstalled() {
    installPrompt = null;
    canInstall.value = false;
}

async function install() {
    if (!installPrompt) {
        return;
    }

    try {
        await installPrompt.prompt();
    } finally {
        installPrompt = null;
        canInstall.value = false;
    }
}

if (typeof window !== 'undefined') {
    window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
    window.addEventListener('appinstalled', handleAppInstalled);

    dismissTimer = setTimeout(() => {
        canInstall.value = false;
    }, 5000);
}

export function useInstallPrompt() {
    onMounted(() => {
        if (!('onbeforeinstallprompt' in window)) {
            canInstall.value = false;
        }
    });

    return {
        canInstall: readonly(canInstall),
        install,
    };
}
