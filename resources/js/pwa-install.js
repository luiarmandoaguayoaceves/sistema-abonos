import { registerSW } from 'virtual:pwa-register';

registerSW({
    immediate: true,
});

let deferredPrompt = null;

function isStandaloneMode() {
    return window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true;
}

document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('pwa-install-banner');
    const installButton = document.getElementById('pwa-install-button');
    const closeButton = document.getElementById('pwa-install-close');

    if (!banner || !installButton || !closeButton) {
        return;
    }

    if (isStandaloneMode()) {
        banner.classList.add('hidden');
        return;
    }

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;
        banner.classList.remove('hidden');
    });

    installButton.addEventListener('click', async () => {
        if (!deferredPrompt) {
            return;
        }

        deferredPrompt.prompt();

        await deferredPrompt.userChoice;

        deferredPrompt = null;
        banner.classList.add('hidden');
    });

    closeButton.addEventListener('click', () => {
        banner.classList.add('hidden');
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        banner.classList.add('hidden');
    });
});