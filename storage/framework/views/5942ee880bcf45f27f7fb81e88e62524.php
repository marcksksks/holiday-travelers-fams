<?php $__env->startSection('title', 'Change Password'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $isForced = $forced ?? false;
?>


<?php if(!$isForced): ?>

    <div class="mx-auto max-w-3xl">

        
        <div class="mb-6">

            <p class="text-sm leading-relaxed text-slate-500">
                Manage your account security and update your current password.
            </p>

        </div>

<?php endif; ?>


<div class="<?php echo e($isForced ? 'overflow-hidden rounded-2xl border border-border bg-card shadow-soft' : 'max-w-xl overflow-hidden rounded-2xl border border-border bg-card shadow-card'); ?>">

    
    <div class="border-b border-border px-6 pb-6 pt-7 sm:px-8">

        <div class="flex items-start gap-4">

            
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 11c1.105 0 2 .895 2 2v2a2 2 0 11-4 0v-2c0-1.105.895-2 2-2zm6 0V8a6 6 0 10-12 0v3m-1 0h14a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1v-8a1 1 0 011-1z" />

                </svg>

            </div>


            <div class="min-w-0">

                <h2 class="font-heading text-xl font-bold text-primary">

                    <?php if($isForced): ?>
                        Change Your Password
                    <?php else: ?>
                        Account Security
                    <?php endif; ?>

                </h2>

                <p class="mt-1 text-sm leading-relaxed text-slate-500">

                    <?php if($isForced): ?>
                        Your administrator requires you to set a new password before continuing.
                    <?php else: ?>
                        Update your password to help keep your Holiday Travelers Travel & Tours Inc. account secure.
                    <?php endif; ?>

                </p>

            </div>

        </div>

    </div>


    
    <form
        method="POST"
        action="<?php echo e(route('password.change.update')); ?>"
        class="space-y-5 p-6 sm:p-8">

        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>


        
        <div>

            <label for="current_password" class="label">
                Current Password
            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 11c1.105 0 2 .895 2 2v2a2 2 0 11-4 0v-2c0-1.105.895-2 2-2zm6 0V8a6 6 0 10-12 0v3m-1 0h14a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1v-8a1 1 0 011-1z" />

                    </svg>

                </div>

                <input
                    id="current_password"
                    type="password"
                    name="current_password"
                    required
                    autofocus
                    autocomplete="current-password"
                    placeholder="Enter your current password"
                    class="input pl-11 pr-11">

                <button
                    type="button"
                    data-password-toggle="current_password"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-primary"
                    aria-label="Show current password"
                    title="Show password">

                    <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.269 2.943 9.542 7-1.273 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 4.24A9.7 9.7 0 0112 4c4.5 0 8.27 2.94 9.54 7a11.3 11.3 0 01-2.2 3.87M6.23 6.23A11.5 11.5 0 002.46 11c1.27 4.06 5.06 7 9.54 7 1.28 0 2.5-.24 3.62-.68" />
                    </svg>

                </button>

            </div>

        </div>


        
        <div>

            <label for="password" class="label">
                New Password
            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />

                    </svg>

                </div>

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Create a new password"
                    class="input pl-11 pr-11">

                <button
                    type="button"
                    data-password-toggle="password"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-primary"
                    aria-label="Show new password"
                    title="Show password">

                    <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.269 2.943 9.542 7-1.273 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 4.24A9.7 9.7 0 0112 4c4.5 0 8.27 2.94 9.54 7a11.3 11.3 0 01-2.2 3.87M6.23 6.23A11.5 11.5 0 002.46 11c1.27 4.06 5.06 7 9.54 7 1.28 0 2.5-.24 3.62-.68" />
                    </svg>

                </button>

            </div>

        </div>


        
        <div class="-mt-2">

            <div class="mb-1.5 flex items-center justify-between">
                <span class="text-xs text-slate-500">
                    Password strength
                </span>

                <span
                    id="password-strength-text"
                    class="text-xs font-semibold text-slate-400">
                    Not entered
                </span>
            </div>

            <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                <div
                    id="password-strength-bar"
                    class="h-full w-0 rounded-full bg-slate-300 transition-all duration-300">
                </div>
            </div>

            <p class="mt-1.5 text-[11px] leading-relaxed text-slate-400">
                Use at least 8 characters and combine uppercase, lowercase, numbers, and symbols.
            </p>

        </div>


        
        <div>

            <label for="password_confirmation" class="label">
                Confirm New Password
            </label>

            <div class="relative">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </div>

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Re-enter your new password"
                    class="input pl-11 pr-11">

                <button
                    type="button"
                    data-password-toggle="password_confirmation"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-primary"
                    aria-label="Show password confirmation"
                    title="Show password">

                    <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.269 2.943 9.542 7-1.273 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 4.24A9.7 9.7 0 0112 4c4.5 0 8.27 2.94 9.54 7a11.3 11.3 0 01-2.2 3.87M6.23 6.23A11.5 11.5 0 002.46 11c1.27 4.06 5.06 7 9.54 7 1.28 0 2.5-.24 3.62-.68" />
                    </svg>

                </button>

            </div>

        </div>


        
        <div class="flex items-start gap-3 rounded-xl border border-accent/20 bg-accent/10 px-4 py-3">

            <svg
                class="mt-0.5 h-5 w-5 shrink-0 text-accent"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

            </svg>

            <div class="text-xs leading-relaxed text-slate-600">

                <p class="font-medium text-primary">
                    Password security
                </p>

                <p class="mt-0.5">
                    Use a strong password that is different from passwords used on your other accounts.
                </p>

            </div>

        </div>


        
        <div class="pt-1">

            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 font-button text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition-all duration-200 hover:bg-[#E08A3B] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-secondary/40 focus:ring-offset-2">

                Update Password

                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7" />

                </svg>

            </button>

        </div>

    </form>

</div>


<?php if(!$isForced): ?>

    </div>

<?php else: ?>

    <p class="mt-5 text-center text-xs text-slate-400">
        Secure account management · Holiday Travelers Travel & Tours Inc.
    </p>

<?php endif; ?>



<script>
document.addEventListener('DOMContentLoaded', () => {

    // Show / hide password fields
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {

        button.addEventListener('click', () => {

            const inputId = button.getAttribute('data-password-toggle');
            const input = document.getElementById(inputId);

            if (!input) return;

            const showing = input.type === 'text';

            input.type = showing ? 'password' : 'text';

            const eyeOpen = button.querySelector('[data-eye-open]');
            const eyeClosed = button.querySelector('[data-eye-closed]');

            eyeOpen?.classList.toggle('hidden', !showing);
            eyeClosed?.classList.toggle('hidden', showing);

            button.setAttribute(
                'aria-label',
                showing ? 'Show password' : 'Hide password'
            );

            button.setAttribute(
                'title',
                showing ? 'Show password' : 'Hide password'
            );

        });

    });


    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');

    if (passwordInput && strengthBar && strengthText) {

        passwordInput.addEventListener('input', () => {

            const value = passwordInput.value;

            if (!value) {
                strengthBar.style.width = '0%';
                strengthBar.className =
                    'h-full w-0 rounded-full bg-slate-300 transition-all duration-300';

                strengthText.textContent = 'Not entered';
                strengthText.className =
                    'text-xs font-semibold text-slate-400';

                return;
            }

            let score = 0;

            if (value.length >= 8) score++;
            if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score++;
            if (/\d/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;

            if (score <= 1) {
                strengthBar.style.width = '25%';
                strengthBar.className =
                    'h-full rounded-full bg-error transition-all duration-300';

                strengthText.textContent = 'Weak';
                strengthText.className =
                    'text-xs font-semibold text-error';
            }
            else if (score === 2) {
                strengthBar.style.width = '50%';
                strengthBar.className =
                    'h-full rounded-full bg-warning transition-all duration-300';

                strengthText.textContent = 'Fair';
                strengthText.className =
                    'text-xs font-semibold text-amber-600';
            }
            else if (score === 3) {
                strengthBar.style.width = '75%';
                strengthBar.className =
                    'h-full rounded-full bg-accent transition-all duration-300';

                strengthText.textContent = 'Good';
                strengthText.className =
                    'text-xs font-semibold text-accent';
            }
            else {
                strengthBar.style.width = '100%';
                strengthBar.className =
                    'h-full rounded-full bg-success transition-all duration-300';

                strengthText.textContent = 'Strong';
                strengthText.className =
                    'text-xs font-semibold text-success';
            }

        });

    }

});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make(($forced ?? false) ? 'layouts.guest' : 'layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\marcj\Documents\fams-laravel-backup\resources\views/auth/change-password.blade.php ENDPATH**/ ?>