import { readonly, ref } from 'vue';

interface BeforeInstallPromptEvent extends Event {
    prompt: () => Promise<{ outcome: 'accepted' | 'dismissed' }>;
}

const canInstall = ref(false);
let installPrompt: BeforeInstallPromptEvent | null = null;

function handleBeforeInstallPrompt(event: Event) {
    const promptEvent = event as BeforeInstallPromptEvent;

    promptEvent.preventDefault();
    installPrompt = promptEvent;
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
}

export function useInstallPrompt() {
    return {
        canInstall: readonly(canInstall),
        install,
    };
}
