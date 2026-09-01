<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> · Holiday Travelers Travel & Tours Inc.</title>

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="bg-background font-body text-slate-700 antialiased">

<?php
    $navItems = [
        'dashboard' => ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
        'facilities.index' => ['label' => 'Facilities', 'route' => 'facilities.index', 'icon' => 'facilities'],
        'reservations.index' => ['label' => 'Reservations', 'route' => 'reservations.index', 'icon' => 'reservations'],
        'appointments.index' => ['label' => 'Appointments', 'route' => 'appointments.index', 'icon' => 'appointments'],
        'visitors.index' => ['label' => 'Visitor Desk', 'route' => 'visitors.index', 'icon' => 'visitors'],
        'documents.index' => ['label' => 'Records Archive', 'route' => 'documents.index', 'icon' => 'documents'],
        'legal.index' => ['label' => 'Legal Records', 'route' => 'legal.index', 'icon' => 'legal'],
        'contracts.index' => ['label' => 'Contracts', 'route' => 'contracts.index', 'icon' => 'contracts'],
        'retention.index' => ['label' => 'Retention', 'route' => 'retention.index', 'icon' => 'retention'],
        'reports.index' => ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'reports'],
        'audit-trail.index' => ['label' => 'Audit Trail', 'route' => 'audit-trail.index', 'icon' => 'audit'],
        'users.index' => ['label' => 'Staff Accounts', 'route' => 'users.index', 'icon' => 'users'],
    ];

    $allowedNav = \App\Support\Rbac::navFor(auth()->user()->app_role ?? null);
?>

<div class="flex min-h-screen">

    
    <aside
        data-sidebar
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-primary text-white transition-all duration-300 md:sticky md:top-0 md:bottom-auto md:h-screen md:self-start">

        
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-5">

            <a
    href="<?php echo e(route('dashboard')); ?>"
    class="block min-w-0 overflow-hidden">

    <div data-sidebar-label class="min-w-0">

        <div class="whitespace-nowrap font-heading text-[20px] font-bold leading-tight tracking-tight text-white">
            Holiday Travelers
        </div>

        <div class="mt-1 whitespace-nowrap font-heading text-[12px] font-medium tracking-wide text-secondary">
            Travel & Tours Inc.
        </div>

    </div>

</a>

            
            <button
                type="button"
                data-mobile-menu
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-800 hover:text-white md:hidden"
                aria-label="Close navigation">

                ✕

            </button>

        </div>

        
        <nav class="flex-1 space-y-2 overflow-y-auto px-4 py-6">

            <p data-sidebar-label
               class="mb-3 px-2 font-button text-[10px] font-semibold uppercase tracking-[0.18em] text-accent">
                Main Menu
            </p>

            <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <?php if(in_array($key, $allowedNav, true)): ?>

                    <a
                        href="<?php echo e(route($item['route'])); ?>"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3 font-button text-sm font-medium transition-all duration-200
                        <?php echo e(request()->routeIs($item['route'])
                            ? 'border-l-4 border-secondary bg-white/10 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-white/5 hover:text-white'); ?>">

                        <?php if (isset($component)) { $__componentOriginalb86967db162ccc0bceeb1ac50f169b8f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb86967db162ccc0bceeb1ac50f169b8f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-icon','data' => ['name' => $item['icon']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['icon'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb86967db162ccc0bceeb1ac50f169b8f)): ?>
<?php $attributes = $__attributesOriginalb86967db162ccc0bceeb1ac50f169b8f; ?>
<?php unset($__attributesOriginalb86967db162ccc0bceeb1ac50f169b8f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb86967db162ccc0bceeb1ac50f169b8f)): ?>
<?php $component = $__componentOriginalb86967db162ccc0bceeb1ac50f169b8f; ?>
<?php unset($__componentOriginalb86967db162ccc0bceeb1ac50f169b8f); ?>
<?php endif; ?>

                        <span
                            data-sidebar-label
                            class="whitespace-nowrap">
                            <?php echo e($item['label']); ?>

                        </span>

                    </a>

                <?php endif; ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </nav>

        
        <div class="border-t border-white/10 bg-primary/50 px-4 py-4">

            <div class="flex items-center gap-3">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-secondary text-sm font-semibold text-white shadow-md">
                    <?php echo e(strtoupper(substr(auth()->user()->full_name, 0, 1))); ?>

                </div>

                <div
                    data-sidebar-label
                    class="min-w-0">

                    <p class="truncate font-button text-sm font-medium text-white">
                        <?php echo e(auth()->user()->full_name); ?>

                    </p>

                    <p class="truncate text-xs text-accent">
                        <?php echo e(\App\Models\User::ROLES[auth()->user()->app_role] ?? auth()->user()->app_role); ?>

                    </p>

                </div>

            </div>

        </div>

    </aside>


    
    <div class="flex min-w-0 flex-1 flex-col">

        
        <header
            class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-border bg-card px-4 shadow-sm md:px-8">

            <div class="flex items-center gap-3">

                
                <button
                    type="button"
                    data-sidebar-toggle
                    class="hidden rounded-lg p-2 text-primary transition hover:bg-primary/5 md:block"
                    aria-label="Toggle sidebar"
                    title="Toggle sidebar">

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                    </svg>

                </button>

                
                <button
                    type="button"
                    data-mobile-menu
                    class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden"
                    aria-label="Open navigation">

                    ☰

                </button>

                <div>

                    <h1 class="font-heading text-xl font-bold text-primary">
                        <?php echo $__env->yieldContent('title', 'Dashboard'); ?>
                    </h1>

                    <p class="hidden text-xs text-accent sm:block">
                        Facilities and Administrative Management System
                    </p>

                </div>

            </div>


            
            <div class="flex items-center gap-3">
                
                <?php
                    $notificationBaseQuery = \App\Models\AppNotification::where(
                        'recipient_email',
                        auth()->user()->email
                    );

                    $unreadNotificationCount = (clone $notificationBaseQuery)
                        ->where('is_read', false)
                        ->count();

                    $headerNotifications = (clone $notificationBaseQuery)
                        ->orderByDesc('created_at')
                        ->limit(5)
                        ->get();
                ?>

                <?php if(Route::has('notifications.index')): ?>

                    <div class="relative">

                        
                        <button
                            type="button"
                            data-notification-button
                            class="relative rounded-lg p-2 text-slate-500 transition hover:bg-accent/10 hover:text-primary"
                            aria-label="<?php echo e($unreadNotificationCount > 0 ? $unreadNotificationCount . ' unread notifications' : 'Notifications'); ?>"
                            aria-expanded="false"
                            title="Notifications">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />

                            </svg>

                            <?php if($unreadNotificationCount > 0): ?>

                                <span
                                    class="absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full border-2 border-white bg-secondary px-1 font-button text-[9px] font-bold leading-none text-white shadow-sm">

                                    <?php echo e($unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount); ?>


                                </span>

                            <?php endif; ?>

                        </button>


                        
                        <div
                            data-notification-menu
                            class="absolute right-0 z-50 mt-3 hidden w-96 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

                            
                            <div class="flex items-center justify-between border-b border-border px-5 py-4">

                                <div>
                                    <h3 class="font-heading text-sm font-semibold text-primary">
                                        Notifications
                                    </h3>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        <?php echo e($unreadNotificationCount); ?> unread
                                    </p>
                                </div>

                                <?php if($unreadNotificationCount > 0): ?>

                                    <span class="rounded-full bg-secondary/10 px-2.5 py-1 text-[10px] font-semibold text-secondary">
                                        <?php echo e($unreadNotificationCount); ?> New
                                    </span>

                                <?php endif; ?>

                            </div>


                            
                            <div class="max-h-96 overflow-y-auto">

                                <?php $__empty_1 = true; $__currentLoopData = $headerNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                    <?php
                                        $severity = strtolower($notification->severity ?? 'info');

                                        $iconClass = match ($severity) {
                                            'success' => 'bg-success/10 text-success',
                                            'warning' => 'bg-warning/10 text-amber-600',
                                            'error', 'danger' => 'bg-error/10 text-error',
                                            default => 'bg-accent/10 text-accent',
                                        };
                                    ?>

                                    <div
                                        class="border-b border-border px-5 py-4 transition hover:bg-background <?php echo e(!$notification->is_read ? 'bg-accent/5' : ''); ?>">

                                        <div class="flex gap-3">

                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl <?php echo e($iconClass); ?>">

                                                <?php if($severity === 'success'): ?>

                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M5 13l4 4L19 7" />
                                                    </svg>

                                                <?php elseif($severity === 'warning'): ?>

                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                                                    </svg>

                                                <?php elseif($severity === 'error' || $severity === 'danger'): ?>

                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M6 18L18 6M6 6l12 12" />
                                                    </svg>

                                                <?php else: ?>

                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>

                                                <?php endif; ?>

                                            </div>


                                            <div class="min-w-0 flex-1">

                                                <div class="flex items-start justify-between gap-2">

                                                    <p class="truncate font-button text-sm font-semibold text-primary">
                                                        <?php echo e($notification->title); ?>

                                                    </p>

                                                    <?php if(!$notification->is_read): ?>

                                                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-secondary"></span>

                                                    <?php endif; ?>

                                                </div>

                                                <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-500">
                                                    <?php echo e(\Illuminate\Support\Str::limit($notification->body, 100)); ?>

                                                </p>

                                                <p class="mt-2 text-[10px] text-slate-400">
                                                    <?php echo e($notification->created_at->diffForHumans()); ?>

                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                    <div class="px-6 py-10 text-center">

                                        <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-accent/10 text-accent">

                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4-5.66V5a2 2 0 10-4 0v.34A6 6 0 006 11v3.2c0 .53-.21 1.04-.59 1.41L4 17h5m6 0a3 3 0 01-6 0" />
                                            </svg>

                                        </div>

                                        <p class="font-button text-sm font-medium text-primary">
                                            No notifications
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            You're all caught up.
                                        </p>

                                    </div>

                                <?php endif; ?>

                            </div>


                            
                            <div class="bg-background px-4 py-3">

                                <a
                                    href="<?php echo e(route('notifications.index')); ?>"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 font-button text-xs font-semibold text-primary transition hover:bg-accent/10">

                                    View all notifications

                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5l7 7-7 7" />
                                    </svg>

                                </a>

                            </div>

                        </div>

                    </div>

                <?php endif; ?>



                
                <div class="relative">

                    <button
                        type="button"
                        data-profile-button
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition hover:bg-primary/5">

                        <div class="hidden text-right sm:block">

                            <p class="font-button text-sm font-medium text-primary">
                                <?php echo e(auth()->user()->full_name); ?>

                            </p>

                            <p class="text-xs text-accent">
                                <?php echo e(\App\Models\User::ROLES[auth()->user()->app_role] ?? auth()->user()->app_role); ?>

                            </p>

                        </div>

                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white shadow-sm">

                            <?php echo e(strtoupper(substr(auth()->user()->full_name, 0, 1))); ?>


                        </div>

                    </button>


                    
                    <div
                        data-profile-menu
                        class="absolute right-0 mt-2 hidden w-52 overflow-hidden rounded-2xl border border-border bg-card shadow-soft">

                        <div class="border-b border-slate-100 px-4 py-3">

                            <p class="text-sm font-medium text-slate-900">
                                <?php echo e(auth()->user()->full_name); ?>

                            </p>

                            <p class="truncate text-xs text-accent">
                                <?php echo e(auth()->user()->email); ?>

                            </p>

                        </div>

                        <?php if(Route::has('password.change')): ?>

                            <a
                                href="<?php echo e(route('password.change')); ?>"
                                class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                Change Password
                            </a>

                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('logout')); ?>">

                            <?php echo csrf_field(); ?>

                            <button
                                type="submit"
                                class="w-full px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50">
                                Log out
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </header>


        
        <div
            data-sidebar-overlay
            class="fixed inset-0 z-40 hidden bg-slate-950/50 md:hidden">
        </div>


        
        <main class="flex-1 bg-background p-4 md:p-8 lg:p-10">

            
            <?php if(session('status')): ?>

                <div
                    data-toast
                    class="mb-5 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">

                    <span>
                        <?php echo e(session('status')); ?>

                    </span>

                    <button
                        type="button"
                        data-dismiss
                        class="ml-4 text-emerald-600 hover:text-emerald-900">

                        ✕

                    </button>

                </div>

            <?php endif; ?>


            
            <?php if($errors->any()): ?>

                <div
                    data-toast
                    class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">

                    <div class="flex justify-between">

                        <p class="font-medium">
                            Please correct the following:
                        </p>

                        <button
                            type="button"
                            data-dismiss
                            class="text-red-600 hover:text-red-900">

                            ✕

                        </button>

                    </div>

                    <ul class="mt-2 list-inside list-disc">

                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <li><?php echo e($error); ?></li>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </ul>

                </div>

            <?php endif; ?>


            <?php echo $__env->yieldContent('content'); ?>

        </main>

    </div>

</div>

</body>
</html>







<?php /**PATH C:\Users\marcj\Documents\fams-laravel-backup\resources\views/layouts/app.blade.php ENDPATH**/ ?>