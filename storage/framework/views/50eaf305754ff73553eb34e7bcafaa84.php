<?php $__env->startSection('title', 'Appointments'); ?>

<?php $__env->startSection('content'); ?>

<div class="space-y-6">

    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Visitor Appointments
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Schedule visitor appointments and monitor upcoming visits across the organization.
            </p>
        </div>


        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

            <p class="text-xs text-slate-500">
                Total Appointments
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                <?php echo e($appointments->total()); ?>

            </p>

        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[370px_minmax(0,1fr)]">

        
        <div>

            <div class="card overflow-hidden xl:sticky xl:top-6">

                
                <div class="border-b border-border bg-background/60 px-5 py-5">

                    <div class="flex items-center gap-3">

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
                                    d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                            </svg>

                        </div>


                        <div>

                            <h3 class="font-heading text-base font-semibold text-primary">
                                Schedule a Visit
                            </h3>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Register a visitor appointment in advance.
                            </p>

                        </div>

                    </div>

                </div>


                <form
                    method="POST"
                    action="<?php echo e(route('appointments.store')); ?>"
                    class="space-y-5 p-5">

                    <?php echo csrf_field(); ?>


                    
                    <div>

                        <label for="visitor_name" class="label">
                            Visitor Name
                            <span class="text-error">*</span>
                        </label>

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
                                        d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                                </svg>

                            </div>

                            <input
                                id="visitor_name"
                                type="text"
                                name="visitor_name"
                                value="<?php echo e(old('visitor_name')); ?>"
                                required
                                placeholder="Full name of visitor"
                                class="input pl-10 <?php $__errorArgs = ['visitor_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                        </div>

                        <?php $__errorArgs = ['visitor_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label for="visitor_organization" class="label">
                            Organization
                        </label>

                        <input
                            id="visitor_organization"
                            type="text"
                            name="visitor_organization"
                            value="<?php echo e(old('visitor_organization')); ?>"
                            placeholder="Company or organization"
                            class="input <?php $__errorArgs = ['visitor_organization'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                        <?php $__errorArgs = ['visitor_organization'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label for="visitor_type" class="label">
                            Visitor Type
                        </label>

                        <select
                            id="visitor_type"
                            name="visitor_type"
                            class="input <?php $__errorArgs = ['visitor_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                            <?php $__currentLoopData = [
                                'customer',
                                'business_partner',
                                'supplier',
                                'government',
                                'applicant',
                                'guest',
                                'other'
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option
                                    value="<?php echo e($type); ?>"
                                    <?php if(old('visitor_type', 'guest') === $type): echo 'selected'; endif; ?>>

                                    <?php echo e(str($type)->headline()); ?>


                                </option>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

                        <?php $__errorArgs = ['visitor_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label for="host_email" class="label">
                            Host Email
                        </label>

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
                                        d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />

                                </svg>

                            </div>

                            <input
                                id="host_email"
                                type="email"
                                name="host_email"
                                value="<?php echo e(old('host_email')); ?>"
                                placeholder="host@example.com"
                                class="input pl-10 <?php $__errorArgs = ['host_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                        </div>

                        <?php $__errorArgs = ['host_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label for="date" class="label">
                            Appointment Date
                            <span class="text-error">*</span>
                        </label>

                        <input
                            id="date"
                            type="date"
                            name="date"
                            value="<?php echo e(old('date')); ?>"
                            required
                            class="input <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                        <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label class="label">
                            Appointment Time
                        </label>

                        <div class="grid grid-cols-2 gap-3">

                            <div>

                                <label
                                    for="start_time"
                                    class="mb-1 block text-xs text-slate-400">

                                    Start
                                    <span class="text-error">*</span>

                                </label>

                                <input
                                    id="start_time"
                                    type="time"
                                    name="start_time"
                                    value="<?php echo e(old('start_time')); ?>"
                                    required
                                    class="input <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                            </div>


                            <div>

                                <label
                                    for="end_time"
                                    class="mb-1 block text-xs text-slate-400">

                                    End

                                </label>

                                <input
                                    id="end_time"
                                    type="time"
                                    name="end_time"
                                    value="<?php echo e(old('end_time')); ?>"
                                    class="input <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                            </div>

                        </div>

                        <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    
                    <div>

                        <label for="facility_id" class="label">
                            Room / Facility
                        </label>

                        <select
                            id="facility_id"
                            name="facility_id"
                            class="input <?php $__errorArgs = ['facility_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error focus:border-error focus:ring-error/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                            <option value="">
                                &mdash; None &mdash;
                            </option>

                            <?php $__currentLoopData = $facilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option
                                    value="<?php echo e($facility->id); ?>"
                                    <?php if(old('facility_id') == $facility->id): echo 'selected'; endif; ?>>

                                    <?php echo e($facility->name); ?>


                                </option>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

                        <p class="mt-1.5 text-xs text-slate-400">
                            Optional meeting room or facility for the appointment.
                        </p>

                        <?php $__errorArgs = ['facility_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs font-medium text-error">
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>


                    <input
                        type="hidden"
                        name="status"
                        value="scheduled">


                    <button
                        type="submit"
                        class="btn-secondary w-full">

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

                        Schedule Appointment

                    </button>

                </form>

            </div>

        </div>


        
        <div class="min-w-0 space-y-4">

            <div>

                <h3 class="font-heading text-lg font-semibold text-primary">
                    Appointment Schedule
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    View scheduled, completed, and cancelled visitor appointments.
                </p>

            </div>


            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header">

                            <tr>

                                <th class="px-5 py-4 font-medium">
                                    Visitor
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Host
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Schedule
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-right font-medium">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                <tr class="transition-colors hover:bg-sky-50/40">

                                    
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-accent/10 font-button text-sm font-bold uppercase text-primary">

                                                <?php echo e(\Illuminate\Support\Str::substr($appointment->visitor_name, 0, 1)); ?>


                                            </div>


                                            <div class="min-w-0">

                                                <p class="max-w-[180px] truncate font-button text-sm font-semibold text-primary">
                                                    <?php echo e($appointment->visitor_name); ?>

                                                </p>


                                                <?php if($appointment->visitor_organization): ?>

                                                    <p class="mt-0.5 max-w-[180px] truncate text-xs text-slate-400">
                                                        <?php echo e($appointment->visitor_organization); ?>

                                                    </p>

                                                <?php elseif($appointment->visitor_type): ?>

                                                    <p class="mt-0.5 text-xs text-slate-400">
                                                        <?php echo e(str($appointment->visitor_type)->headline()); ?>

                                                    </p>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-2 text-slate-600">

                                            <svg
                                                class="h-4 w-4 shrink-0 text-slate-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                                            </svg>

                                            <span class="max-w-[180px] truncate">
                                                <?php echo e($appointment->host_name ?: ($appointment->host_email ?: 'Not assigned')); ?>

                                            </span>

                                        </div>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <div class="space-y-1">

                                            <div class="flex items-center gap-2 font-medium text-slate-700">

                                                <svg
                                                    class="h-4 w-4 shrink-0 text-accent"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                                                </svg>

                                                <?php echo e($appointment->date->format('M d, Y')); ?>


                                            </div>


                                            <div class="flex items-center gap-2 pl-6 text-xs text-slate-500">

                                                <?php echo e($appointment->start_time); ?>


                                                <?php if($appointment->end_time): ?>
                                                    &ndash; <?php echo e($appointment->end_time); ?>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php switch($appointment->status):

                                            case ('scheduled'): ?>

                                                <span class="badge badge-info">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-accent"></span>
                                                    Scheduled
                                                </span>

                                                <?php break; ?>


                                            <?php case ('confirmed'): ?>

                                                <span class="badge badge-success">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                    Confirmed
                                                </span>

                                                <?php break; ?>


                                            <?php case ('checked_in'): ?>

                                                <span class="badge badge-success">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                    Checked In
                                                </span>

                                                <?php break; ?>


                                            <?php case ('completed'): ?>

                                                <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                                    Completed
                                                </span>

                                                <?php break; ?>


                                            <?php case ('cancelled'): ?>

                                                <span class="badge badge-error">
                                                    Cancelled
                                                </span>

                                                <?php break; ?>


                                            <?php default: ?>

                                                <span class="badge badge-info">
                                                    <?php echo e(str($appointment->status)->headline()); ?>

                                                </span>

                                        <?php endswitch; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4 text-right">

                                        <?php if(! in_array($appointment->status, ['cancelled', 'completed'])): ?>

                                            <form
                                                method="POST"
                                                action="<?php echo e(route('appointments.cancel', $appointment)); ?>"
                                                class="inline"
                                                onsubmit="return confirm('Cancel this appointment?');">

                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>


                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-border bg-white px-3 py-2 font-button text-xs font-medium text-slate-500 transition hover:border-error/30 hover:bg-error/5 hover:text-error">

                                                    <svg
                                                        class="h-3.5 w-3.5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />

                                                    </svg>

                                                    Cancel

                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <span class="text-xs text-slate-400">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                <tr>

                                    <td colspan="5" class="px-6 py-16">

                                        <div class="mx-auto flex max-w-sm flex-col items-center text-center">

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
                                                        d="M8 7V3M16 7V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

                                                </svg>

                                            </div>


                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No appointments scheduled
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Visitor appointments will appear here once scheduled.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <?php if($appointments->hasPages()): ?>

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    <?php echo e($appointments->links()); ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\marcj\Documents\fams-laravel-backup\resources\views/appointments/index.blade.php ENDPATH**/ ?>