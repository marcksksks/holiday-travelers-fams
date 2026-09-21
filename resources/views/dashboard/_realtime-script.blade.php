@once
<script>
(() => {
    if (window.__famsDashboardRealtimeStarted) {
        return;
    }

    window.__famsDashboardRealtimeStarted = true;

    const root =
        document.querySelector(
            '[data-dashboard-realtime-root]'
        );

    if (!root) {
        return;
    }

    const partialUrl =
        root.dataset.dashboardPartialUrl;

    const configuredInterval =
        Number(
            root.dataset.dashboardRefreshInterval
        );

    const refreshInterval =
        Number.isFinite(configuredInterval) &&
        configuredInterval >= 5000
            ? configuredInterval
            : 15000;

    let refreshInProgress = false;
    let lastSuccessfulSync = Date.now();

    root.dataset.dashboardReceivedAtMs =
        String(
            Date.now()
        );


    const formatDuration =
        (milliseconds) => {

            const totalMinutes =
                Math.max(
                    0,
                    Math.ceil(
                        milliseconds / 60000
                    )
                );

            if (totalMinutes < 1) {
                return '< 1m';
            }

            const days =
                Math.floor(
                    totalMinutes / 1440
                );

            const hours =
                Math.floor(
                    (totalMinutes % 1440) / 60
                );

            const minutes =
                totalMinutes % 60;

            if (days > 0) {
                return hours > 0
                    ? `${days}d ${hours}h`
                    : `${days}d`;
            }

            if (hours > 0) {
                return minutes > 0
                    ? `${hours}h ${minutes}m`
                    : `${hours}h`;
            }

            return `${minutes}m`;
        };


    const setLiveStatus =
        (state) => {

            const status =
                root.querySelector(
                    '[data-dashboard-live-status]'
                );

            const indicator =
                root.querySelector(
                    '[data-dashboard-live-indicator]'
                );

            const dot =
                root.querySelector(
                    '[data-dashboard-live-dot]'
                );

            if (
                !status ||
                !indicator
            ) {
                return;
            }

            indicator.classList.remove(
                'border-success/20',
                'bg-success/5',
                'text-success',
                'border-warning/30',
                'bg-warning/5',
                'text-amber-600',
                'border-error/20',
                'bg-error/5',
                'text-error'
            );

            if (state === 'syncing') {

                indicator.classList.add(
                    'border-warning/30',
                    'bg-warning/5',
                    'text-amber-600'
                );

                status.textContent =
                    'Live · updating...';

                return;
            }

            if (state === 'error') {

                indicator.classList.add(
                    'border-error/20',
                    'bg-error/5',
                    'text-error'
                );

                status.textContent =
                    'Live update unavailable';

                return;
            }

            indicator.classList.add(
                'border-success/20',
                'bg-success/5',
                'text-success'
            );

            if (dot) {
                dot.classList.remove(
                    'hidden'
                );
            }

            const elapsedSeconds =
                Math.max(
                    0,
                    Math.floor(
                        (
                            Date.now() -
                            lastSuccessfulSync
                        ) / 1000
                    )
                );

            status.textContent =
                elapsedSeconds < 5
                    ? 'Live · updated just now'
                    : `Live · updated ${elapsedSeconds}s ago`;
        };


    const currentServerTimeFor =
        (element) => {

            const serverNow =
                Number(
                    element.dataset.serverNowMs
                );

            const receivedAt =
                Number(
                    root.dataset.dashboardReceivedAtMs
                );

            if (
                !Number.isFinite(serverNow) ||
                !Number.isFinite(receivedAt)
            ) {
                return null;
            }

            return serverNow +
                (
                    Date.now() -
                    receivedAt
                );
        };


    const updateLiveTimings =
        () => {

            root
                .querySelectorAll(
                    '[data-dashboard-appointment-timing]'
                )
                .forEach(
                    (element) => {

                        const value =
                            element.querySelector(
                                '[data-dashboard-appointment-timing-value]'
                            );

                        const startMs =
                            Number(
                                element.dataset.startMs
                            );

                        const currentTime =
                            currentServerTimeFor(
                                element
                            );

                        if (
                            !value ||
                            !Number.isFinite(startMs) ||
                            currentTime === null
                        ) {
                            return;
                        }

                        const difference =
                            startMs -
                            currentTime;

                        if (
                            Math.abs(
                                difference
                            ) < 60000
                        ) {
                            value.textContent =
                                'Starting now';

                            return;
                        }

                        if (difference > 0) {

                            value.textContent =
                                `Starts in ${
                                    formatDuration(
                                        difference
                                    )
                                }`;

                            return;
                        }

                        value.textContent =
                            `Started ${
                                formatDuration(
                                    Math.abs(
                                        difference
                                    )
                                )
                            } ago`;
                    }
                );


            root
                .querySelectorAll(
                    '[data-dashboard-facility-timing]'
                )
                .forEach(
                    (element) => {

                        const value =
                            element.querySelector(
                                '[data-dashboard-facility-timing-value]'
                            );

                        const endMs =
                            Number(
                                element.dataset.endMs
                            );

                        const currentTime =
                            currentServerTimeFor(
                                element
                            );

                        if (
                            !value ||
                            !Number.isFinite(endMs) ||
                            currentTime === null
                        ) {
                            return;
                        }

                        const remaining =
                            endMs -
                            currentTime;

                        if (remaining <= 0) {
                            value.textContent =
                                'Reservation ended';

                            return;
                        }

                        value.textContent =
                            `${
                                formatDuration(
                                    remaining
                                )
                            } remaining`;
                    }
                );
        };


    const refreshDashboard =
        async () => {

            if (
                document.hidden ||
                refreshInProgress ||
                !partialUrl
            ) {
                return;
            }

            refreshInProgress = true;

            setLiveStatus(
                'syncing'
            );

            try {

                const separator =
                    partialUrl.includes('?')
                        ? '&'
                        : '?';

                const response =
                    await fetch(
                        `${partialUrl}${separator}_=${Date.now()}`,
                        {
                            method: 'GET',

                            credentials:
                                'same-origin',

                            cache:
                                'no-store',

                            headers: {
                                'Accept':
                                    'text/html',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
                        }
                    );

                if (!response.ok) {
                    throw new Error(
                        `Dashboard refresh failed with HTTP ${response.status}`
                    );
                }

                const html =
                    await response.text();

                if (!html.trim()) {
                    throw new Error(
                        'Dashboard refresh returned an empty response.'
                    );
                }

                root.innerHTML =
                    html;

                root.dataset.dashboardReceivedAtMs =
                    String(
                        Date.now()
                    );

                lastSuccessfulSync =
                    Date.now();

                updateLiveTimings();

                setLiveStatus(
                    'live'
                );

            } catch (error) {

                console.warn(
                    'Dashboard live refresh failed.',
                    error
                );

                setLiveStatus(
                    'error'
                );

            } finally {

                refreshInProgress =
                    false;
            }
        };


    updateLiveTimings();

    setLiveStatus(
        'live'
    );


    window.setInterval(
        updateLiveTimings,
        30000
    );


    window.setInterval(
        () => {

            if (!refreshInProgress) {
                setLiveStatus(
                    'live'
                );
            }

        },
        1000
    );


    window.setInterval(
        refreshDashboard,
        refreshInterval
    );


    document.addEventListener(
        'visibilitychange',
        () => {

            if (!document.hidden) {
                refreshDashboard();
            }

        }
    );


    window.addEventListener(
        'focus',
        () => {
            refreshDashboard();
        }
    );
})();
</script>
@endonce