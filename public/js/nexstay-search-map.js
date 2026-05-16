/**
 * Search / explore map — multiple property markers + list sync.
 */
(function () {
    'use strict';

    function tileOpts() {
        return {
            maxZoom: 19,
            attribution: window.NEXSTAY_MAP_ATTR || '&copy; OpenStreetMap',
        };
    }

    function tileUrl() {
        return window.NEXSTAY_MAP_TILE || 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    }

    function boot() {
        if (typeof L === 'undefined') return;
        var el = document.getElementById('nx-search-map');
        if (!el || !window.NEXSTAY_MAP_MARKERS) return;

        var markers = window.NEXSTAY_MAP_MARKERS;
        var center = window.NEXSTAY_MAP_CENTER || { lat: 20.5937, lng: 78.9629 };
        var zoom = window.NEXSTAY_MAP_ZOOM || 5;

        var map = L.map(el, { scrollWheelZoom: true }).setView([center.lat, center.lng], zoom);
        L.tileLayer(tileUrl(), tileOpts()).addTo(map);

        var group = [];
        markers.forEach(function (m) {
            if (typeof m.lat !== 'number' || typeof m.lng !== 'number') return;
            var html = '<div class="nx-map-popup" style="min-width:180px">';
            if (m.image) {
                html += '<img src="' + String(m.image).replace(/"/g, '') + '" alt="" style="width:100%;height:100px;object-fit:cover;border-radius:8px;margin-bottom:8px">';
            }
            html += '<strong style="display:block">' + (m.name || '') + '</strong>';
            if (m.city) html += '<span style="font-size:12px;opacity:.85">' + m.city + '</span>';
            if (m.min_price) html += '<div style="margin-top:6px;font-weight:600;color:#0d6efd">From ₹' + Math.round(m.min_price) + '/night</div>';
            html += '<a href="' + m.url + '" style="display:inline-block;margin-top:8px;font-size:13px">View listing →</a></div>';
            var marker = L.marker([m.lat, m.lng]).addTo(map).bindPopup(html);
            marker._nxId = m.id;
            group.push(marker);
        });

        if (group.length) {
            map.fitBounds(L.featureGroup(group).getBounds().pad(0.15));
        }

        document.querySelectorAll('.nx-map-list-item').forEach(function (row) {
            row.addEventListener('mouseenter', function () {
                var lat = parseFloat(row.dataset.lat);
                var lng = parseFloat(row.dataset.lng);
                if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
                    map.panTo([lat, lng], { animate: true });
                }
            });
        });

        setTimeout(function () { map.invalidateSize(); }, 300);
        setTimeout(function () { map.invalidateSize(); }, 1000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
