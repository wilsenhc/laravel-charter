import { router } from '@inertiajs/vue3';
import { registerSW } from 'virtual:pwa-register';

registerSW({ immediate: true });

let lastUpdateCheck = 0;

const checkForUpdates = async () => {
    if (Date.now() - lastUpdateCheck < 60 * 60 * 1000) {
        return;
    }

    lastUpdateCheck = Date.now();

    const registration = await navigator.serviceWorker.getRegistration();

    await registration?.update();
};

router.on('navigate', checkForUpdates);

document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        checkForUpdates();
    }
});