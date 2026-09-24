<div
    data-staff-create-modal
    class="fixed inset-0 z-[80] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="staff-create-title">

    {{-- Backdrop --}}
    <button
        type="button"
        data-staff-create-close
        class="absolute inset-0 bg-slate-950/50 backdrop-blur-[2px]"
        aria-label="Close Add Staff dialog">
    </button>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div
            class="relative w-full max-w-2xl overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">

            {{-- Modal header --}}
            <div class="flex items-start justify-between gap-4 border-b border-border bg-background/60 px-6 py-5">

                <div class="flex items-start gap-3">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm10-4v6m3-3h-6" />

                        </svg>

                    </div>


                    <div>

                        <p class="text-[10px] font-semibold uppercase tracking-wider text-secondary">
                            New Account
                        </p>

                        <h3
                            id="staff-create-title"
                            class="mt-0.5 font-heading text-lg font-semibold text-primary">
                            Add Staff Member
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Create system access and assign the appropriate organizational role.
                        </p>

                    </div>

                </div>


                <button
                    type="button"
                    data-staff-create-close
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

            </div>


            <form
                method="POST"
                action="{{ route('users.store') }}"
                class="max-h-[75vh] overflow-y-auto">

                @csrf


                <div class="space-y-5 p-6">

                    {{-- Identity --}}
                    <div>

                        <p class="mb-3 text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            Staff Identity
                        </p>

                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>

                                <label for="staff_full_name" class="label">
                                    Full Name
                                    <span class="text-error">*</span>
                                </label>

                                <input
                                    id="staff_full_name"
                                    type="text"
                                    name="full_name"
                                    required
                                    value="{{ old('full_name') }}"
                                    placeholder="Juan Dela Cruz"
                                    class="input">

                                @error('full_name')
                                    <p class="mt-1 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            <div>

                                <label for="staff_email" class="label">
                                    Email Address
                                    <span class="text-error">*</span>
                                </label>

                                <input
                                    id="staff_email"
                                    type="email"
                                    name="email"
                                    required
                                    value="{{ old('email') }}"
                                    placeholder="staff@example.com"
                                    class="input">

                                @error('email')
                                    <p class="mt-1 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- Organization --}}
                    <div>

                        <p class="mb-3 text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            Organization
                        </p>

                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>

                                <label for="staff_department" class="label">
                                    Department
                                </label>

                                <input
                                    id="staff_department"
                                    type="text"
                                    name="department"
                                    value="{{ old('department') }}"
                                    placeholder="Administration"
                                    class="input">

                            </div>


                            <div>

                                <label for="staff_job_title" class="label">
                                    Job Title
                                </label>

                                <input
                                    id="staff_job_title"
                                    type="text"
                                    name="job_title"
                                    value="{{ old('job_title') }}"
                                    placeholder="Administrative Officer"
                                    class="input">

                            </div>


                            <div>

                                <label for="staff_phone" class="label">
                                    Contact Number
                                </label>

                                <input
                                    id="staff_phone"
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="09XXXXXXXXX"
                                    class="input">

                            </div>


                            <div>

                                <div class="flex items-center justify-between gap-3">

                                    <label for="staff_app_role" class="label">
                                        System Role
                                        <span class="text-error">*</span>
                                    </label>

                                    <span class="text-[10px] font-medium uppercase tracking-wide text-slate-400">
                                        Access Level
                                    </span>

                                </div>


                                <select
                                    id="staff_app_role"
                                    name="app_role"
                                    required
                                    class="input">

                                    @foreach (\App\Models\User::ROLES as $value => $label)

                                        <option
                                            value="{{ $value }}"
                                            @selected(old('app_role', 'employee') === $value)>

                                            {{ $label }}

                                        </option>

                                    @endforeach

                                </select>


                                <div class="mt-2 rounded-lg border border-primary/10 bg-primary/[0.03] px-3 py-2.5">

                                    <div class="flex items-start gap-2">

                                        <svg
                                            class="mt-0.5 h-4 w-4 shrink-0 text-accent"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />

                                        </svg>

                                        <p
                                            data-staff-role-description
                                            class="text-[11px] leading-5 text-slate-500">
                                            Select a role to review its intended level of system access.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Security --}}
                    <div class="rounded-xl border border-warning/20 bg-warning/5 p-4">

                        <div class="flex items-start gap-3">

                            <div class="mt-0.5 text-amber-600">

                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13.55A2 2 0 004.2 20h15.6a2 2 0 001.73-2.99L13.71 3.86a2 2 0 00-3.42 0z" />

                                </svg>

                            </div>


                            <div class="min-w-0 flex-1">

                                <label for="staff_password" class="label">
                                    Temporary Password
                                    <span class="text-error">*</span>
                                </label>


                                <div class="relative">

                                    <input
                                        id="staff_password"
                                        type="password"
                                        name="password"
                                        required
                                        minlength="12"
                                        autocomplete="new-password"
                                        placeholder="Minimum 12 characters"
                                        class="input pr-36">


                                    <div class="absolute inset-y-0 right-2 flex items-center gap-1">

                                        <button
                                            type="button"
                                            data-staff-password-generate
                                            class="rounded-md px-2 py-1 text-[10px] font-semibold text-secondary transition hover:bg-secondary/10">
                                            Generate
                                        </button>

                                        <span class="h-4 w-px bg-border"></span>

                                        <button
                                            type="button"
                                            data-staff-password-toggle
                                            class="rounded-md px-2 py-1 text-[10px] font-semibold text-primary transition hover:bg-primary/5">
                                            Show
                                        </button>

                                    </div>

                                </div>


                                <div class="mt-3">

                                    <div class="flex items-center justify-between gap-3">

                                        <span class="text-[10px] font-medium text-slate-400">
                                            Password strength
                                        </span>

                                        <span
                                            data-staff-strength-label
                                            class="text-[10px] font-semibold text-slate-400">
                                            Not entered
                                        </span>

                                    </div>


                                    <div class="mt-1.5 grid grid-cols-4 gap-1">

                                        <span
                                            data-staff-strength-bar
                                            class="h-1 rounded-full bg-slate-200">
                                        </span>

                                        <span
                                            data-staff-strength-bar
                                            class="h-1 rounded-full bg-slate-200">
                                        </span>

                                        <span
                                            data-staff-strength-bar
                                            class="h-1 rounded-full bg-slate-200">
                                        </span>

                                        <span
                                            data-staff-strength-bar
                                            class="h-1 rounded-full bg-slate-200">
                                        </span>

                                    </div>

                                </div>


                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Use a temporary password with at least 12 characters, uppercase and lowercase letters, a number, and a symbol. The staff member must replace it after first login.
                                </p>


                                @error('password')

                                    <p class="mt-1 text-xs font-medium text-error">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Footer --}}
                <div class="flex flex-col-reverse gap-3 border-t border-border bg-background/50 px-6 py-4 sm:flex-row sm:justify-end">

                    <button
                        type="button"
                        data-staff-create-close
                        class="btn-outline justify-center">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn-secondary justify-center">
                        Create Staff Account
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script data-staff-create-enhancements>
    (() => {

        const roleSelect =
            document.getElementById('staff_app_role');

        const roleDescription =
            document.querySelector(
                '[data-staff-role-description]'
            );

        const password =
            document.getElementById('staff_password');

        const generateButton =
            document.querySelector(
                '[data-staff-password-generate]'
            );

        const toggleButton =
            document.querySelector(
                '[data-staff-password-toggle]'
            );

        const strengthLabel =
            document.querySelector(
                '[data-staff-strength-label]'
            );

        const strengthBars =
            Array.from(
                document.querySelectorAll(
                    '[data-staff-strength-bar]'
                )
            );


        const roleDescriptions = {
            employee:
                'General staff access to the operational modules permitted for employees.',

            receptionist:
                'Front-desk access focused on appointments, visitors, and visitor operations.',

            admin_officer:
                'Administrative access for facilities, documents, records, and operational management.',

            manager:
                'Management-level oversight with approval, reporting, and administrative review access.',

            legal_officer:
                'Specialized access for legal records, legal review, and contract-related information.',

            sys_admin:
                'Highest administrative role with system administration and staff account management access.'
        };


        const updateRoleDescription =
            () => {

                if (! roleSelect || ! roleDescription) {
                    return;
                }

                roleDescription.textContent =
                    roleDescriptions[roleSelect.value]
                    ??
                    'Access will be determined by the permissions configured for this role.';

            };


        const passwordScore =
            (value) => {

                if (! value) {
                    return 0;
                }

                let score = 0;

                if (value.length >= 12) {
                    score++;
                }

                if (
                    /[a-z]/.test(value) &&
                    /[A-Z]/.test(value)
                ) {
                    score++;
                }

                if (/\d/.test(value)) {
                    score++;
                }

                if (/[^A-Za-z0-9]/.test(value)) {
                    score++;
                }

                return Math.min(score, 4);

            };


        const updatePasswordStrength =
            () => {

                if (! password || ! strengthLabel) {
                    return;
                }

                const score =
                    passwordScore(password.value);

                const states = {
                    0: {
                        label: 'Not entered',
                        className: 'bg-slate-200'
                    },
                    1: {
                        label: 'Weak',
                        className: 'bg-error'
                    },
                    2: {
                        label: 'Fair',
                        className: 'bg-warning'
                    },
                    3: {
                        label: 'Good',
                        className: 'bg-accent'
                    },
                    4: {
                        label: 'Strong',
                        className: 'bg-success'
                    }
                };

                const state =
                    states[score];

                strengthLabel.textContent =
                    state.label;

                strengthBars.forEach(
                    (bar, index) => {

                        bar.classList.remove(
                            'bg-slate-200',
                            'bg-error',
                            'bg-warning',
                            'bg-accent',
                            'bg-success'
                        );

                        if (index < score) {
                            bar.classList.add(
                                state.className
                            );
                        }
                        else {
                            bar.classList.add(
                                'bg-slate-200'
                            );
                        }

                    }
                );

            };


        const randomIndex =
            (length) => {

                const values =
                    new Uint32Array(1);

                crypto.getRandomValues(values);

                return values[0] % length;

            };


        const generatePassword =
            () => {

                if (! password) {
                    return;
                }

                const groups = [
                    'ABCDEFGHJKLMNPQRSTUVWXYZ',
                    'abcdefghijkmnopqrstuvwxyz',
                    '23456789',
                    '!@#$%^&*'
                ];

                const all =
                    groups.join('');

                const characters =
                    groups.map(
                        (group) =>
                            group[randomIndex(group.length)]
                    );

                while (characters.length < 14) {

                    characters.push(
                        all[randomIndex(all.length)]
                    );

                }

                for (
                    let i = characters.length - 1;
                    i > 0;
                    i--
                ) {

                    const j =
                        randomIndex(i + 1);

                    [
                        characters[i],
                        characters[j]
                    ] = [
                        characters[j],
                        characters[i]
                    ];

                }

                password.value =
                    characters.join('');

                password.type =
                    'text';

                if (toggleButton) {
                    toggleButton.textContent = 'Hide';
                }

                password.dispatchEvent(
                    new Event(
                        'input',
                        {
                            bubbles: true
                        }
                    )
                );

                password.focus();
                password.select();

            };


        roleSelect?.addEventListener(
            'change',
            updateRoleDescription
        );

        password?.addEventListener(
            'input',
            updatePasswordStrength
        );

        generateButton?.addEventListener(
            'click',
            generatePassword
        );


        updateRoleDescription();
        updatePasswordStrength();

    })();
</script>