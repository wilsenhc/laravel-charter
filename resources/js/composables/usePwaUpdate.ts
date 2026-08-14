import { ref, readonly } from 'vue';

const updateAvailable = ref(false);
let controllerChangeHandler: (() => void) | null = null;

function handleControllerChange() {
    if (!updateAvailable.value) {
        updateAvailable.value = true;
    }
}

function initControllerChangeListener() {
    if (typeof window === 'undefined' || !('serviceWorker' in navigator)) {
        return;
    }

    controllerChangeHandler = handleControllerChange;
    navigator.serviceWorker.addEventListener(
        'controllerchange',
        controllerChangeHandler,
    );
}

function cleanup() {
    if (
        controllerChangeHandler &&
        typeof window !== 'undefined' &&
        'serviceWorker' in navigator
    ) {
        navigator.serviceWorker.removeEventListener(
            'controllerchange',
            controllerChangeHandler,
        );
        controllerChangeHandler = null;
    }
}

function reloadPage() {
    window.location.reload();
}

function dismissUpdate() {
    updateAvailable.value = false;
}

initControllerChangeListener();

export function usePwaUpdate() {
    return {
        updateAvailable: readonly(updateAvailable),
        reloadPage,
        dismissUpdate,
    };
}

if (typeof window !== 'undefined') {
    window.addEventListener('beforeunload', cleanup, { once: true });
}
