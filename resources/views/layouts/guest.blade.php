<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'Sign in') · Holiday Travelers Travel & Tours Inc.
    </title>

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body class="min-h-screen bg-background font-body text-slate-700 antialiased">

    <main
        class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-8 sm:py-12">

        {{-- Background --}}
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-gradient-to-b from-accent/20 to-transparent">
        </div>

        <div
            class="pointer-events-none absolute -bottom-32 -right-24 h-96 w-96 rounded-full bg-secondary/10 blur-3xl">
        </div>

        <div
            class="pointer-events-none absolute -left-32 top-1/3 h-80 w-80 rounded-full bg-accent/10 blur-3xl">
        </div>


        <div class="relative z-10 w-full max-w-md">

            {{-- Brand --}}
            <div class="mb-5 flex items-center justify-center gap-3">

                <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full border border-border bg-card shadow-card">

                    <img
                        src="{{ asset('images/holiday-travelers-mark.png') }}"
                        alt="Holiday Travelers"
                        class="h-full w-full object-contain"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                    <span
                        class="hidden h-full w-full items-center justify-center font-heading text-sm font-bold text-primary">
                        HT
                    </span>

                </div>


                <div class="min-w-0">

                    <p class="font-heading text-sm font-bold text-primary">
                        Holiday Travelers
                    </p>

                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-secondary">
                        Facilities & Admin System
                    </p>

                </div>

            </div>


            @if (session('status'))

                <div
                    role="status"
                    class="mb-4 rounded-xl border border-success/20 bg-success/10 px-4 py-3 text-sm text-green-700 shadow-sm">

                    {{ session('status') }}

                </div>

            @endif


            @if ($errors->any())

                <div
                    role="alert"
                    class="mb-4 rounded-xl border border-error/20 bg-error/10 px-4 py-3 text-sm text-error shadow-sm">

                    <p class="mb-1 font-semibold">
                        Please check the following:
                    </p>

                    <ul class="list-inside list-disc space-y-1">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            @yield('content')


            <div class="mt-5 flex items-center justify-center gap-2 text-center text-[11px] text-slate-400">

                <svg
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 11V7a4 4 0 118 0v4m-8 0h8v8h-8M4 11h4v8H4z" />

                </svg>

                <span>
                    Secure Holiday Travelers administrative portal
                </span>

            </div>

        </div>

    </main>

</body>
</html>