@php($trackingPageKey = $pageKey ?? 'marketing')
<script>
    (() => {
        const endpoint = @json(route('marketing.click-events.store'));
        const csrfToken = @json(csrf_token());
        const defaultPageKey = @json($trackingPageKey);
        const clientTokenStorageKey = 'dossentry_marketing_client_token_v1';
        const attributionStorageKey = 'dossentry_marketing_attribution_v1';
        const landingUrlStorageKey = 'dossentry_marketing_landing_url_v1';
        const trackedParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
        const currentUrl = new URL(window.location.href);

        const storage = {
            get(key) {
                try {
                    return window.localStorage.getItem(key);
                } catch (error) {
                    return null;
                }
            },
            set(key, value) {
                try {
                    window.localStorage.setItem(key, value);
                } catch (error) {
                    // Ignore storage failures in private browsing or locked-down environments.
                }
            },
        };

        const normalizeText = (value) => (value || '').trim().replace(/\s+/g, ' ');

        const readJson = (key) => {
            const raw = storage.get(key);

            if (!raw) {
                return null;
            }

            try {
                return JSON.parse(raw);
            } catch (error) {
                return null;
            }
        };

        const writeJson = (key, value) => {
            storage.set(key, JSON.stringify(value));
        };

        const ensureClientToken = () => {
            const existing = normalizeText(storage.get(clientTokenStorageKey));

            if (existing) {
                return existing;
            }

            const fallbackToken = `mc_${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
            const token = window.crypto && typeof window.crypto.randomUUID === 'function'
                ? window.crypto.randomUUID()
                : fallbackToken;

            storage.set(clientTokenStorageKey, token);

            return token;
        };

        const currentAttribution = trackedParams.reduce((carry, key) => {
            const value = normalizeText(currentUrl.searchParams.get(key));

            if (value) {
                carry[key] = value;
            }

            return carry;
        }, {});

        const hasCurrentAttribution = Object.keys(currentAttribution).length > 0;
        const storedAttribution = readJson(attributionStorageKey) || {};
        const attribution = hasCurrentAttribution ? currentAttribution : storedAttribution;

        if (hasCurrentAttribution) {
            writeJson(attributionStorageKey, currentAttribution);
        }

        const storedLandingUrl = normalizeText(storage.get(landingUrlStorageKey));
        const landingUrl = hasCurrentAttribution || !storedLandingUrl ? currentUrl.toString() : storedLandingUrl;
        storage.set(landingUrlStorageKey, landingUrl);

        const sendEvent = (payload) => {
            const formData = new FormData();
            formData.append('_token', csrfToken);

            Object.entries(payload).forEach(([key, value]) => {
                if (value !== null && value !== undefined && value !== '') {
                    formData.append(key, value);
                }
            });

            if (typeof navigator.sendBeacon === 'function') {
                try {
                    if (navigator.sendBeacon(endpoint, formData)) {
                        return;
                    }
                } catch (error) {
                    // Fall through to fetch.
                }
            }

            fetch(endpoint, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                keepalive: true,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }).catch(() => {
                // Swallow tracking failures.
            });
        };

        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[data-track-cta]');

            if (!link) {
                return;
            }

            const href = normalizeText(link.getAttribute('href'));

            if (!href) {
                return;
            }

            let targetUrl;

            try {
                targetUrl = new URL(link.href || href, window.location.href);
            } catch (error) {
                return;
            }

            if (!['http:', 'https:'].includes(targetUrl.protocol)) {
                return;
            }

            const payload = {
                page_key: normalizeText(link.dataset.trackPage) || defaultPageKey,
                placement: normalizeText(link.dataset.trackPlacement),
                cta_key: normalizeText(link.dataset.trackCta),
                cta_label: normalizeText(link.dataset.trackLabel) || normalizeText(link.textContent),
                source_url: currentUrl.toString(),
                landing_url: landingUrl,
                target_url: targetUrl.toString(),
                client_token: ensureClientToken(),
            };

            trackedParams.forEach((key) => {
                if (attribution[key]) {
                    payload[key] = attribution[key];
                }
            });

            sendEvent(payload);
        });
    })();
</script>
