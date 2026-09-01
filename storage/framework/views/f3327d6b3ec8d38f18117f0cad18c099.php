<?php $__env->startSection('title', 'Facilities'); ?>

<?php $__env->startSection('content'); ?>

<div class="space-y-6">

    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Facilities Management
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Manage meeting rooms, shared spaces, and other facilities available for reservation.
            </p>
        </div>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageFacilities')): ?>

            <a
                href="<?php echo e(route('facilities.create')); ?>"
                class="btn-secondary shrink-0">

                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4" />

                </svg>

                Add Facility

            </a>

        <?php endif; ?>

    </div>


    
    <div class="flex flex-col gap-3 rounded-2xl border border-accent/20 bg-accent/5 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex items-start gap-3">

            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-accent">

                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5M9 8h1M14 8h1M9 11h1M14 11h1" />

                </svg>

            </div>

            <div>
                <p class="font-button text-sm font-semibold text-primary">
                    Facility Directory
                </p>

                <p class="mt-0.5 text-xs text-slate-500">
                    Showing registered facilities and their current availability status.
                </p>
            </div>

        </div>

        <div class="text-xs font-medium text-slate-500">
            <?php echo e($facilities->total()); ?>

            <?php echo e(\Illuminate\Support\Str::plural('facility', $facilities->total())); ?>

        </div>

    </div>


    
    <div class="table-shell">

        <div class="overflow-x-auto">

            <table class="min-w-full text-left text-sm">

                <thead class="table-header">

                    <tr>
                        <th class="px-6 py-4 font-medium">
                            Facility
                        </th>

                        <th class="px-6 py-4 font-medium">
                            Type
                        </th>

                        <th class="px-6 py-4 font-medium">
                            Location
                        </th>

                        <th class="px-6 py-4 font-medium">
                            Capacity
                        </th>

                        <th class="px-6 py-4 font-medium">
                            Status
                        </th>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageFacilities')): ?>
                            <th class="px-6 py-4 text-right font-medium">
                                Actions
                            </th>
                        <?php endif; ?>
                    </tr>

                </thead>


                <tbody class="divide-y divide-border bg-card">

                    <?php $__empty_1 = true; $__currentLoopData = $facilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr class="transition-colors hover:bg-sky-50/40">

                            
                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                                        </svg>

                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate font-button text-sm font-semibold text-primary">
                                            <?php echo e($facility->name); ?>

                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-400">
                                            Facility #<?php echo e($facility->id); ?>

                                        </p>

                                    </div>

                                </div>

                            </td>


                            
                            <td class="px-6 py-4">

                                <span class="inline-flex rounded-lg bg-primary/5 px-2.5 py-1 text-xs font-medium text-primary">
                                    <?php echo e(str($facility->facility_type)->headline()); ?>

                                </span>

                            </td>


                            
                            <td class="px-6 py-4 text-slate-600">

                                <div class="flex items-center gap-2">

                                    <svg
                                        class="h-4 w-4 shrink-0 text-accent"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />

                                    </svg>

                                    <span>
                                        <?php echo e($facility->location ?: 'Not specified'); ?>

                                    </span>

                                </div>

                            </td>


                            
                            <td class="px-6 py-4">

                                <div class="flex items-center gap-2 text-slate-600">

                                    <svg
                                        class="h-4 w-4 text-slate-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5 5 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />

                                    </svg>

                                    <span class="font-medium">
                                        <?php echo e($facility->capacity); ?>

                                    </span>

                                    <span class="text-xs text-slate-400">
                                        people
                                    </span>

                                </div>

                            </td>


                            
                            <td class="px-6 py-4">

                                <?php switch($facility->status):

                                    case ('available'): ?>

                                        <span class="badge badge-success">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                            Available
                                        </span>

                                        <?php break; ?>


                                    <?php case ('maintenance'): ?>

                                        <span class="badge badge-warning">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-warning"></span>
                                            Maintenance
                                        </span>

                                        <?php break; ?>


                                    <?php case ('unavailable'): ?>

                                        <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Unavailable
                                        </span>

                                        <?php break; ?>


                                    <?php case ('archived'): ?>

                                        <span class="badge bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Archived
                                        </span>

                                        <?php break; ?>


                                    <?php default: ?>

                                        <span class="badge badge-info">
                                            <?php echo e(str($facility->status)->headline()); ?>

                                        </span>

                                <?php endswitch; ?>

                            </td>


                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageFacilities')): ?>

                                <td class="px-6 py-4 text-right">

                                    <a
                                        href="<?php echo e(route('facilities.edit', $facility)); ?>"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-white px-3 py-2 font-button text-xs font-medium text-primary transition hover:border-accent hover:bg-accent/5">

                                        <svg
                                            class="h-3.5 w-3.5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 13H9v-2.828l6.586-6.586z" />

                                        </svg>

                                        Edit

                                    </a>

                                </td>

                            <?php endif; ?>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-16 text-center">

                                <div class="mx-auto flex max-w-sm flex-col items-center">

                                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                                        <svg
                                            class="h-7 w-7"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 21h18M5 21V5l7-3 7 3v16M9 21v-5h6v5" />

                                        </svg>

                                    </div>

                                    <h3 class="font-heading text-base font-semibold text-primary">
                                        No facilities found
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Facilities added to the system will appear here.
                                    </p>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageFacilities')): ?>

                                        <a
                                            href="<?php echo e(route('facilities.create')); ?>"
                                            class="btn-secondary mt-5">

                                            Add your first facility

                                        </a>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    
    <?php if($facilities->hasPages()): ?>

        <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
            <?php echo e($facilities->links()); ?>

        </div>

    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\marcj\Documents\fams-laravel-backup\resources\views/facilities/index.blade.php ENDPATH**/ ?>