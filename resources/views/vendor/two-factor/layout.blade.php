<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}">

    <title>
        Two-Factor Authentication | {{ config('app.name', 'FAMS') }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body class="min-h-full bg-background text-slate-900">

    <main
        class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6">

        {{-- Decorative background --}}
        <div
            aria-hidden="true"
            class="pointer-events-none absolute inset-0 overflow-hidden">

            <div
                class="absolute -left-20 -top-24 h-72 w-72 rounded-full bg-secondary/10 blur-3xl">
            </div>

            <div
                class="absolute -bottom-24 -right-20 h-80 w-80 rounded-full bg-accent/10 blur-3xl">
            </div>

        </div>


        <div class="relative w-full max-w-md">

            {{-- Branding --}}
            <div class="mb-6 text-center">

                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-white shadow-card">

                    <svg
                        class="h-7 w-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4zm-2 9l2 2 4-4" />

                    </svg>

                </div>


                <h1
                    class="mt-4 font-heading text-2xl font-bold text-primary">

                    Two-Factor Authentication

                </h1>


                <p
                    class="mt-2 text-sm leading-6 text-slate-500">

                    Complete the second verification step to securely access
                    {{ config('app.name', 'FAMS') }}.

                </p>

            </div>


            <section
                class="card overflow-hidden">

                <div
                    class="border-b border-border bg-background/60 px-6 py-5">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V7a5 5 0 00-10 0v2H6a2 2 0 00-2 2v8a2 2 0 002 2zm3-12V7a3 3 0 016 0v2H9z" />

                            </svg>

                        </div>

                        <div>

                            <h2
                                class="font-heading text-base font-semibold text-primary">

                                Verification Required

                            </h2>

                            <p
                                class="mt-0.5 text-xs text-slate-500">

                                Use your authenticator app or one unused recovery code.

                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6">

                    @yield('card-body')

                </div>

            </section>


            <div class="mt-5 text-center">

                <a
                    href="{{ route('login') }}"
                    class="text-sm font-semibold text-secondary transition hover:opacity-80">

                    ← Back to Sign In

                </a>

            </div>


            <p
                class="mt-5 text-center text-xs leading-5 text-slate-400">

                Never share your authentication or recovery codes with anyone.

            </p>

        </div>

    </main>

</body>
</html>
