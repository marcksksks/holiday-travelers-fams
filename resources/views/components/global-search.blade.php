<div
    data-global-search
    data-search-url="{{ route('global-search.index') }}"
    class="relative flex shrink-0 items-center"
>
    {{-- Compact trigger for smaller widths --}}
    <button
        type="button"
        data-global-search-trigger
        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary xl:hidden"
        aria-label="Open global search"
        aria-controls="fams-global-search-panel"
        aria-expanded="false"
        title="Search"
    >
        <svg
            class="h-[18px] w-[18px]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"
            />
        </svg>
    </button>


    {{-- Desktop search field --}}
    <div
        class="hidden h-9 w-60 items-center gap-2 rounded-lg border border-border bg-background/30 px-3 transition focus-within:border-accent/60 focus-within:bg-card focus-within:ring-2 focus-within:ring-accent/10 xl:flex 2xl:w-72"
    >
        <svg
            class="h-4 w-4 shrink-0 text-slate-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"
            />
        </svg>

        <input
            type="search"
            data-global-search-input
            data-global-search-desktop
            placeholder="Search..."
            autocomplete="off"
            spellcheck="false"
            aria-label="Search"
            aria-controls="fams-global-search-panel"
            aria-expanded="false"
            class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-primary outline-none placeholder:text-slate-400 focus:border-0 focus:outline-none focus:ring-0"
        >

        <kbd
            class="shrink-0 rounded border border-border bg-card px-1.5 py-0.5 font-body text-[9px] font-medium leading-4 text-slate-400"
            aria-hidden="true"
        >
            Ctrl K
        </kbd>
    </div>


    {{-- Search dropdown --}}
    <div
        id="fams-global-search-panel"
        data-global-search-panel
        class="fixed inset-x-4 top-[4.5rem] z-[80] hidden w-auto overflow-hidden rounded-xl border border-border bg-card shadow-soft xl:absolute xl:inset-x-auto xl:right-0 xl:top-full xl:mt-2 xl:w-[min(29rem,calc(100vw-2rem))]"
        role="dialog"
        aria-label="Global search"
    >
        {{-- Search input used on compact layouts --}}
        <div class="border-b border-border p-3 xl:hidden">
            <div
                class="flex h-11 items-center gap-2 rounded-xl border border-border bg-background/30 px-3 focus-within:border-accent/60 focus-within:ring-2 focus-within:ring-accent/10"
            >
                <svg
                    class="h-4 w-4 shrink-0 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"
                    />
                </svg>

                <input
                    type="search"
                    data-global-search-input
                    data-global-search-compact
                    placeholder="Search records, people, workspaces..."
                    autocomplete="off"
                    spellcheck="false"
                    aria-label="Search"
                    aria-controls="fams-global-search-panel"
                    aria-expanded="false"
                    class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] text-primary outline-none placeholder:text-slate-400 focus:border-0 focus:outline-none focus:ring-0"
                >
            </div>
        </div>


        {{-- Initial state --}}
        <div
            data-global-search-state
            class="px-6 py-8 text-center"
        >
            <div
                class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-accent/10 text-accent"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"
                    />
                </svg>
            </div>

            <p
                class="mt-3 font-button text-sm font-semibold text-primary"
            >
                Search
            </p>

            <p
                class="mx-auto mt-1 max-w-sm text-xs leading-5 text-slate-500"
            >
                Find workspaces, facilities, reservations, visitors,
                documents, retention records, legal matters, contracts,
                and staff you are authorized to access.
            </p>
        </div>


        {{-- Loading --}}
        <div
            data-global-search-loading
            class="hidden px-6 py-9 text-center"
        >
            <div
                class="mx-auto h-5 w-5 animate-spin rounded-full border-2 border-accent/20 border-t-accent"
            ></div>

            <p class="mt-3 text-xs text-slate-500">
                Searching...
            </p>
        </div>


        {{-- Results --}}
        <div
            data-global-search-results
            class="hidden max-h-[calc(100dvh-13rem)] overflow-y-auto py-2 sm:max-h-[26rem]"
            role="listbox"
            aria-label="Global search results"
        ></div>


        {{-- Empty state --}}
        <div
            data-global-search-empty
            class="hidden px-6 py-9 text-center"
        >
            <div
                class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-background text-slate-400"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M8.5 8.5 15.5 15.5M15.5 8.5 8.5 15.5"
                    />
                </svg>
            </div>

            <p
                class="mt-3 font-button text-sm font-semibold text-primary"
            >
                No results found
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Try a different name, reference, email, or keyword.
            </p>
        </div>


        {{-- Footer --}}
        <div
            class="flex items-center justify-between gap-3 border-t border-border bg-background/30 px-4 py-2.5"
        >
            <p
                data-global-search-status
                class="text-[10px] text-slate-400"
                aria-live="polite"
            >
                Type at least 2 characters
            </p>

            <div
                class="hidden items-center gap-3 text-[10px] text-slate-400 sm:flex"
                aria-hidden="true"
            >
                <span>↑↓ Navigate</span>
                <span>Enter Open</span>
                <span>Esc Close</span>
            </div>
        </div>
    </div>
</div>


@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const root =
                document.querySelector('[data-global-search]');

            if (! root) {
                return;
            }

            const searchUrl =
                root.dataset.searchUrl;

            const trigger =
                root.querySelector('[data-global-search-trigger]');

            const panel =
                root.querySelector('[data-global-search-panel]');

            const inputs =
                Array.from(
                    root.querySelectorAll(
                        '[data-global-search-input]'
                    )
                );

            const desktopInput =
                root.querySelector(
                    '[data-global-search-desktop]'
                );

            const compactInput =
                root.querySelector(
                    '[data-global-search-compact]'
                );

            const state =
                root.querySelector(
                    '[data-global-search-state]'
                );

            const loading =
                root.querySelector(
                    '[data-global-search-loading]'
                );

            const results =
                root.querySelector(
                    '[data-global-search-results]'
                );

            const empty =
                root.querySelector(
                    '[data-global-search-empty]'
                );

            const status =
                root.querySelector(
                    '[data-global-search-status]'
                );

            let debounceTimer = null;
            let requestController = null;
            let activeQuery = '';


            const desktopFieldVisible = () =>
                window.matchMedia(
                    '(min-width: 1280px)'
                ).matches;


            const setExpanded = (expanded) => {

                const value =
                    expanded ? 'true' : 'false';

                trigger?.setAttribute(
                    'aria-expanded',
                    value
                );

                inputs.forEach(
                    (input) => {
                        input.setAttribute(
                            'aria-expanded',
                            value
                        );
                    }
                );
            };


            const openPanel = (
                focusInput = false
            ) => {

                window.dispatchEvent(
                    new CustomEvent(
                        'fams:close-notification-menu'
                    )
                );

                window.dispatchEvent(
                    new CustomEvent(
                        'fams:close-profile-menu'
                    )
                );

                panel.classList.remove('hidden');

                setExpanded(true);

                if (! focusInput) {
                    return;
                }

                window.setTimeout(
                    () => {

                        const target =
                            desktopFieldVisible()
                                ? desktopInput
                                : compactInput;

                        target?.focus();

                    },
                    0
                );
            };


            const closePanel = () => {

                panel.classList.add('hidden');

                setExpanded(false);
            };


            const showOnly = (target) => {

                [
                    state,
                    loading,
                    results,
                    empty,
                ].forEach(
                    (element) => {

                        if (! element) {
                            return;
                        }

                        element.classList.toggle(
                            'hidden',
                            element !== target
                        );
                    }
                );
            };


            const setStatus = (message) => {

                if (status) {
                    status.textContent = message;
                }
            };


            const syncInputs = (
                source,
                value
            ) => {

                inputs.forEach(
                    (input) => {

                        if (input !== source) {
                            input.value = value;
                        }
                    }
                );
            };


            const clearResults = () => {

                results?.replaceChildren();
            };


            const resultLinks = () =>
                Array.from(
                    results.querySelectorAll(
                        '[data-global-search-result]'
                    )
                );


            const focusResult = (index) => {

                const links =
                    resultLinks();

                if (links.length === 0) {
                    return;
                }

                const normalized =
                    (
                        index + links.length
                    )
                    %
                    links.length;

                links[normalized].focus();
            };


            const buildResults = (items) => {

                clearResults();

                let currentSection = null;

                items.forEach(
                    (item) => {

                        if (
                            item.section !== currentSection
                        ) {

                            const heading =
                                document.createElement(
                                    'div'
                                );

                            heading.className =
                                'px-4 pb-1 pt-2.5 text-[9px] font-semibold uppercase tracking-[0.16em] text-slate-400';

                            heading.textContent =
                                item.section;

                            results.appendChild(
                                heading
                            );

                            currentSection =
                                item.section;
                        }


                        const link =
                            document.createElement(
                                'a'
                            );

                        link.href =
                            item.url;

                        link.setAttribute(
                            'role',
                            'option'
                        );

                        link.dataset.globalSearchResult =
                            'true';

                        link.className =
                            'group mx-2 flex items-center gap-2.5 rounded-lg px-3 py-2.5 transition hover:bg-background focus:bg-background focus:outline-none focus-visible:ring-2 focus-visible:ring-accent/40';


                        const icon =
                            document.createElement(
                                'span'
                            );

                        icon.className =
                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-accent/10 font-button text-xs font-bold text-accent transition group-hover:bg-accent/15';

                        icon.textContent =
                            (
                                item.type
                                ||
                                item.section
                                ||
                                'F'
                            )
                                .charAt(0)
                                .toUpperCase();


                        const content =
                            document.createElement(
                                'span'
                            );

                        content.className =
                            'min-w-0 flex-1';


                        const title =
                            document.createElement(
                                'span'
                            );

                        title.className =
                            'block truncate font-button text-[13px] font-semibold text-primary';

                        title.textContent =
                            item.title;


                        const subtitle =
                            document.createElement(
                                'span'
                            );

                        subtitle.className =
                            'mt-0.5 block truncate text-[11px] text-slate-500';

                        subtitle.textContent =
                            item.subtitle
                            ||
                            item.type;


                        const badge =
                            document.createElement(
                                'span'
                            );

                        badge.className =
                            'shrink-0 rounded-md bg-background/60 px-2 py-1 text-[8px] font-semibold uppercase tracking-wide text-slate-400';

                        badge.textContent =
                            item.type;


                        content.append(
                            title,
                            subtitle
                        );

                        link.append(
                            icon,
                            content,
                            badge
                        );

                        results.appendChild(
                            link
                        );
                    }
                );
            };


            const performSearch = async (query) => {

                const normalized =
                    query.trim();

                activeQuery =
                    normalized;

                if (normalized.length < 2) {

                    requestController?.abort();

                    clearResults();

                    showOnly(state);

                    setStatus(
                        'Type at least 2 characters'
                    );

                    return;
                }

                openPanel();

                showOnly(loading);

                setStatus('Searching...');

                requestController?.abort();

                requestController =
                    new AbortController();

                try {

                    const response =
                        await fetch(
                            searchUrl
                            +
                            '?q='
                            +
                            encodeURIComponent(
                                normalized
                            ),
                            {
                                headers: {
                                    Accept:
                                        'application/json',
                                },

                                signal:
                                    requestController.signal,
                            }
                        );

                    if (! response.ok) {
                        throw new Error(
                            'Global search request failed.'
                        );
                    }

                    const payload =
                        await response.json();

                    if (
                        activeQuery !== normalized
                    ) {
                        return;
                    }

                    const items =
                        Array.isArray(
                            payload.results
                        )
                            ? payload.results
                            : [];

                    if (items.length === 0) {

                        clearResults();

                        showOnly(empty);

                        setStatus('No results');

                        return;
                    }

                    buildResults(items);

                    showOnly(results);

                    setStatus(
                        `${items.length} ${
                            items.length === 1
                                ? 'result'
                                : 'results'
                        }`
                    );

                } catch (error) {

                    if (
                        error.name === 'AbortError'
                    ) {
                        return;
                    }

                    clearResults();

                    showOnly(empty);

                    setStatus(
                        'Search temporarily unavailable'
                    );
                }
            };


            inputs.forEach(
                (input) => {

                    input.addEventListener(
                        'focus',
                        () => {

                            openPanel();

                            if (
                                input.value
                                    .trim()
                                    .length
                                <
                                2
                            ) {
                                showOnly(state);
                            }
                        }
                    );


                    input.addEventListener(
                        'input',
                        () => {

                            const value =
                                input.value;

                            syncInputs(
                                input,
                                value
                            );

                            window.clearTimeout(
                                debounceTimer
                            );

                            debounceTimer =
                                window.setTimeout(
                                    () => {
                                        performSearch(
                                            value
                                        );
                                    },
                                    220
                                );
                        }
                    );


                    input.addEventListener(
                        'keydown',
                        (event) => {

                            if (
                                event.key === 'ArrowDown'
                            ) {

                                event.preventDefault();

                                focusResult(0);

                                return;
                            }

                            if (
                                event.key === 'Enter'
                            ) {

                                const first =
                                    resultLinks()[0];

                                if (! first) {
                                    return;
                                }

                                event.preventDefault();

                                window.location.href =
                                    first.href;
                            }
                        }
                    );
                }
            );


            results.addEventListener(
                'keydown',
                (event) => {

                    const links =
                        resultLinks();

                    const currentIndex =
                        links.indexOf(
                            document.activeElement
                        );

                    if (currentIndex < 0) {
                        return;
                    }

                    if (
                        event.key === 'ArrowDown'
                    ) {

                        event.preventDefault();

                        focusResult(
                            currentIndex + 1
                        );

                        return;
                    }

                    if (
                        event.key === 'ArrowUp'
                    ) {

                        event.preventDefault();

                        if (currentIndex === 0) {

                            (
                                desktopFieldVisible()
                                    ? desktopInput
                                    : compactInput
                            )?.focus();

                            return;
                        }

                        focusResult(
                            currentIndex - 1
                        );
                    }
                }
            );


            trigger?.addEventListener(
                'click',
                () => {

                    if (
                        panel.classList.contains(
                            'hidden'
                        )
                    ) {
                        openPanel(true);
                    } else {
                        closePanel();
                    }
                }
            );


            document.addEventListener(
                'keydown',
                (event) => {

                    if (
                        (
                            event.ctrlKey
                            ||
                            event.metaKey
                        )
                        &&
                        event.key.toLowerCase() === 'k'
                    ) {

                        event.preventDefault();

                        openPanel(true);

                        return;
                    }

                    if (
                        event.key === 'Escape'
                    ) {
                        closePanel();
                    }
                }
            );


            document.addEventListener(
                'click',
                (event) => {

                    if (
                        ! root.contains(
                            event.target
                        )
                    ) {
                        closePanel();
                    }
                }
            );


            window.addEventListener(
                'fams:close-global-search',
                closePanel
            );

        });
    </script>
@endonce