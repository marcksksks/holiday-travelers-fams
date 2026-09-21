<div
    id="fams-system-clock"
    class="hidden items-center gap-2 rounded-xl border border-border bg-white/70 px-3 py-2 shadow-sm backdrop-blur-sm sm:flex"
    data-server-time="{{ now()->timestamp * 1000 }}"
    data-timezone="{{ config('app.timezone', 'Asia/Manila') }}"
>
    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-accent/10 text-primary">

        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            class="h-4 w-4"
            aria-hidden="true"
        >
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M12 7v5l3 2"></path>
        </svg>

    </div>

    <div class="min-w-0 leading-tight">

        <p
            data-system-clock-date
            class="whitespace-nowrap text-[9px] font-semibold uppercase tracking-wider text-slate-400"
        >
            --
        </p>

        <div class="mt-0.5 flex items-baseline gap-1.5">

            <p
                data-system-clock-time
                class="whitespace-nowrap font-heading text-sm font-bold tabular-nums text-primary"
            >
                --:--:--
            </p>

            <span
                data-system-clock-period
                class="text-[9px] font-semibold text-secondary"
            >
            </span>

        </div>

    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const clock =
                document.getElementById('fams-system-clock');

            if (! clock) {
                return;
            }

            const dateElement =
                clock.querySelector('[data-system-clock-date]');

            const timeElement =
                clock.querySelector('[data-system-clock-time]');

            const periodElement =
                clock.querySelector('[data-system-clock-period]');

            const timezone =
                clock.dataset.timezone || 'Asia/Manila';

            const serverTimestamp =
                Number(clock.dataset.serverTime);

            const startedAt =
                performance.now();

            const currentTime = () => {

                const elapsed =
                    performance.now() - startedAt;

                return new Date(
                    serverTimestamp + elapsed
                );

            };

            const dateFormatter =
                new Intl.DateTimeFormat(
                    'en-PH',
                    {
                        timeZone: timezone,
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric',
                    }
                );

            const timeFormatter =
                new Intl.DateTimeFormat(
                    'en-PH',
                    {
                        timeZone: timezone,
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true,
                    }
                );

            const updateClock = () => {

                const now =
                    currentTime();

                dateElement.textContent =
                    dateFormatter
                        .format(now)
                        .toUpperCase();

                const parts =
                    timeFormatter
                        .formatToParts(now);

                const hour =
                    parts.find(
                        part => part.type === 'hour'
                    )?.value ?? '--';

                const minute =
                    parts.find(
                        part => part.type === 'minute'
                    )?.value ?? '--';

                const second =
                    parts.find(
                        part => part.type === 'second'
                    )?.value ?? '--';

                const period =
                    parts.find(
                        part => part.type === 'dayPeriod'
                    )?.value ?? '';

                timeElement.textContent =
                    `${hour}:${minute}:${second}`;

                periodElement.textContent =
                    period;

            };

            updateClock();

            window.setInterval(
                updateClock,
                1000
            );

        });
    </script>
@endonce