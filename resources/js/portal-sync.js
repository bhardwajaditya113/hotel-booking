import './bootstrap';
import './echo-client';

const meta = (name) => document.querySelector(`meta[name="${name}"]`)?.getAttribute('content');

let clientVersion = parseInt(meta('portal-sync-version') || '0', 10) || 0;
const pollUrl = meta('portal-sync-poll-url');

function softReload() {
    if (document.visibilityState !== 'visible') {
        return;
    }
    window.location.reload();
}

function onNewVersion(v) {
    if (!Number.isFinite(v) || v <= clientVersion) {
        return;
    }
    clientVersion = v;
    softReload();
}

if (window.Echo) {
    window.Echo.private('portal-sync')
        .listen('.PortalDataChanged', (e) => {
            const raw = e?.version;
            const v = typeof raw === 'number' ? raw : parseInt(raw, 10);
            onNewVersion(v);
        })
        .error((err) => console.warn('[portal-sync] Echo:', err));
}

if (pollUrl) {
    window.setInterval(async () => {
        try {
            const res = await window.axios.get(pollUrl);
            const v = parseInt(res.data?.version, 10);
            onNewVersion(v);
        } catch {
            /* ignore */
        }
    }, 20000);
}
