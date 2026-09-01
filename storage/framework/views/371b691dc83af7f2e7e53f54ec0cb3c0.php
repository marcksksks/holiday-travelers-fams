<?php $__env->startSection('title', 'Records Retention & Compliance'); ?>

<?php $__env->startSection('content'); ?>

<div class="space-y-6">

    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Records Retention & Compliance
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Monitor retention periods, compliance conditions, review schedules, and record disposition.
            </p>
        </div>

        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">
            <p class="text-xs text-slate-500">
                Active Policies
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                <?php echo e($policies->count()); ?>

            </p>
        </div>

    </div>


    
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="card relative overflow-hidden p-5">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-primary"></div>

            <p class="text-xs font-medium text-slate-500">
                Tracked Records
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-primary">
                <?php echo e($stats['total']); ?>

            </p>

            <p class="mt-1 text-xs text-slate-400">
                Records under retention tracking
            </p>
        </div>


        <div class="card relative overflow-hidden p-5">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-success"></div>

            <p class="text-xs font-medium text-slate-500">
                Compliant
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-success">
                <?php echo e($stats['compliant']); ?>

            </p>

            <p class="mt-1 text-xs text-slate-400">
                Records currently compliant
            </p>
        </div>


        <div class="card relative overflow-hidden p-5">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-warning"></div>

            <p class="text-xs font-medium text-slate-500">
                At Risk
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-amber-600">
                <?php echo e($stats['at_risk']); ?>

            </p>

            <p class="mt-1 text-xs text-slate-400">
                Compliance attention needed
            </p>
        </div>


        <div class="card relative overflow-hidden p-5">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-error"></div>

            <p class="text-xs font-medium text-slate-500">
                Review Required
            </p>

            <p class="mt-2 font-heading text-3xl font-bold text-error">
                <?php echo e($stats['review_required']); ?>

            </p>

            <p class="mt-1 text-xs text-slate-400">
                Records awaiting review
            </p>
        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[400px_minmax(0,1fr)]">

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageRetention')): ?>

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
                                        d="M9 5H7a2 2 0 00-2 2v12h14V7a2 2 0 00-2-2h-2M9 5a3 3 0 016 0M9 5h6M9 11h6M9 15h4" />

                                </svg>

                            </div>

                            <div>
                                <h3 class="font-heading text-base font-semibold text-primary">
                                    Track a Record
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Register a record for retention monitoring.
                                </p>
                            </div>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="<?php echo e(route('retention.store')); ?>"
                        class="space-y-5 p-5">

                        <?php echo csrf_field(); ?>


                        <div>
                            <label for="record_title" class="label">
                                Record Title
                                <span class="text-error">*</span>
                            </label>

                            <input
                                id="record_title"
                                type="text"
                                name="record_title"
                                value="<?php echo e(old('record_title')); ?>"
                                required
                                placeholder="e.g. Partnership Agreement 2026"
                                class="input">

                            <?php $__errorArgs = ['record_title'];
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
                                <label for="record_type" class="label">
                                    Record Type
                                </label>

                                <select id="record_type"
                                        name="record_type"
                                        class="input">

                                    <?php $__currentLoopData = [
                                        'document',
                                        'contract',
                                        'legal_record',
                                        'other'
                                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <option
                                            value="<?php echo e($type); ?>"
                                            <?php if(old('record_type', 'document') === $type): echo 'selected'; endif; ?>>

                                            <?php echo e(str($type)->headline()); ?>


                                        </option>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>
                            </div>


                            <div>
                                <label for="record_id" class="label">
                                    Related ID
                                </label>

                                <input
                                    id="record_id"
                                    type="number"
                                    min="1"
                                    name="record_id"
                                    value="<?php echo e(old('record_id')); ?>"
                                    placeholder="Optional"
                                    class="input">
                            </div>

                        </div>


                        <div>
                            <label for="policy_id" class="label">
                                Retention Policy
                            </label>

                            <select
                                id="policy_id"
                                name="policy_id"
                                class="input">

                                <option value="">
                                    No policy selected
                                </option>

                                <?php $__currentLoopData = $policies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $policy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option
                                        value="<?php echo e($policy->id); ?>"
                                        <?php if((string) old('policy_id') === (string) $policy->id): echo 'selected'; endif; ?>>

                                        <?php echo e($policy->name); ?>

                                        (<?php echo e($policy->retention_years); ?> <?php echo e(Str::plural('year', $policy->retention_years)); ?>)

                                    </option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>
                        </div>


                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>
                                <label for="start_date" class="label">
                                    Retention Start
                                </label>

                                <input
                                    id="start_date"
                                    type="date"
                                    name="start_date"
                                    value="<?php echo e(old('start_date')); ?>"
                                    class="input">
                            </div>


                            <div>
                                <label for="review_date" class="label">
                                    Review Date
                                </label>

                                <input
                                    id="review_date"
                                    type="date"
                                    name="review_date"
                                    value="<?php echo e(old('review_date')); ?>"
                                    class="input">
                            </div>

                        </div>


                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>
                                <label for="status" class="label">
                                    Retention Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="input">

                                    <?php $__currentLoopData = [
                                        'retained',
                                        'review_required',
                                        'extended',
                                        'archived',
                                        'marked_for_disposal'
                                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <option
                                            value="<?php echo e($status); ?>"
                                            <?php if(old('status', 'retained') === $status): echo 'selected'; endif; ?>>

                                            <?php echo e(str($status)->headline()); ?>


                                        </option>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>
                            </div>


                            <div>
                                <label for="compliance_status" class="label">
                                    Compliance
                                </label>

                                <select
                                    id="compliance_status"
                                    name="compliance_status"
                                    class="input">

                                    <?php $__currentLoopData = [
                                        'compliant',
                                        'at_risk',
                                        'non_compliant'
                                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <option
                                            value="<?php echo e($status); ?>"
                                            <?php if(old('compliance_status', 'compliant') === $status): echo 'selected'; endif; ?>>

                                            <?php echo e(str($status)->headline()); ?>


                                        </option>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>
                            </div>

                        </div>


                        <div>
                            <label for="notes" class="label">
                                Notes
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                placeholder="Retention notes, review requirements, or compliance observations..."
                                class="input"><?php echo e(old('notes')); ?></textarea>
                        </div>


                        <button type="submit" class="btn-secondary w-full">

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

                            Track Record

                        </button>

                    </form>

                </div>

            </div>

        <?php endif; ?>


        
        <div class="min-w-0 space-y-4">

            <div class="flex flex-col gap-4">

                <div>
                    <h3 class="font-heading text-lg font-semibold text-primary">
                        Retention Register
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Monitor review schedules, disposition status, and compliance.
                    </p>
                </div>


                
                <form
                    method="GET"
                    action="<?php echo e(route('retention.index')); ?>"
                    class="grid gap-2 sm:grid-cols-3">

                    <select
                        name="record_type"
                        onchange="this.form.submit()"
                        class="input">

                        <option value="">
                            All Record Types
                        </option>

                        <?php $__currentLoopData = [
                            'document',
                            'contract',
                            'legal_record',
                            'other'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option
                                value="<?php echo e($type); ?>"
                                <?php if(request('record_type') === $type): echo 'selected'; endif; ?>>

                                <?php echo e(str($type)->headline()); ?>


                            </option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>


                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="input">

                        <option value="">
                            All Retention Statuses
                        </option>

                        <?php $__currentLoopData = [
                            'retained',
                            'review_required',
                            'extended',
                            'archived',
                            'marked_for_disposal'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option
                                value="<?php echo e($status); ?>"
                                <?php if(request('status') === $status): echo 'selected'; endif; ?>>

                                <?php echo e(str($status)->headline()); ?>


                            </option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>


                    <select
                        name="compliance"
                        onchange="this.form.submit()"
                        class="input">

                        <option value="">
                            All Compliance
                        </option>

                        <?php $__currentLoopData = [
                            'compliant',
                            'at_risk',
                            'non_compliant'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option
                                value="<?php echo e($status); ?>"
                                <?php if(request('compliance') === $status): echo 'selected'; endif; ?>>

                                <?php echo e(str($status)->headline()); ?>


                            </option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>

                </form>

            </div>


            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header">

                            <tr>
                                <th class="px-5 py-4 font-medium">
                                    Record
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Policy
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Review
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Retention Status
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Compliance
                                </th>
                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            <?php $__empty_1 = true; $__currentLoopData = $retentions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $retention): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                <?php
                                    $daysToReview = $retention->review_date
                                        ? (int) now()->startOfDay()->diffInDays(
                                            $retention->review_date->copy()->startOfDay(),
                                            false
                                        )
                                        : null;
                                ?>


                                <tr class="transition hover:bg-sky-50/40">

                                    
                                    <td class="px-5 py-4">

                                        <div class="flex items-start gap-3">

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
                                                        d="M7 3h7l5 5v13H7V3zm7 0v5h5" />

                                                </svg>

                                            </div>

                                            <div class="min-w-0">

                                                <p class="max-w-[220px] truncate font-button text-sm font-semibold text-primary">
                                                    <?php echo e($retention->record_title); ?>

                                                </p>

                                                <div class="mt-1 flex flex-wrap gap-2">

                                                    <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[10px] font-medium text-primary">
                                                        <?php echo e(str($retention->record_type)->headline()); ?>

                                                    </span>

                                                    <?php if($retention->record_id): ?>

                                                        <span class="text-[10px] text-slate-400">
                                                            ID #<?php echo e($retention->record_id); ?>

                                                        </span>

                                                    <?php endif; ?>

                                                </div>

                                                <?php if($retention->last_action_by): ?>

                                                    <p class="mt-1 text-[10px] text-slate-400">
                                                        Updated by <?php echo e($retention->last_action_by); ?>

                                                    </p>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php if($retention->policy): ?>

                                            <p class="text-sm font-medium text-slate-700">
                                                <?php echo e($retention->policy->name); ?>

                                            </p>

                                            <p class="mt-1 text-[10px] text-slate-400">
                                                <?php echo e($retention->policy->retention_years); ?>

                                                <?php echo e(Str::plural('year', $retention->policy->retention_years)); ?>

                                            </p>

                                        <?php elseif($retention->policy_name): ?>

                                            <p class="text-sm text-slate-600">
                                                <?php echo e($retention->policy_name); ?>

                                            </p>

                                        <?php else: ?>

                                            <span class="text-xs text-slate-400">
                                                No policy
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php if($retention->review_date): ?>

                                            <p class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'text-sm font-medium',
                                                'text-error' => $daysToReview !== null && $daysToReview < 0,
                                                'text-amber-600' => $daysToReview !== null && $daysToReview >= 0 && $daysToReview <= 30,
                                                'text-slate-700' => $daysToReview === null || $daysToReview > 30,
                                            ]); ?>">

                                                <?php echo e($retention->review_date->format('M d, Y')); ?>


                                            </p>

                                            <?php if($daysToReview < 0): ?>

                                                <p class="mt-1 text-[10px] font-medium text-error">
                                                    Overdue by <?php echo e(abs($daysToReview)); ?> days
                                                </p>

                                            <?php elseif($daysToReview === 0): ?>

                                                <p class="mt-1 text-[10px] font-medium text-error">
                                                    Review due today
                                                </p>

                                            <?php elseif($daysToReview <= 30): ?>

                                                <p class="mt-1 text-[10px] font-medium text-amber-600">
                                                    Due in <?php echo e($daysToReview); ?> days
                                                </p>

                                            <?php endif; ?>

                                        <?php else: ?>

                                            <span class="text-xs text-slate-400">
                                                No review date
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageRetention')): ?>

                                            <form
                                                method="POST"
                                                action="<?php echo e(route('retention.update', $retention)); ?>"
                                                class="min-w-[170px]">

                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>

                                                <input type="hidden"
                                                       name="record_title"
                                                       value="<?php echo e($retention->record_title); ?>">

                                                <input type="hidden"
                                                       name="record_type"
                                                       value="<?php echo e($retention->record_type); ?>">

                                                <input type="hidden"
                                                       name="record_id"
                                                       value="<?php echo e($retention->record_id); ?>">

                                                <input type="hidden"
                                                       name="policy_id"
                                                       value="<?php echo e($retention->policy_id); ?>">

                                                <input type="hidden"
                                                       name="start_date"
                                                       value="<?php echo e($retention->start_date?->toDateString()); ?>">

                                                <input type="hidden"
                                                       name="review_date"
                                                       value="<?php echo e($retention->review_date?->toDateString()); ?>">

                                                <input type="hidden"
                                                       name="compliance_status"
                                                       value="<?php echo e($retention->compliance_status); ?>">

                                                <input type="hidden"
                                                       name="notes"
                                                       value="<?php echo e($retention->notes); ?>">

                                                <select
                                                    name="status"
                                                    onchange="this.form.submit()"
                                                    class="input py-2 text-xs">

                                                    <?php $__currentLoopData = [
                                                        'retained',
                                                        'review_required',
                                                        'extended',
                                                        'archived',
                                                        'marked_for_disposal'
                                                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                        <option
                                                            value="<?php echo e($status); ?>"
                                                            <?php if($retention->status === $status): echo 'selected'; endif; ?>>

                                                            <?php echo e(str($status)->headline()); ?>


                                                        </option>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </select>

                                            </form>

                                        <?php else: ?>

                                            <span class="badge bg-slate-100 text-slate-600">
                                                <?php echo e(str($retention->status)->headline()); ?>

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageRetention')): ?>

                                            <form
                                                method="POST"
                                                action="<?php echo e(route('retention.update', $retention)); ?>"
                                                class="min-w-[145px]">

                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>

                                                <input type="hidden"
                                                       name="record_title"
                                                       value="<?php echo e($retention->record_title); ?>">

                                                <input type="hidden"
                                                       name="record_type"
                                                       value="<?php echo e($retention->record_type); ?>">

                                                <input type="hidden"
                                                       name="record_id"
                                                       value="<?php echo e($retention->record_id); ?>">

                                                <input type="hidden"
                                                       name="policy_id"
                                                       value="<?php echo e($retention->policy_id); ?>">

                                                <input type="hidden"
                                                       name="start_date"
                                                       value="<?php echo e($retention->start_date?->toDateString()); ?>">

                                                <input type="hidden"
                                                       name="review_date"
                                                       value="<?php echo e($retention->review_date?->toDateString()); ?>">

                                                <input type="hidden"
                                                       name="status"
                                                       value="<?php echo e($retention->status); ?>">

                                                <input type="hidden"
                                                       name="notes"
                                                       value="<?php echo e($retention->notes); ?>">

                                                <select
                                                    name="compliance_status"
                                                    onchange="this.form.submit()"
                                                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                        'input py-2 text-xs font-semibold',

                                                        'border-success/30 bg-success/5 text-success'
                                                            => $retention->compliance_status === 'compliant',

                                                        'border-warning/40 bg-warning/5 text-amber-700'
                                                            => $retention->compliance_status === 'at_risk',

                                                        'border-error/30 bg-error/5 text-error'
                                                            => $retention->compliance_status === 'non_compliant',
                                                    ]); ?>">

                                                    <?php $__currentLoopData = [
                                                        'compliant',
                                                        'at_risk',
                                                        'non_compliant'
                                                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                        <option
                                                            value="<?php echo e($status); ?>"
                                                            <?php if($retention->compliance_status === $status): echo 'selected'; endif; ?>>

                                                            <?php echo e(str($status)->headline()); ?>


                                                        </option>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </select>

                                            </form>

                                        <?php else: ?>

                                            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'badge',
                                                'badge-success' => $retention->compliance_status === 'compliant',
                                                'badge-warning' => $retention->compliance_status === 'at_risk',
                                                'badge-error' => $retention->compliance_status === 'non_compliant',
                                            ]); ?>">

                                                <?php echo e(str($retention->compliance_status)->headline()); ?>


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
                                                        d="M7 3h7l5 5v13H7V3zm7 0v5h5" />

                                                </svg>

                                            </div>

                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No retention records found
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Records registered for retention monitoring will appear here.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <?php if($retentions->hasPages()): ?>

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    <?php echo e($retentions->withQueryString()->links()); ?>

                </div>

            <?php endif; ?>

        </div>

    </div>


    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageRetention')): ?>

        <div class="card overflow-hidden">

            <div class="border-b border-border bg-background/60 px-5 py-5">

                <div class="flex items-center justify-between gap-4">

                    <div>
                        <h3 class="font-heading text-base font-semibold text-primary">
                            Retention Policies
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Define retention duration and legal basis for organizational record categories.
                        </p>
                    </div>

                    <span class="badge badge-info">
                        <?php echo e($allPolicies->count()); ?> Policies
                    </span>

                </div>

            </div>


            <div class="grid gap-6 p-5 xl:grid-cols-[360px_minmax(0,1fr)]">

                
                <form
                    method="POST"
                    action="<?php echo e(route('retention-policies.store')); ?>"
                    class="space-y-4 rounded-xl border border-border bg-background/40 p-4">

                    <?php echo csrf_field(); ?>

                    <h4 class="font-heading text-sm font-semibold text-primary">
                        Create Policy
                    </h4>


                    <div>
                        <label class="label">
                            Policy Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="e.g. Contract Records Policy"
                            class="input">
                    </div>


                    <div>
                        <label class="label">
                            Record Category
                        </label>

                        <select name="record_category" class="input">

                            <?php $__currentLoopData = [
                                'administrative',
                                'contract',
                                'legal',
                                'permit',
                                'license',
                                'compliance',
                                'partnership',
                                'financial',
                                'operational',
                                'other'
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($category); ?>">
                                    <?php echo e(str($category)->headline()); ?>

                                </option>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>
                    </div>


                    <div>
                        <label class="label">
                            Retention Years
                        </label>

                        <input
                            type="number"
                            min="1"
                            name="retention_years"
                            value="5"
                            required
                            class="input">
                    </div>


                    <div>
                        <label class="label">
                            Legal Basis
                        </label>

                        <textarea
                            name="legal_basis"
                            rows="2"
                            placeholder="Law, regulation, policy, or contractual basis..."
                            class="input"></textarea>
                    </div>


                    <div>
                        <label class="label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            rows="2"
                            placeholder="Describe the policy..."
                            class="input"></textarea>
                    </div>


                    <input type="hidden" name="is_active" value="0">

                    <label class="flex items-center gap-2 text-sm text-slate-600">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            checked
                            class="rounded border-border text-primary focus:ring-primary">

                        Active policy

                    </label>


                    <button type="submit" class="btn-primary w-full justify-center">
                        Create Policy
                    </button>

                </form>


                
                <div class="space-y-3">

                    <?php $__empty_1 = true; $__currentLoopData = $allPolicies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $policy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <div class="rounded-xl border border-border bg-white p-4">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                <div>

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h4 class="font-button text-sm font-semibold text-primary">
                                            <?php echo e($policy->name); ?>

                                        </h4>

                                        <?php if($policy->is_active): ?>

                                            <span class="badge badge-success">
                                                Active
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-slate-100 text-slate-500">
                                                Inactive
                                            </span>

                                        <?php endif; ?>

                                    </div>


                                    <p class="mt-2 text-xs text-slate-500">

                                        <?php echo e(str($policy->record_category)->headline()); ?>


                                        <span class="mx-1">•</span>

                                        <?php echo e($policy->retention_years); ?>

                                        <?php echo e(Str::plural('year', $policy->retention_years)); ?>


                                    </p>


                                    <?php if($policy->legal_basis): ?>

                                        <p class="mt-2 text-xs text-slate-500">
                                            <span class="font-semibold text-slate-600">
                                                Legal basis:
                                            </span>

                                            <?php echo e($policy->legal_basis); ?>

                                        </p>

                                    <?php endif; ?>


                                    <?php if($policy->description): ?>

                                        <p class="mt-2 text-xs leading-5 text-slate-400">
                                            <?php echo e($policy->description); ?>

                                        </p>

                                    <?php endif; ?>

                                </div>


                                
                                <form
                                    method="POST"
                                    action="<?php echo e(route('retention-policies.update', $policy)); ?>">

                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>

                                    <input type="hidden"
                                           name="name"
                                           value="<?php echo e($policy->name); ?>">

                                    <input type="hidden"
                                           name="record_category"
                                           value="<?php echo e($policy->record_category); ?>">

                                    <input type="hidden"
                                           name="retention_years"
                                           value="<?php echo e($policy->retention_years); ?>">

                                    <input type="hidden"
                                           name="description"
                                           value="<?php echo e($policy->description); ?>">

                                    <input type="hidden"
                                           name="legal_basis"
                                           value="<?php echo e($policy->legal_basis); ?>">

                                    <input type="hidden"
                                           name="is_active"
                                           value="<?php echo e($policy->is_active ? 0 : 1); ?>">

                                    <button
                                        type="submit"
                                        class="btn-outline whitespace-nowrap text-xs">

                                        <?php echo e($policy->is_active ? 'Deactivate' : 'Activate'); ?>


                                    </button>

                                </form>

                            </div>

                        </div>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <div class="rounded-xl border border-dashed border-border p-10 text-center">

                            <p class="text-sm font-medium text-slate-600">
                                No retention policies
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Create your first retention policy using the form.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\marcj\Documents\fams-laravel-backup\resources\views/retention/index.blade.php ENDPATH**/ ?>