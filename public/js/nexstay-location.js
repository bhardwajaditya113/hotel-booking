/**
 * Location search: Photon (Komoot) forward geocoder — no API key.
 * https://photon.komoot.io — use responsibly in production (cache, rate limits).
 */
(function () {
    'use strict';

    function debounce(fn, ms) {
        var t;
        return function () {
            var ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, ms);
        };
    }

    function photonUrl(q) {
        return 'https://photon.komoot.io/api/?q=' + encodeURIComponent(q) + '&limit=8';
    }

    function labelFromFeature(f) {
        var p = f.properties || {};
        var parts = [];
        if (p.name) parts.push(p.name);
        if (p.city || p.town || p.village) parts.push(p.city || p.town || p.village);
        if (p.state) parts.push(p.state);
        if (p.country) parts.push(p.country);
        return parts.join(', ') || (p.name || 'Location');
    }

    function initPicker(wrap) {
        var cityInp = wrap.querySelector('[data-nx-city]');
        var latInp = wrap.querySelector('[data-nx-lat]');
        var lngInp = wrap.querySelector('[data-nx-lng]');
        var list = wrap.querySelector('.nx-location-suggestions');
        if (!cityInp || !latInp || !lngInp || !list) return;

        function hideList() {
            list.hidden = true;
            list.innerHTML = '';
            cityInp.setAttribute('aria-expanded', 'false');
        }

        function showList() {
            list.hidden = false;
            cityInp.setAttribute('aria-expanded', 'true');
        }

        var runSearch = debounce(function () {
            var q = (cityInp.value || '').trim();
            if (q.length < 2) {
                hideList();
                return;
            }
            fetch(photonUrl(q))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    list.innerHTML = '';
                    var feats = (data && data.features) ? data.features : [];
                    if (!feats.length) {
                        hideList();
                        return;
                    }
                    feats.forEach(function (f, idx) {
                        var coords = f.geometry && f.geometry.coordinates;
                        if (!coords || coords.length < 2) return;
                        var lng = coords[0], lat = coords[1];
                        var li = document.createElement('li');
                        li.className = 'list-group-item list-group-item-action nx-loc-item';
                        li.setAttribute('role', 'option');
                        li.textContent = labelFromFeature(f);
                        li.addEventListener('mousedown', function (e) {
                            e.preventDefault();
                            cityInp.value = labelFromFeature(f);
                            latInp.value = String(lat);
                            lngInp.value = String(lng);
                            hideList();
                            cityInp.focus();
                        });
                        list.appendChild(li);
                    });
                    showList();
                })
                .catch(function () { hideList(); });
        }, 220);

        cityInp.addEventListener('input', runSearch);
        cityInp.addEventListener('focus', function () {
            if (list.children.length) showList();
        });
        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) hideList();
        });
        cityInp.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') hideList();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-nx-location-picker]').forEach(initPicker);
    });
})();
