<?php $__env->startSection('title', 'Staff Accounts'); ?>

<?php $__env->startSection('content'); ?>

<div class="space-y-6">

    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Staff Accounts
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Create staff accounts, assign system roles, and manage account access.
            </p>
        </div>

        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

            <p class="text-xs text-slate-500">
                Total Accounts
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                <?php echo e($stats['total']); ?>

            </p>

        </div>

    </div>


    
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-primary"></div>

            <p class="text-xs font-medium text-slate-500">
                Staff Accounts
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-primary">
                <?php echo e($stats['total']); ?>

            </p>

            <p class="mt-1 text-xs text-slate-400">
                Registered system users
            </p>

        </div>


        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-success"></div>

            <p class="text-xs font-medium text-slate-500">
                Active
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-success">
                <?php echo e($stats['active']); ?>

            </p>

            <p class="mt-1 text-xs text-slate-400">
                Accounts with system access
            </p>

        </div>


        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-slate-400"></div>

            <p class="text-xs font-medium text-slate-500">
                Deactivated
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-slate-600">
                <?php echo e($stats['inactive']); ?>

            </p>

            <p class="mt-1 text-xs text-slate-400">
                Accounts without access
            </p>

        </div>


        <div class="card relative overflow-hidden p-5">

            <div class="absolute inset-x-0 bottom-0 h-1 bg-warning"></div>

            <p class="text-xs font-medium text-slate-500">
                Password Change
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-amber-600">
                <?php echo e($stats['password_change']); ?>

            </p>

            <p class="mt-1 text-xs text-slate-400">
                Temporary passwords pending
            </p>

        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[390px_minmax(0,1fr)]">

        
        <div>

            <div class="card overflow-hidden xl:sticky xl:top-6">

                <div class="border-b border-border bg-background/60 px-5 py-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

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

                            <h3 class="font-heading text-base font-semibold text-primary">
                                Create Staff Account
                            </h3>

                            <p class="mt-0.5 text-xs text-slate-500">
                                A password change will be required after first login.
                            </p>

                        </div>

                    </div>

                </div>


                <form
                    method="POST"
                    action="<?php echo e(route('users.store')); ?>"
                    class="space-y-5 p-5">

                    <?php echo csrf_field(); ?>


                    <div>

                        <label for="full_name" class="label">
                            Full Name
                            <span class="text-error">*</span>
                        </label>

                        <input
                            id="full_name"
                            type="text"
                            name="full_name"
                            required
                            value="<?php echo e(old('full_name')); ?>"
                            placeholder="Juan Dela Cruz"
                            class="input">

                        <?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    <div>

                        <label for="email" class="label">
                            Email Address
                            <span class="text-error">*</span>
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            required
                            value="<?php echo e(old('email')); ?>"
                            placeholder="staff@example.com"
                            class="input">

                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    <div class="grid gap-4 sm:grid-cols-2">

                        <div>

                            <label for="department" class="label">
                                Department
                            </label>

                            <input
                                id="department"
                                type="text"
                                name="department"
                                value="<?php echo e(old('department')); ?>"
                                placeholder="Administration"
                                class="input">

                        </div>


                        <div>

                            <label for="job_title" class="label">
                                Job Title
                            </label>

                            <input
                                id="job_title"
                                type="text"
                                name="job_title"
                                value="<?php echo e(old('job_title')); ?>"
                                placeholder="Officer"
                                class="input">

                        </div>

                    </div>


                    <div>

                        <label for="phone" class="label">
                            Contact Number
                        </label>

                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="<?php echo e(old('phone')); ?>"
                            placeholder="09XXXXXXXXX"
                            class="input">

                    </div>


                    <div>

                        <label for="app_role" class="label">
                            System Role
                            <span class="text-error">*</span>
                        </label>

                        <select
                            id="app_role"
                            name="app_role"
                            class="input">

                            <?php $__currentLoopData = \App\Models\User::ROLES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option
                                    value="<?php echo e($value); ?>"
                                    <?php if(old('app_role', 'employee') === $value): echo 'selected'; endif; ?>>

                                    <?php echo e($label); ?>


                                </option>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

                    </div>


                    <div>

                        <label for="password" class="label">
                            Temporary Password
                            <span class="text-error">*</span>
                        </label>


                        <div class="relative">

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Minimum 8 characters"
                                class="input pr-20">


                            <button
                                type="button"
                                id="toggleStaffPassword"
                                class="absolute inset-y-0 right-3 text-xs font-semibold text-primary">

                                Show

                            </button>

                        </div>


                        <p class="mt-1.5 text-xs text-slate-400">
                            The staff member must replace this temporary password after signing in.
                        </p>


                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    <button
                        type="submit"
                        class="btn-secondary w-full">

                        Create Account

                    </button>

                </form>

            </div>

        </div>


        
        <div class="min-w-0 space-y-4">

            <div>

                <h3 class="font-heading text-lg font-semibold text-primary">
                    Staff Directory
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Search staff, review account status, and manage system roles.
                </p>

            </div>


            
<form
    id="staffFilterForm"
    method="GET"
    action="<?php echo e(route('users.index')); ?>"
    class="card grid gap-3 p-4 md:grid-cols-[1fr_190px_170px_auto]">

    
    <div class="relative">

        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-4.35-4.35M19 11a8 8 0 11-16 0 8 8 0 0116 0z" />

            </svg>

        </div>

        <input
            id="staffSearch"
            type="search"
            name="search"
            value="<?php echo e(request('search')); ?>"
            placeholder="Search staff..."
            autocomplete="off"
            class="input pl-10 pr-10">

        <div
            id="staffSearchIndicator"
            class="pointer-events-none absolute inset-y-0 right-0 hidden items-center pr-3">

            <svg
                class="h-4 w-4 animate-spin text-accent"
                fill="none"
                viewBox="0 0 24 24">

                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4">
                </circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>

            </svg>

        </div>

    </div>


    
    <select
        id="staffRoleFilter"
        name="role"
        onchange="this.form.submit()"
        class="input">

        <option value="">
            All Roles
        </option>

        <?php $__currentLoopData = \App\Models\User::ROLES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <option
                value="<?php echo e($value); ?>"
                <?php if(request('role') === $value): echo 'selected'; endif; ?>>

                <?php echo e($label); ?>


            </option>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </select>


    
    <select
        id="staffStatusFilter"
        name="status"
        onchange="this.form.submit()"
        class="input">

        <option value="">
            All Statuses
        </option>

        <option
            value="active"
            <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>

            Active

        </option>

        <option
            value="inactive"
            <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>

            Deactivated

        </option>

    </select>


    
    <a
        href="<?php echo e(route('users.index')); ?>"
        class="btn-outline justify-center">

        Clear

    </a>

</form>



            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header">

                            <tr>

                                <th class="px-5 py-4 font-medium">
                                    Staff Member
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Role
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Password
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-right font-medium">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            <?php $__empty_1 = true; $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                <tr class="transition hover:bg-sky-50/40">

                                    
                                    <td class="px-5 py-4">

                                        <div class="flex min-w-[230px] items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 font-heading text-sm font-bold text-primary">

                                                <?php echo e(strtoupper(substr($person->full_name ?: $person->email, 0, 1))); ?>


                                            </div>


                                            <div class="min-w-0">

                                                <div class="flex items-center gap-2">

                                                    <p class="max-w-[220px] truncate font-button text-sm font-semibold text-primary">
                                                        <?php echo e($person->full_name); ?>

                                                    </p>


                                                    <?php if($person->id === auth()->id()): ?>

                                                        <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wide text-primary">
                                                            You
                                                        </span>

                                                    <?php endif; ?>

                                                </div>


                                                <p class="mt-0.5 max-w-[240px] truncate text-xs text-slate-500">
                                                    <?php echo e($person->email); ?>

                                                </p>


                                                <?php if($person->job_title || $person->department): ?>

                                                    <p class="mt-1 max-w-[240px] truncate text-[10px] text-slate-400">

                                                        <?php echo e($person->job_title ?: 'Staff'); ?>


                                                        <?php if($person->department): ?>
                                                            · <?php echo e($person->department); ?>

                                                        <?php endif; ?>

                                                    </p>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php if($person->id === auth()->id()): ?>

                                            <span class="badge badge-info">
                                                <?php echo e(\App\Models\User::ROLES[$person->app_role] ?? str($person->app_role)->headline()); ?>

                                            </span>

                                        <?php else: ?>

                                            <form
                                                method="POST"
                                                action="<?php echo e(route('users.set-role', $person)); ?>"
                                                class="min-w-[180px]">

                                                <?php echo csrf_field(); ?>

                                                <select
                                                    name="app_role"
                                                    onchange="if(confirm('Change this staff member''s system role?')) this.form.submit(); else this.value='<?php echo e($person->app_role); ?>';"
                                                    class="input py-2 text-xs">

                                                    <?php $__currentLoopData = \App\Models\User::ROLES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                        <option
                                                            value="<?php echo e($value); ?>"
                                                            <?php if($person->app_role === $value): echo 'selected'; endif; ?>>

                                                            <?php echo e($label); ?>


                                                        </option>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </select>

                                            </form>

                                        <?php endif; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php if($person->force_password_change): ?>

                                            <span class="badge badge-warning">
                                                Change Required
                                            </span>

                                        <?php else: ?>

                                            <span class="badge badge-success">
                                                Password Set
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php if($person->is_active): ?>

                                            <span class="badge badge-success">
                                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                Active
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                                Deactivated
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4 text-right">

                                        <?php if($person->id === auth()->id()): ?>

                                            <span class="text-xs text-slate-400">
                                                Current account
                                            </span>

                                        <?php else: ?>

                                            <form
                                                method="POST"
                                                action="<?php echo e(route('users.toggle-active', $person)); ?>"
                                                onsubmit="return confirm('<?php echo e($person->is_active ? 'Deactivate this staff account?' : 'Reactivate this staff account?'); ?>');">

                                                <?php echo csrf_field(); ?>

                                                <?php if($person->is_active): ?>

                                                    <button
                                                        type="submit"
                                                        class="inline-flex rounded-lg border border-error/20 bg-error/5 px-3 py-2 font-button text-xs font-semibold text-error transition hover:bg-error hover:text-white">

                                                        Deactivate

                                                    </button>

                                                <?php else: ?>

                                                    <button
                                                        type="submit"
                                                        class="inline-flex rounded-lg bg-success/10 px-3 py-2 font-button text-xs font-semibold text-success transition hover:bg-success hover:text-white">

                                                        Reactivate

                                                    </button>

                                                <?php endif; ?>

                                            </form>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                <tr>

                                    <td colspan="5" class="px-6 py-16">

                                        <div class="mx-auto max-w-sm text-center">

                                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                                                <svg
                                                    class="h-7 w-7"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8" />

                                                </svg>

                                            </div>

                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No staff accounts found
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">
                                                No users match the selected filters.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <?php if($staff->hasPages()): ?>

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    <?php echo e($staff->withQueryString()->links()); ?>

                </div>

            <?php endif; ?>

        </div>

    </div>


    
    <div class="rounded-xl border border-warning/30 bg-warning/5 px-4 py-3">

        <p class="text-xs leading-5 text-slate-600">

            <span class="font-semibold text-primary">
                Account security:
            </span>

            Newly created staff accounts receive a temporary password and are required to change it after signing in. Role and account-status changes are recorded in the Audit Trail.

        </p>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Temporary Password Visibility
    |--------------------------------------------------------------------------
    */

    const password = document.getElementById('password');
    const passwordToggle = document.getElementById('toggleStaffPassword');

    if (password && passwordToggle) {

        passwordToggle.addEventListener('click', function () {

            const hidden = password.type === 'password';

            password.type = hidden ? 'text' : 'password';

            passwordToggle.textContent = hidden
                ? 'Hide'
                : 'Show';

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Automatic Staff Search
    |--------------------------------------------------------------------------
    */

    const filterForm = document.getElementById('staffFilterForm');
    const searchInput = document.getElementById('staffSearch');
    const searchIndicator = document.getElementById('staffSearchIndicator');

    let searchTimer;

    if (filterForm && searchInput) {

        searchInput.addEventListener('input', function () {

            clearTimeout(searchTimer);

            if (searchIndicator) {
                searchIndicator.classList.remove('hidden');
                searchIndicator.classList.add('flex');
            }

            searchTimer = setTimeout(function () {

                filterForm.submit();

            }, 500);

        });


        /*
         * Pressing Enter searches immediately.
         */
        searchInput.addEventListener('keydown', function (event) {

            if (event.key === 'Enter') {

                event.preventDefault();

                clearTimeout(searchTimer);

                filterForm.submit();

            }

        });


        /*
         * Clicking the browser search-field X also
         * automatically refreshes the directory.
         */
        searchInput.addEventListener('search', function () {

            clearTimeout(searchTimer);

            filterForm.submit();

        });

    }

});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\marcj\Documents\fams-laravel-backup\resources\views/users/index.blade.php ENDPATH**/ ?>