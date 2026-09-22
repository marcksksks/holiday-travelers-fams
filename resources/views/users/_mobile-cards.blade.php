<div class="grid gap-3 md:hidden">

    @forelse ($staff as $person)

        @php
            $roleStyle = match ($person->app_role) {
                'sys_admin' =>
                    'bg-violet-50 text-violet-700 ring-violet-200',

                'manager' =>
                    'bg-amber-50 text-amber-700 ring-amber-200',

                'admin_officer' =>
                    'bg-sky-50 text-sky-700 ring-sky-200',

                'legal_officer' =>
                    'bg-rose-50 text-rose-700 ring-rose-200',

                'receptionist' =>
                    'bg-cyan-50 text-cyan-700 ring-cyan-200',

                default =>
                    'bg-slate-50 text-slate-600 ring-slate-200',
            };
        @endphp


        <article
            data-staff-mobile-card
            class="card overflow-hidden">

            <div class="p-4">

                <div class="flex items-start justify-between gap-3">

                    <div class="flex min-w-0 items-start gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-primary/10 bg-primary/5 font-heading text-sm font-bold text-primary">

                            {{ strtoupper(substr($person->full_name ?: $person->email, 0, 1)) }}

                        </div>


                        <div class="min-w-0">

                            <div class="flex flex-wrap items-center gap-1.5">

                                <p class="truncate font-button text-sm font-semibold text-primary">
                                    {{ $person->full_name }}
                                </p>


                                @if ($person->id === auth()->id())

                                    <span class="rounded-full bg-accent/10 px-1.5 py-0.5 text-[8px] font-bold uppercase text-primary">
                                        You
                                    </span>

                                @endif

                            </div>


                            <p class="mt-1 truncate text-[11px] text-slate-500">
                                {{ $person->email }}
                            </p>


                            @if ($person->job_title || $person->department)

                                <p class="mt-1 truncate text-[10px] text-slate-400">
                                    {{ $person->job_title ?: 'Staff' }}

                                    @if ($person->department)
                                        · {{ $person->department }}
                                    @endif
                                </p>

                            @endif

                        </div>

                    </div>


                    @if ($person->is_active)

                        <span class="badge badge-success">
                            Active
                        </span>

                    @else

                        <span class="badge bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                            Deactivated
                        </span>

                    @endif

                </div>


                <div class="mt-4 grid grid-cols-2 gap-3">

                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Role
                        </p>

                        <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full px-2 py-1 text-[10px] font-semibold ring-1 ring-inset {{ $roleStyle }}">

                            <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>

                            {{ \App\Models\User::ROLES[$person->app_role] ?? str($person->app_role)->headline() }}

                        </span>

                    </div>


                    <div class="rounded-xl bg-background px-3 py-2.5">

                        <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Login Security
                        </p>


                        @if ($person->force_password_change)

                            <p class="mt-1 text-xs font-semibold text-amber-600">
                                Password change required
                            </p>

                        @else

                            <p class="mt-1 text-xs font-semibold text-success">
                                Password configured
                            </p>

                        @endif


                        <p class="mt-1 text-[10px] font-medium {{ $person->requiresMandatoryMfa() ? 'text-primary' : 'text-slate-400' }}">
                            {{ $person->requiresMandatoryMfa() ? 'MFA required' : 'MFA optional' }}
                        </p>

                    </div>

                </div>

            </div>


            @if ($person->id !== auth()->id())

                <details class="border-t border-border bg-background/40">

                    <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-xs font-semibold text-primary">

                        Manage Account

                        <svg
                            class="h-4 w-4 transition group-open:rotate-180"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7" />

                        </svg>

                    </summary>


                    <div class="space-y-4 border-t border-border bg-card p-4">

                        <form
                            method="POST"
                            action="{{ route('users.set-role', $person) }}"
                            onsubmit="return confirm('Change this staff member\'s system role? Their module access may change immediately.');">

                            @csrf

                            <label
                                for="mobile_role_{{ $person->id }}"
                                class="label">

                                Assigned Role

                            </label>

                            <div class="flex flex-col gap-2">

                                <select
                                    id="mobile_role_{{ $person->id }}"
                                    name="app_role"
                                    class="input">

                                    @foreach (\App\Models\User::ROLES as $value => $label)

                                        <option
                                            value="{{ $value }}"
                                            @selected($person->app_role === $value)>

                                            {{ $label }}

                                        </option>

                                    @endforeach

                                </select>


                                <button
                                    type="submit"
                                    class="btn-outline justify-center">

                                    Update Role

                                </button>

                            </div>

                        </form>


                        <div class="border-t border-border pt-4">

                            <form
                                method="POST"
                                action="{{ route('users.toggle-active', $person) }}"
                                onsubmit="return confirm('{{ $person->is_active ? 'Deactivate this staff account? The user will no longer be able to sign in.' : 'Reactivate this staff account and restore sign-in access?' }}');">

                                @csrf

                                @if ($person->is_active)

                                    <button
                                        type="submit"
                                        class="w-full rounded-lg border border-error/20 bg-error/5 px-4 py-2.5 text-xs font-semibold text-error transition hover:bg-error hover:text-white">

                                        Deactivate Account

                                    </button>

                                @else

                                    <button
                                        type="submit"
                                        class="w-full rounded-lg bg-success px-4 py-2.5 text-xs font-semibold text-white transition hover:opacity-90">

                                        Reactivate Account

                                    </button>

                                @endif

                            </form>

                        </div>

                    </div>

                </details>

            @else

                <div class="border-t border-border bg-background/40 px-4 py-3">

                    <p class="text-[11px] font-medium text-slate-500">
                        Current signed-in account
                    </p>

                </div>

            @endif

        </article>


    @empty

        <div class="card">

            <x-empty-state
                title="No staff accounts found"
                description="No staff accounts match the current search and filters.">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a4 4 0 00-5-4m-4 6H3v-2a4 4 0 014-4h2a4 4 0 014 4v2zm-5-8a4 4 0 100-8 4 4 0 000 8z" />

                    </svg>

                </x-slot:icon>

            </x-empty-state>

        </div>

    @endforelse

</div>