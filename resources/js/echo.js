import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
const configuredReverbHost = import.meta.env.VITE_REVERB_HOST;
const localPage = ['localhost', '127.0.0.1'].includes(window.location.hostname);
const localReverb = ['localhost', '127.0.0.1'].includes(configuredReverbHost);

// Do not make a phone try to connect to its own localhost. The polling fallback
// below uses the current public web address and works through the existing tunnel.
if (configuredReverbHost && (localPage || !localReverb)) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: configuredReverbHost,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });

    window.Echo.channel('quattro.updates')
        .listen('.quattro.data.changed', payload => {
            if (liveRevisions && payload?.type && payload?.revision) {
                liveRevisions[payload.type] = payload.revision;
            }
            window.dispatchEvent(new CustomEvent('quattro:data-changed', { detail: payload }));
        });
}

// Poll the tiny revision endpoint as a fallback for mobile/ngrok setups where
// the web tunnel does not also expose the separate Reverb port.
let liveRevisions = null;
let liveRevisionRequest = false;

async function pollQuattroRevisions() {
    if (liveRevisionRequest || document.visibilityState !== 'visible') return;
    liveRevisionRequest = true;
    try {
        const response = await fetch('/live/revisions', {
            headers: { 'Accept': 'application/json', 'ngrok-skip-browser-warning': 'true' },
            credentials: 'same-origin',
            cache: 'no-store',
        });
        if (!response.ok) return;
        const { revisions = {} } = await response.json();
        if (liveRevisions) {
            Object.entries(revisions).forEach(([type, revision]) => {
                if (revision && revision !== liveRevisions[type]) {
                    window.dispatchEvent(new CustomEvent('quattro:data-changed', {
                        detail: { type, occurred_at: new Date().toISOString(), via: 'poll' },
                    }));
                }
            });
        }
        liveRevisions = revisions;
    } catch (error) {
        console.warn('Pemeriksaan pembaruan Quattro tertunda.', error);
    } finally {
        liveRevisionRequest = false;
    }
}

pollQuattroRevisions();
window.setInterval(pollQuattroRevisions, 4000);
