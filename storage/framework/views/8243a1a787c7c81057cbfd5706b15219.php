<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<div class="space-y-8">

    
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold tracking-tight text-primary">
                Welcome back, <?php echo e(auth()->user()->full_name); ?>!
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Here's what's happening across your administrative system today.
            </p>
        </div>

    </div>


    
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">


        
        <a
            href="<?php echo e(route('facilities.index')); ?>"
            class="group card relative overflow-hidden border-b-4 border-b-primary p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Available Facilities
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        <?php echo e($facilityCount); ?>

                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5M9 8h1M14 8h1M9 11h1M14 11h1" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-primary">
                <span>View facilities</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>


        
        <a
            href="<?php echo e(route('reservations.index')); ?>"
            class="group card relative overflow-hidden border-b-4 border-b-secondary p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Pending Reservations
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        <?php echo e($pendingReservations); ?>

                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-secondary/10 text-secondary">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-secondary">
                <span>Review reservations</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>


        
        <a
            href="<?php echo e(route('appointments.index')); ?>"
            class="group card relative overflow-hidden border-b-4 border-b-accent p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Today's Appointments
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        <?php echo e($todaysAppointments); ?>

                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-accent/10 text-accent">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zM8 15h3M8 18h5" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-accent">
                <span>View appointments</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>


        
        <a
            href="<?php echo e(route('visitors.index')); ?>"
            class="group card relative overflow-hidden border-b-4 border-b-success p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Checked-in Visitors
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        <?php echo e($checkedInVisitors); ?>

                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-success/10 text-success">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zM17 11h4M19 9v4" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-success">
                <span>Open visitor desk</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>


        
        <a
            href="<?php echo e(route('contracts.index')); ?>"
            class="group card relative overflow-hidden border-b-4 border-b-warning p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Contracts Expiring Soon
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        <?php echo e($contractsExpiringSoon); ?>

                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-warning/10 text-amber-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 3h8l3 3v15H5V3h3zM8 3v5h8V3M8 13h8M8 17h6" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-amber-600">
                <span>View contracts</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>


        
        <a
            href="<?php echo e(route('legal.index')); ?>"
            class="group card relative overflow-hidden border-b-4 border-b-error p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-soft">

            <div class="flex items-start justify-between gap-3">

                <div>
                    <p class="text-xs font-medium leading-relaxed text-slate-500">
                        Legal Action Required
                    </p>

                    <p class="mt-2 font-heading text-3xl font-bold text-primary">
                        <?php echo e($legalActionRequired); ?>

                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-error/10 text-error">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 3v18M5 7h14M7 7l-3 6h6L7 7zM17 7l-3 6h6l-3-6zM5 21h14" />
                    </svg>

                </div>

            </div>

            <div class="mt-5 flex items-center gap-1 text-xs font-medium text-error">
                <span>Review legal records</span>
                <span class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
            </div>

        </a>

    </div>


    
    <div class="grid gap-6 lg:grid-cols-2">


        
        <section class="card overflow-hidden">

            <div class="flex items-center justify-between gap-4 border-b border-border px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                        </svg>

                    </div>

                    <div>
                        <h2 class="font-heading text-base font-semibold text-primary">
                            Upcoming Approved Reservations
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Recently approved facility bookings
                        </p>
                    </div>

                </div>

                <a
                    href="<?php echo e(route('reservations.index')); ?>"
                    class="group flex shrink-0 items-center gap-1 text-xs font-medium text-primary hover:text-secondary">

                    View all

                    <span class="transition-transform group-hover:translate-x-1">
                        &rarr;
                    </span>

                </a>

            </div>


            <ul class="divide-y divide-border">

                <?php $__empty_1 = true; $__currentLoopData = $upcomingReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                    <li class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-background">

                        <div class="min-w-0">

                            <p class="truncate font-button text-sm font-medium text-primary">
                                <?php echo e($reservation->facility_name); ?>

                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                <?php echo e($reservation->date->format('M d, Y')); ?>

                                &middot;
                                <?php echo e($reservation->start_time); ?>&ndash;<?php echo e($reservation->end_time); ?>

                            </p>

                        </div>

                        <span class="badge badge-success shrink-0">
                            Approved
                        </span>

                    </li>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                    <li class="px-6 py-10">

                        <div class="flex flex-col items-center justify-center text-center">

                            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-400">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                                </svg>

                            </div>

                            <p class="text-sm font-medium text-slate-400">
                                No upcoming reservations.
                            </p>

                        </div>

                    </li>

                <?php endif; ?>

            </ul>

        </section>


        
        <section class="card overflow-hidden">

            <div class="flex items-center justify-between gap-4 border-b border-border px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-accent">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zM8 15h3M8 18h5" />
                        </svg>

                    </div>

                    <div>
                        <h2 class="font-heading text-base font-semibold text-primary">
                            Today's Appointments
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Scheduled visitors for today
                        </p>
                    </div>

                </div>

                <a
                    href="<?php echo e(route('appointments.index')); ?>"
                    class="group flex shrink-0 items-center gap-1 text-xs font-medium text-primary hover:text-accent">

                    View all

                    <span class="transition-transform group-hover:translate-x-1">
                        &rarr;
                    </span>

                </a>

            </div>


            <ul class="divide-y divide-border">

                <?php $__empty_1 = true; $__currentLoopData = $recentAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                    <li class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-background">

                        <div class="min-w-0">

                            <p class="truncate font-button text-sm font-medium text-primary">
                                <?php echo e($appointment->visitor_name); ?>

                            </p>

                            <p class="mt-1 text-xs text-slate-500">

                                <?php echo e($appointment->start_time); ?>


                                <?php if($appointment->host_name): ?>
                                    &middot; Host: <?php echo e($appointment->host_name); ?>

                                <?php endif; ?>

                            </p>

                        </div>

                        <span class="badge badge-info shrink-0">
                            <?php echo e(ucfirst($appointment->status)); ?>

                        </span>

                    </li>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                    <li class="px-6 py-10">

                        <div class="flex flex-col items-center justify-center text-center">

                            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-accent/10 text-accent">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />
                                </svg>

                            </div>

                            <p class="text-sm font-medium text-slate-400">
                                No appointments scheduled today.
                            </p>

                        </div>

                    </li>

                <?php endif; ?>

            </ul>

        </section>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\marcj\Documents\fams-laravel-backup\resources\views/dashboard/index.blade.php ENDPATH**/ ?>