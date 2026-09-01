<?php $__env->startSection('title', 'Legal Records'); ?>

<?php $__env->startSection('content'); ?>

<div class="space-y-6">

    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Legal Records
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Manage permits, licenses, legal requirements, cases, and compliance-related records.
            </p>
        </div>


        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">

            <p class="text-xs text-slate-500">
                Total Records
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                <?php echo e($records->total()); ?>

            </p>

        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[410px_minmax(0,1fr)]">

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageLegal')): ?>

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
                                        d="M9 12h6M9 16h6M9 8h3M6 3h9l3 3v15H6V3z" />

                                </svg>

                            </div>


                            <div>

                                <h3 class="font-heading text-base font-semibold text-primary">
                                    Add Legal Record
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Register a legal, regulatory, or compliance record.
                                </p>

                            </div>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="<?php echo e(route('legal.store')); ?>"
                        enctype="multipart/form-data"
                        class="space-y-5 p-5">

                        <?php echo csrf_field(); ?>


                        
                        <div>

                            <label for="title" class="label">
                                Record Title
                                <span class="text-error">*</span>
                            </label>

                            <input
                                id="title"
                                type="text"
                                name="title"
                                value="<?php echo e(old('title')); ?>"
                                required
                                placeholder="e.g. Business Permit 2026"
                                class="input <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                            <?php $__errorArgs = ['title'];
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

                            <label for="record_type" class="label">
                                Record Type
                                <span class="text-error">*</span>
                            </label>

                            <select
                                id="record_type"
                                name="record_type"
                                class="input">

                                <?php $__currentLoopData = [
                                    'permit',
                                    'license',
                                    'legal_case',
                                    'requirement',
                                    'legal_document'
                                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option
                                        value="<?php echo e($type); ?>"
                                        <?php if(old('record_type', 'permit') === $type): echo 'selected'; endif; ?>>

                                        <?php echo e(str($type)->headline()); ?>


                                    </option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                        </div>


                        
                        <div>

                            <label for="reference_number" class="label">
                                Reference Number
                            </label>

                            <input
                                id="reference_number"
                                type="text"
                                name="reference_number"
                                value="<?php echo e(old('reference_number')); ?>"
                                placeholder="e.g. BP-2026-00125"
                                class="input">

                        </div>


                        
                        <div>

                            <label for="issuing_authority" class="label">
                                Issuing Authority
                            </label>

                            <input
                                id="issuing_authority"
                                type="text"
                                name="issuing_authority"
                                value="<?php echo e(old('issuing_authority')); ?>"
                                placeholder="e.g. City Government"
                                class="input">

                        </div>


                        
                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>

                                <label for="issue_date" class="label">
                                    Issue Date
                                </label>

                                <input
                                    id="issue_date"
                                    type="date"
                                    name="issue_date"
                                    value="<?php echo e(old('issue_date')); ?>"
                                    class="input">

                            </div>


                            <div>

                                <label for="expiration_date" class="label">
                                    Expiration Date
                                </label>

                                <input
                                    id="expiration_date"
                                    type="date"
                                    name="expiration_date"
                                    value="<?php echo e(old('expiration_date')); ?>"
                                    class="input">

                                <?php $__errorArgs = ['expiration_date'];
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

                        </div>


                        
                        <div>

                            <label for="responsible_officer_email" class="label">
                                Responsible Officer
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
                                    id="responsible_officer_email"
                                    type="email"
                                    name="responsible_officer_email"
                                    value="<?php echo e(old('responsible_officer_email')); ?>"
                                    placeholder="officer@example.com"
                                    class="input pl-10">

                            </div>

                        </div>


                        
                        <div>

                            <label for="status" class="label">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="input">

                                <?php $__currentLoopData = [
                                    'active',
                                    'pending',
                                    'expiring_soon',
                                    'expired',
                                    'renewed',
                                    'closed'
                                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option
                                        value="<?php echo e($status); ?>"
                                        <?php if(old('status', 'active') === $status): echo 'selected'; endif; ?>>

                                        <?php echo e(str($status)->headline()); ?>


                                    </option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                        </div>


                        
                        <div>

                            <label for="description" class="label">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="Brief description of this legal record..."
                                class="input"><?php echo e(old('description')); ?></textarea>

                        </div>


                        
                        <div>

                            <label for="legal_notes" class="label">
                                Legal Notes
                            </label>

                            <textarea
                                id="legal_notes"
                                name="legal_notes"
                                rows="3"
                                placeholder="Important legal observations, obligations, or follow-up actions..."
                                class="input"><?php echo e(old('legal_notes')); ?></textarea>

                        </div>


                        
                        <div>

                            <label for="file" class="label">
                                Supporting File
                            </label>

                            <input
                                id="file"
                                type="file"
                                name="file"
                                class="block w-full rounded-xl border border-border bg-white text-xs text-slate-500 file:mr-3 file:border-0 file:bg-primary/10 file:px-4 file:py-3 file:font-button file:text-xs file:font-semibold file:text-primary hover:file:bg-primary/15">

                            <p class="mt-1.5 text-xs text-slate-400">
                                Maximum file size: 20 MB.
                            </p>

                            <?php $__errorArgs = ['file'];
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

                            Save Legal Record

                        </button>

                    </form>

                </div>

            </div>

        <?php endif; ?>


        
        <div class="min-w-0 space-y-4">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <h3 class="font-heading text-lg font-semibold text-primary">
                        Legal Record Register
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Monitor legal status, expiration dates, and review requirements.
                    </p>

                </div>


                
                <form method="GET" action="<?php echo e(route('legal.index')); ?>">

                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="input min-w-[190px]">

                        <option value="">
                            All Statuses
                        </option>

                        <?php $__currentLoopData = [
                            'active',
                            'pending',
                            'expiring_soon',
                            'expired',
                            'renewed',
                            'closed'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option
                                value="<?php echo e($status); ?>"
                                <?php if(request('status') === $status): echo 'selected'; endif; ?>>

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
                                    Authority
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Expiration
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Status
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Review
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                <?php
                                    $daysToExpiry = $record->expiration_date
                                        ? now()->startOfDay()->diffInDays($record->expiration_date, false)
                                        : null;
                                ?>


                                <tr class="transition hover:bg-sky-50/40">

                                    
                                    <td class="px-5 py-4">

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
                                                        d="M9 12h6M9 16h6M9 8h3M6 3h9l3 3v15H6V3z" />

                                                </svg>

                                            </div>


                                            <div class="min-w-0">

                                                <p class="max-w-[220px] truncate font-button text-sm font-semibold text-primary">
                                                    <?php echo e($record->title); ?>

                                                </p>


                                                <div class="mt-1 flex flex-wrap items-center gap-2">

                                                    <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[10px] font-medium text-primary">
                                                        <?php echo e(str($record->record_type)->headline()); ?>

                                                    </span>


                                                    <?php if($record->reference_number): ?>

                                                        <span class="text-[10px] text-slate-400">
                                                            #<?php echo e($record->reference_number); ?>

                                                        </span>

                                                    <?php endif; ?>

                                                </div>


                                                <?php if($record->file_name): ?>

                                                    <p class="mt-1 max-w-[220px] truncate text-[10px] text-slate-400">
                                                        File: <?php echo e($record->file_name); ?>

                                                    </p>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <p class="max-w-[180px] text-sm text-slate-600">
                                            <?php echo e($record->issuing_authority ?: 'Not specified'); ?>

                                        </p>


                                        <?php if($record->responsible_officer_email): ?>

                                            <p class="mt-1 max-w-[180px] truncate text-[10px] text-slate-400">
                                                <?php echo e($record->responsible_officer_email); ?>

                                            </p>

                                        <?php endif; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php if($record->expiration_date): ?>

                                            <p class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'text-sm font-medium',
                                                'text-error' => $daysToExpiry !== null && $daysToExpiry < 0,
                                                'text-amber-600' => $daysToExpiry !== null && $daysToExpiry >= 0 && $daysToExpiry <= 30,
                                                'text-slate-700' => $daysToExpiry === null || $daysToExpiry > 30,
                                            ]); ?>">

                                                <?php echo e($record->expiration_date->format('M d, Y')); ?>


                                            </p>


                                            <?php if($daysToExpiry !== null && $daysToExpiry < 0): ?>

                                                <p class="mt-1 text-[10px] font-medium text-error">
                                                    Expired <?php echo e(abs($daysToExpiry)); ?> days ago
                                                </p>

                                            <?php elseif($daysToExpiry !== null && $daysToExpiry === 0): ?>

                                                <p class="mt-1 text-[10px] font-medium text-error">
                                                    Expires today
                                                </p>

                                            <?php elseif($daysToExpiry !== null && $daysToExpiry <= 30): ?>

                                                <p class="mt-1 text-[10px] font-medium text-amber-600">
                                                    <?php echo e($daysToExpiry); ?> days remaining
                                                </p>

                                            <?php endif; ?>

                                        <?php else: ?>

                                            <span class="text-xs text-slate-400">
                                                No expiration
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php switch($record->status):

                                            case ('active'): ?>

                                                <span class="badge badge-success">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                    Active
                                                </span>

                                                <?php break; ?>


                                            <?php case ('pending'): ?>

                                                <span class="badge badge-warning">
                                                    Pending
                                                </span>

                                                <?php break; ?>


                                            <?php case ('expiring_soon'): ?>

                                                <span class="badge badge-warning">
                                                    Expiring Soon
                                                </span>

                                                <?php break; ?>


                                            <?php case ('expired'): ?>

                                                <span class="badge badge-error">
                                                    Expired
                                                </span>

                                                <?php break; ?>


                                            <?php case ('renewed'): ?>

                                                <span class="badge badge-info">
                                                    Renewed
                                                </span>

                                                <?php break; ?>


                                            <?php case ('closed'): ?>

                                                <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                                    Closed
                                                </span>

                                                <?php break; ?>


                                            <?php default: ?>

                                                <span class="badge badge-info">
                                                    <?php echo e(str($record->status)->headline()); ?>

                                                </span>

                                        <?php endswitch; ?>

                                    </td>


                                    
                                    <td class="px-5 py-4">

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reviewLegal')): ?>

                                            <form
                                                method="POST"
                                                action="<?php echo e(route('legal.review', $record)); ?>"
                                                class="min-w-[150px]">

                                                <?php echo csrf_field(); ?>

                                                <select
                                                    name="review_status"
                                                    onchange="this.form.submit()"
                                                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                        'input py-2 text-xs font-medium',

                                                        'border-slate-200 bg-slate-50 text-slate-600'
                                                            => $record->review_status === 'not_reviewed',

                                                        'border-accent/30 bg-accent/5 text-primary'
                                                            => $record->review_status === 'in_review',

                                                        'border-success/30 bg-success/5 text-success'
                                                            => $record->review_status === 'reviewed',

                                                        'border-error/30 bg-error/5 text-error'
                                                            => $record->review_status === 'action_required',
                                                    ]); ?>">

                                                    <?php $__currentLoopData = [
                                                        'not_reviewed',
                                                        'in_review',
                                                        'reviewed',
                                                        'action_required'
                                                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reviewStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                        <option
                                                            value="<?php echo e($reviewStatus); ?>"
                                                            <?php if($record->review_status === $reviewStatus): echo 'selected'; endif; ?>>

                                                            <?php echo e(str($reviewStatus)->headline()); ?>


                                                        </option>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </select>

                                            </form>

                                        <?php else: ?>

                                            <?php switch($record->review_status):

                                                case ('reviewed'): ?>

                                                    <span class="badge badge-success">
                                                        Reviewed
                                                    </span>

                                                    <?php break; ?>


                                                <?php case ('action_required'): ?>

                                                    <span class="badge badge-error">
                                                        Action Required
                                                    </span>

                                                    <?php break; ?>


                                                <?php case ('in_review'): ?>

                                                    <span class="badge badge-info">
                                                        In Review
                                                    </span>

                                                    <?php break; ?>


                                                <?php default: ?>

                                                    <span class="badge bg-slate-100 text-slate-600">
                                                        Not Reviewed
                                                    </span>

                                            <?php endswitch; ?>

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
                                                        d="M9 12h6M9 16h6M9 8h3M6 3h9l3 3v15H6V3z" />

                                                </svg>

                                            </div>


                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No legal records found
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">

                                                <?php if(request('status')): ?>

                                                    No records match the selected status.

                                                <?php else: ?>

                                                    Legal and compliance records will appear here.

                                                <?php endif; ?>

                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <?php if($records->hasPages()): ?>

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    <?php echo e($records->withQueryString()->links()); ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\marcj\Documents\fams-laravel-backup\resources\views/legal/index.blade.php ENDPATH**/ ?>