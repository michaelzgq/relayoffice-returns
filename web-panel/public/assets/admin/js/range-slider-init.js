document.querySelectorAll('.range-slider-wrapper').forEach(group => {
    const minPrice = group.querySelector('.minPrice');
    const maxPrice = group.querySelector('.maxPrice');
    const minPriceValue = group.querySelector('.minPriceValue');
    const maxPriceValue = group.querySelector('.maxPriceValue');
    const rangeTrack = group.querySelector('.rangeTrack');
    const tooltipMin = group.querySelector('.tooltipMin');
    const tooltipMax = group.querySelector('.tooltipMax');
    const dir = document.documentElement.getAttribute('dir') || 'ltr';

    function updateTrackBackground() {
        const min = parseInt(minPrice.min);
        const max = parseInt(maxPrice.max);
        const minVal = parseInt(minPrice.value);
        const maxVal = parseInt(maxPrice.value);
        const range = max - min;

        const minPercent = ((minVal - min) / range) * 100;
        const maxPercent = ((maxVal - min) / range) * 100;

        rangeTrack.style.insetInlineStart = `${minPercent}%`;
        rangeTrack.style.width = `${maxPercent - minPercent}%`;

    }

    function updateTooltipsAndTrack() {
        const min = parseInt(minPrice.min);
        const max = parseInt(minPrice.max);
        const minVal = parseInt(minPrice.value);
        const maxVal = parseInt(maxPrice.value);
        const minPercent = ((minVal - min) / (max - min)) * 100;
        const maxPercent = ((maxVal - min) / (max - min)) * 100;

        minPriceValue.value = minVal;
        maxPriceValue.value = maxVal;

        tooltipMin.textContent = minVal;
        tooltipMax.textContent = maxVal;

        tooltipMin.style.insetInlineStart = dir === 'rtl' ? `${100 - minPercent}%` : `${minPercent}%`;
        tooltipMax.style.insetInlineStart = dir === 'rtl' ? `${100 - maxPercent}%` : `${maxPercent}%`;

        updateTrackBackground();
    }

    minPrice.addEventListener('input', () => {
        if (parseInt(minPrice.value) >= parseInt(maxPrice.value)) {
            minPrice.value = maxPrice.value - 1;
        }
        updateTooltipsAndTrack();
    });

    maxPrice.addEventListener('input', () => {
        if (parseInt(maxPrice.value) <= parseInt(minPrice.value)) {
            maxPrice.value = parseInt(minPrice.value) + 1;
        }
        updateTooltipsAndTrack();
    });

    minPriceValue.addEventListener('input', () => {
        let value = parseInt(minPriceValue.value) || 0;
        value = Math.max(parseInt(minPrice.min), Math.min(value, parseInt(maxPrice.value) - 1));
        minPrice.value = value;
        updateTooltipsAndTrack();
    });

    maxPriceValue.addEventListener('input', () => {
        let value = parseInt(maxPriceValue.value) || 0;
        value = Math.min(parseInt(maxPrice.max), Math.max(value, parseInt(minPrice.value) + 1));
        maxPrice.value = value;
        updateTooltipsAndTrack();
    });

    updateTooltipsAndTrack();
});
