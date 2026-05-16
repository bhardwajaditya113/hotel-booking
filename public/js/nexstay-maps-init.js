/**
 * Leaflet maps (OpenStreetMap tiles). Requires global L after leaflet.js.
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

    function initOne(el) {
        if (el.dataset.nxLeafletInit) return;
        el.dataset.nxLeafletInit = '1';
        if (typeof L === 'undefined') return;
        var lat = parseFloat(el.dataset.lat);
        var lng = parseFloat(el.dataset.lng);
        if (Number.isNaN(lat) || Number.isNaN(lng)) return;
        var z = parseInt(el.dataset.zoom, 10) || 13;
        var map = L.map(el, { scrollWheelZoom: false }).setView([lat, lng], z);
        L.tileLayer(tileUrl(), tileOpts()).addTo(map);
        var marker = L.marker([lat, lng]).addTo(map);
        if (el.dataset.title) {
            marker.bindPopup(el.dataset.title);
        }
        setTimeout(function () { map.invalidateSize(); }, 350);
        setTimeout(function () { map.invalidateSize(); }, 1200);
    }

    function initAll() {
        document.querySelectorAll('[data-nx-leaflet]').forEach(initOne);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
