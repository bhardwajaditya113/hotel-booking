<script>
(function () {
    var countryEl = document.getElementById('nxListingCountry');
    var indiaWrap = document.getElementById('nxListingStateIndiaWrap');
    var globalWrap = document.getElementById('nxListingStateGlobalWrap');
    var indiaSelect = document.getElementById('nxListingStateIndiaSelect');
    var indiaOtherWrap = document.getElementById('nxListingStateIndiaOtherWrap');
    var indiaOtherInput = document.getElementById('nxListingStateIndiaOther');
    var globalInput = document.getElementById('nxListingStateGlobalInput');
    var presetEl = document.getElementById('nxCancellationPreset');
    var customWrap = document.getElementById('nxCancellationCustomWrap');

    function countryIsIndia() {
        return countryEl && countryEl.value === 'India';
    }

    /** Exactly one enabled control submits as name="state". */
    function applyStateNames() {
        if (!indiaSelect || !globalInput || !indiaOtherInput) return;

        var india = countryIsIndia();
        var other = india && indiaSelect.value === '__other__';

        indiaSelect.disabled = !india || other;
        globalInput.disabled = india;

        indiaOtherInput.disabled = !india || !other;
        if (!other) {
            indiaOtherInput.removeAttribute('name');
            indiaOtherInput.value = '';
            indiaOtherWrap && indiaOtherWrap.classList.add('d-none');
        } else {
            indiaOtherWrap && indiaOtherWrap.classList.remove('d-none');
            indiaSelect.removeAttribute('name');
            indiaOtherInput.setAttribute('name', 'state');
            globalInput.removeAttribute('name');
            return;
        }

        if (india) {
            indiaSelect.setAttribute('name', 'state');
            globalInput.removeAttribute('name');
        } else {
            indiaSelect.removeAttribute('name');
            globalInput.setAttribute('name', 'state');
        }
    }

    function toggleIndiaOtherUi() {
        if (!indiaSelect || !indiaOtherWrap) return;
        var show = countryIsIndia() && indiaSelect.value === '__other__';
        indiaOtherWrap.classList.toggle('d-none', !show);
        if (!show && indiaOtherInput) indiaOtherInput.value = '';
        applyStateNames();
    }

    function refreshCountryUi() {
        var india = countryIsIndia();
        if (indiaWrap) indiaWrap.classList.toggle('d-none', !india);
        if (globalWrap) globalWrap.classList.toggle('d-none', india);
        toggleIndiaOtherUi();
    }

    function toggleCancellationCustom() {
        if (!presetEl || !customWrap) return;
        var custom = presetEl.value === 'custom';
        customWrap.classList.toggle('d-none', !custom);
    }

    countryEl && countryEl.addEventListener('change', refreshCountryUi);
    indiaSelect && indiaSelect.addEventListener('change', toggleIndiaOtherUi);
    presetEl && presetEl.addEventListener('change', toggleCancellationCustom);

    refreshCountryUi();
    toggleCancellationCustom();
})();
</script>
