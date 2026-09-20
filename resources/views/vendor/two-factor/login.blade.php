@extends('two-factor::layout')

@section('card-body')

    <form
        method="POST"
        class="space-y-5">

        @csrf


        @if ($errors->isNotEmpty())

            <div
                role="alert"
                class="rounded-xl border border-error/30 bg-error/5 px-4 py-3">

                <div class="flex items-start gap-3">

                    <svg
                        class="mt-0.5 h-4 w-4 shrink-0 text-error"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M12 3L2 21h20L12 3z" />

                    </svg>

                    <div>

                        <p
                            class="text-xs font-semibold text-error">

                            Verification failed

                        </p>

                        @foreach ($errors->all() as $error)

                            <p
                                class="mt-1 text-xs leading-5 text-error">

                                {{ $error }}

                            </p>

                        @endforeach

                    </div>

                </div>

            </div>

        @endif


        <div>

            <label
                for="{{ $input }}"
                class="label">

                Authentication or Recovery Code

            </label>


            <input
                id="{{ $input }}"
                name="{{ $input }}"
                type="text"
                required
                autofocus
                autocomplete="one-time-code"
                spellcheck="false"
                autocapitalize="none"
                inputmode="text"
                minlength="6"
                maxlength="64"
                class="input text-center font-mono text-lg tracking-[0.22em] @error($input) border-error @enderror"
                placeholder="Enter your code">


            <p
                class="mt-2 text-xs leading-5 text-slate-500">

                Enter the current code from your authenticator app.
                You may also enter one of your unused recovery codes.

            </p>

        </div>


        <button
            type="submit"
            class="btn-primary w-full justify-center">

            Verify and Continue

        </button>


        <div
            class="rounded-xl border border-border bg-background/50 px-4 py-3">

            <div class="flex items-start gap-3">

                <svg
                    class="mt-0.5 h-4 w-4 shrink-0 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9h.01M11 12h1v4h1m8-4a9 9 0 11-18 0 9 9 0 0118 0z" />

                </svg>

                <p
                    class="text-xs leading-5 text-slate-500">

                    Authenticator codes change periodically.
                    If a code expires while entering it, use the newest code shown by your app.

                </p>

            </div>

        </div>

    </form>

@endsection
