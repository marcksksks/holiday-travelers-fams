<div
    id="fams-system-clock"
    role="timer"
    aria-label="Current system date and time"
    aria-live="off"
    class="hidden min-w-[112px] flex-col items-end justify-center sm:flex"
    data-server-time="{{ now()->timestamp * 1000 }}"
    data-timezone="{{ config('app.timezone', 'Asia/Manila') }}"
>
    <p
        data-system-clock-date
        class="whitespace-nowrap text-[9px] font-semibold uppercase tracking-[0.14em] text-slate-400"
    >
        --
    </p>

    <div class="mt-0.5 flex items-baseline justify-end gap-1.5">

        <p
            data-system-clock-time
            class="whitespace-nowrap font-heading text-base font-semibold tracking-tight tabular-nums text-primary"
        >
            --:--:--
        </p>

        <span
            data-system-clock-period
            class="whitespace-nowrap text-[10px] font-semibold uppercase text-secondary"
        >
        </span>

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