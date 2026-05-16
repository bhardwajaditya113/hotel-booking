import './bootstrap';
import './echo-client';

const meta = (name) => document.querySelector(`meta[name="${name}"]`)?.getAttribute('content') ?? '';

function toast(kind, message) {
    if (typeof window.toastr !== 'undefined' && message) {
        window.toastr[kind](message);
    }
}

function scheduleReloadIfPathMatches(fragmentSubstrings) {
    const path = window.location.pathname || '';
    const hit = fragmentSubstrings.some((frag) => path.includes(frag));
    if (!hit) {
        return;
    }
    window.setTimeout(() => window.location.reload(), 1700);
}

const userId = meta('auth-user-id');

if (window.Echo && userId) {
    window.Echo.private(`host.${userId}`)
        .listen('.HostBookingRequestReceived', (e) => {
            const prefix = meta('booking-realtime-msg-host-request');
            const code = e?.booking_code ? String(e.booking_code) : '';
            toast('info', `${prefix}${code ? ` (${code})` : ''}`);
            scheduleReloadIfPathMatches(['/property/bookings/incoming']);
        })
        .error((err) => console.warn('[booking-realtime] host:', err));

    window.Echo.private(`App.Models.User.${userId}`)
        .listen('.GuestBookingDecisionReceived', (e) => {
            const dec = e?.decision;
            if (dec === 'approved') {
                toast('success', meta('booking-realtime-msg-guest-approved'));
            } else {
                toast('warning', meta('booking-realtime-msg-guest-declined'));
            }
            scheduleReloadIfPathMatches(['/user/booking', '/booking/confirmation']);
        })
        .error((err) => console.warn('[booking-realtime] guest:', err));
}
