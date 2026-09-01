<?php $__env->startSection('title', 'Contracts'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $canRenewContracts = auth()->user()->hasRole([
        \App\Models\User::ROLE_ADMIN_OFFICER,
        \App\Models\User::ROLE_MANAGER,
        \App\Models\User::ROLE_SYS_ADMIN,
    ]);
?>

<div class="space-y-6">

    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Contract Management
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Create, review, approve, monitor, and renew organizational contracts.
            </p>
        </div>

        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">
            <p class="text-xs text-slate-500">
                Total Contracts
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                <?php echo e($contracts->total()); ?>

            </p>
        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[410px_minmax(0,1fr)]">

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageContracts')): ?>

            <div>

                <div class="card overflow-hidden xl:sticky xl:top-6">

                    <div class="border-b border-border bg-background/60 px-5 py-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                                <svg class="h-5 w-5"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                                </svg>

                            </div>

                            <div>
                                <h3 class="font-heading text-base font-semibold text-primary">
                                    New Contract
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Contracts begin as drafts before legal review.
                                </p>
                            </div>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="<?php echo e(route('contracts.store')); ?>"
                        enctype="multipart/form-data"
                        class="space-y-5 p-5">

                        <?php echo csrf_field(); ?>


                        
                        <div>

                            <label for="contract_number" class="label">
                                Contract Number
                            </label>

                            <input
                                id="contract_number"
                                type="text"
                                name="contract_number"
                                value="<?php echo e(old('contract_number')); ?>"
                                placeholder="e.g. HT-CTR-2026-001"
                                class="input">

                        </div>


                        
                        <div>

                            <label for="title" class="label">
                                Contract Title
                                <span class="text-error">*</span>
                            </label>

                            <input
                                id="title"
                                type="text"
                                name="title"
                                value="<?php echo e(old('title')); ?>"
                                required
                                placeholder="e.g. Hotel Partnership Agreement"
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

                            <label for="contract_type" class="label">
                                Contract Type
                            </label>

                            <select
                                id="contract_type"
                                name="contract_type"
                                class="input">

                                <?php $__currentLoopData = [
                                    'hotel',
                                    'tour_operator',
                                    'transportation',
                                    'supplier',
                                    'partnership',
                                    'service',
                                    'other'
                                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option
                                        value="<?php echo e($type); ?>"
                                        <?php if(old('contract_type', 'service') === $type): echo 'selected'; endif; ?>>

                                        <?php echo e(str($type)->headline()); ?>


                                    </option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                        </div>


                        
                        <div>

                            <label for="party" class="label">
                                Counterparty / Partner
                            </label>

                            <input
                                id="party"
                                type="text"
                                name="parties[]"
                                value="<?php echo e(old('parties.0')); ?>"
                                placeholder="e.g. Sunrise Hotel Corporation"
                                class="input">

                        </div>


                        
                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>
                                <label for="start_date" class="label">
                                    Start Date
                                </label>

                                <input
                                    id="start_date"
                                    type="date"
                                    name="start_date"
                                    value="<?php echo e(old('start_date')); ?>"
                                    class="input">
                            </div>


                            <div>
                                <label for="end_date" class="label">
                                    End Date
                                </label>

                                <input
                                    id="end_date"
                                    type="date"
                                    name="end_date"
                                    value="<?php echo e(old('end_date')); ?>"
                                    class="input">

                                <?php $__errorArgs = ['end_date'];
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


                        
                        <div class="grid grid-cols-[1fr_110px] gap-3">

                            <div>
                                <label for="value" class="label">
                                    Contract Value
                                </label>

                                <input
                                    id="value"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="value"
                                    value="<?php echo e(old('value')); ?>"
                                    placeholder="0.00"
                                    class="input">
                            </div>


                            <div>
                                <label for="currency" class="label">
                                    Currency
                                </label>

                                <input
                                    id="currency"
                                    type="text"
                                    name="currency"
                                    maxlength="8"
                                    value="<?php echo e(old('currency', 'PHP')); ?>"
                                    class="input uppercase">
                            </div>

                        </div>


                        
                        <div>

                            <label for="responsible_officer_email" class="label">
                                Responsible Officer
                            </label>

                            <input
                                id="responsible_officer_email"
                                type="email"
                                name="responsible_officer_email"
                                value="<?php echo e(old('responsible_officer_email', auth()->user()->email)); ?>"
                                class="input">

                        </div>


                        
                        <div>

                            <label for="description" class="label">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="Briefly describe the purpose and scope of this contract..."
                                class="input"><?php echo e(old('description')); ?></textarea>

                        </div>


                        
                        <div>

                            <label for="file" class="label">
                                Contract File
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


                        <button type="submit" class="btn-secondary w-full">

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M5 13l4 4L19 7" />

                            </svg>

                            Save Contract Draft

                        </button>

                    </form>

                </div>

            </div>

        <?php endif; ?>


        
        <div class="min-w-0 space-y-4">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <h3 class="font-heading text-lg font-semibold text-primary">
                        Contract Register
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Track each contract through legal review, approval, activation, and renewal.
                    </p>
                </div>


                <form method="GET" action="<?php echo e(route('contracts.index')); ?>">

                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="input min-w-[190px]">

                        <option value="">
                            All Statuses
                        </option>

                        <?php $__currentLoopData = [
                            'draft',
                            'under_review',
                            'pending_approval',
                            'active',
                            'renewed',
                            'expired'
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


            <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                <?php
                    $daysToEnd = $contract->end_date
                        ? now()->startOfDay()->diffInDays($contract->end_date, false)
                        : null;

                    $parties = $contract->parties ?? [];
                ?>


                <article class="card overflow-hidden">

                    
                    <div class="flex flex-col gap-4 border-b border-border px-5 py-5 sm:flex-row sm:items-start sm:justify-between">

                        <div class="flex min-w-0 items-start gap-3">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                                <svg class="h-5 w-5"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                                </svg>

                            </div>


                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <h4 class="font-heading text-base font-semibold text-primary">
                                        <?php echo e($contract->title); ?>

                                    </h4>

                                    <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[10px] font-semibold text-primary">
                                        v<?php echo e($contract->version ?? 1); ?>

                                    </span>

                                </div>


                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">

                                    <span>
                                        <?php echo e(str($contract->contract_type)->headline()); ?>

                                    </span>

                                    <?php if($contract->contract_number): ?>
                                        <span>•</span>
                                        <span><?php echo e($contract->contract_number); ?></span>
                                    <?php endif; ?>

                                    <?php if($contract->file_name): ?>
                                        <span>•</span>
                                        <span class="max-w-[220px] truncate">
                                            <?php echo e($contract->file_name); ?>

                                        </span>
                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>


                        
                        <div>

                            <?php switch($contract->status):

                                case ('draft'): ?>
                                    <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                        Draft
                                    </span>
                                    <?php break; ?>

                                <?php case ('under_review'): ?>
                                    <span class="badge badge-info">
                                        Under Review
                                    </span>
                                    <?php break; ?>

                                <?php case ('pending_approval'): ?>
                                    <span class="badge badge-warning">
                                        Pending Approval
                                    </span>
                                    <?php break; ?>

                                <?php case ('active'): ?>
                                    <span class="badge badge-success">
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                        Active
                                    </span>
                                    <?php break; ?>

                                <?php case ('renewed'): ?>
                                    <span class="badge badge-info">
                                        Renewed
                                    </span>
                                    <?php break; ?>

                                <?php case ('expired'): ?>
                                    <span class="badge badge-error">
                                        Expired
                                    </span>
                                    <?php break; ?>

                                <?php default: ?>
                                    <span class="badge badge-info">
                                        <?php echo e(str($contract->status)->headline()); ?>

                                    </span>

                            <?php endswitch; ?>

                        </div>

                    </div>


                    
                    <div class="grid gap-5 px-5 py-5 sm:grid-cols-2 xl:grid-cols-4">

                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Counterparty
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-700">
                                <?php echo e(count($parties) ? implode(', ', $parties) : 'Not specified'); ?>

                            </p>
                        </div>


                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Contract Value
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-700">
                                <?php if($contract->value !== null): ?>
                                    <?php echo e($contract->currency ?: 'PHP'); ?>

                                    <?php echo e(number_format((float) $contract->value, 2)); ?>

                                <?php else: ?>
                                    Not specified
                                <?php endif; ?>
                            </p>
                        </div>


                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Contract Term
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-700">
                                <?php echo e($contract->start_date?->format('M d, Y') ?? 'No start date'); ?>

                            </p>

                            <p class="mt-0.5 text-xs text-slate-400">
                                to <?php echo e($contract->end_date?->format('M d, Y') ?? 'No end date'); ?>

                            </p>
                        </div>


                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                Responsible Officer
                            </p>

                            <p class="mt-1 break-all text-sm font-medium text-slate-700">
                                <?php echo e($contract->responsible_officer_email ?: 'Not assigned'); ?>

                            </p>
                        </div>

                    </div>


                    
                    <?php if($daysToEnd !== null && $daysToEnd <= 30): ?>

                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'mx-5 mb-5 rounded-xl border px-4 py-3',
                            'border-error/20 bg-error/5' => $daysToEnd < 0,
                            'border-warning/30 bg-warning/5' => $daysToEnd >= 0,
                        ]); ?>">

                            <?php if($daysToEnd < 0): ?>

                                <p class="text-xs font-semibold text-error">
                                    Contract ended <?php echo e(abs($daysToEnd)); ?> days ago.
                                </p>

                            <?php elseif($daysToEnd === 0): ?>

                                <p class="text-xs font-semibold text-error">
                                    Contract ends today.
                                </p>

                            <?php else: ?>

                                <p class="text-xs font-semibold text-amber-700">
                                    Contract expires in <?php echo e($daysToEnd); ?> days.
                                </p>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>


                    
                    <div class="border-t border-border bg-background/40 px-5 py-4">

                        <div class="grid gap-4 sm:grid-cols-2">

                            
                            <div>

                                <p class="mb-2 text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                    Legal Review
                                </p>

                                <?php switch($contract->legal_review_status):

                                    case ('approved'): ?>
                                        <span class="badge badge-success">
                                            Approved
                                        </span>
                                        <?php break; ?>

                                    <?php case ('objections'): ?>
                                        <span class="badge badge-error">
                                            Objections Raised
                                        </span>
                                        <?php break; ?>

                                    <?php case ('pending'): ?>
                                        <span class="badge badge-warning">
                                            Pending
                                        </span>
                                        <?php break; ?>

                                    <?php case ('in_review'): ?>
                                        <span class="badge badge-info">
                                            In Review
                                        </span>
                                        <?php break; ?>

                                    <?php default: ?>
                                        <span class="badge bg-slate-100 text-slate-500">
                                            Not Submitted
                                        </span>

                                <?php endswitch; ?>

                                <?php if($contract->legal_reviewed_by): ?>

                                    <p class="mt-2 text-[10px] text-slate-400">
                                        Reviewed by <?php echo e($contract->legal_reviewed_by); ?>

                                    </p>

                                <?php endif; ?>

                            </div>


                            
                            <div>

                                <p class="mb-2 text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                    Management Approval
                                </p>

                                <?php switch($contract->approval_status):

                                    case ('approved'): ?>
                                        <span class="badge badge-success">
                                            Approved
                                        </span>
                                        <?php break; ?>

                                    <?php case ('rejected'): ?>
                                        <span class="badge badge-error">
                                            Rejected
                                        </span>
                                        <?php break; ?>

                                    <?php case ('pending'): ?>
                                        <span class="badge badge-warning">
                                            Awaiting Decision
                                        </span>
                                        <?php break; ?>

                                    <?php default: ?>
                                        <span class="badge bg-slate-100 text-slate-500">
                                            Not Submitted
                                        </span>

                                <?php endswitch; ?>

                                <?php if($contract->approved_by): ?>

                                    <p class="mt-2 text-[10px] text-slate-400">
                                        Decision by <?php echo e($contract->approved_by); ?>

                                    </p>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>


                    
                    <div class="flex flex-wrap items-end gap-2 border-t border-border px-5 py-4">

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageContracts')): ?>

                            <?php if(in_array($contract->status, ['draft', 'renewed'])): ?>

                                <form method="POST"
                                      action="<?php echo e(route('contracts.submit-review', $contract)); ?>">

                                    <?php echo csrf_field(); ?>

                                    <button type="submit" class="btn-secondary text-xs">
                                        Submit for Legal Review
                                    </button>

                                </form>

                            <?php endif; ?>

                        <?php endif; ?>


                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reviewLegal')): ?>

                            <?php if(in_array($contract->legal_review_status, ['pending', 'in_review'])): ?>

                                <form method="POST"
                                      action="<?php echo e(route('contracts.legal-review', $contract)); ?>">

                                    <?php echo csrf_field(); ?>

                                    <input type="hidden"
                                           name="outcome"
                                           value="approved">

                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg bg-success/10 px-3 py-2 font-button text-xs font-semibold text-success transition hover:bg-success hover:text-white">

                                        Clear Legal Review

                                    </button>

                                </form>


                                <form method="POST"
                                      action="<?php echo e(route('contracts.legal-review', $contract)); ?>"
                                      onsubmit="return confirm('Raise legal objections for this contract?');">

                                    <?php echo csrf_field(); ?>

                                    <input type="hidden"
                                           name="outcome"
                                           value="objections">

                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg bg-error/10 px-3 py-2 font-button text-xs font-semibold text-error transition hover:bg-error hover:text-white">

                                        Raise Objections

                                    </button>

                                </form>

                            <?php endif; ?>

                        <?php endif; ?>


                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approveContracts')): ?>

                            <?php if($contract->approval_status === 'pending'): ?>

                                <form method="POST"
                                      action="<?php echo e(route('contracts.decide', $contract)); ?>">

                                    <?php echo csrf_field(); ?>

                                    <input type="hidden"
                                           name="decision"
                                           value="approve">

                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg bg-success px-3 py-2 font-button text-xs font-semibold text-white transition hover:opacity-90">

                                        Approve Contract

                                    </button>

                                </form>


                                <form method="POST"
                                      action="<?php echo e(route('contracts.decide', $contract)); ?>"
                                      onsubmit="return confirm('Reject this contract and return it to draft?');">

                                    <?php echo csrf_field(); ?>

                                    <input type="hidden"
                                           name="decision"
                                           value="reject">

                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg border border-error/20 bg-error/5 px-3 py-2 font-button text-xs font-semibold text-error transition hover:bg-error hover:text-white">

                                        Reject

                                    </button>

                                </form>

                            <?php endif; ?>

                        <?php endif; ?>


                        
                        <?php if($canRenewContracts && in_array($contract->status, ['active', 'expired', 'renewed'])): ?>

                            <form
                                method="POST"
                                action="<?php echo e(route('contracts.renew', $contract)); ?>"
                                class="ml-auto flex flex-wrap items-end gap-2">

                                <?php echo csrf_field(); ?>

                                <div>
                                    <label class="mb-1 block text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                        New End Date
                                    </label>

                                    <input
                                        type="date"
                                        name="new_end_date"
                                        required
                                        min="<?php echo e(now()->addDay()->toDateString()); ?>"
                                        class="input py-2 text-xs">
                                </div>

                                <button type="submit" class="btn-outline text-xs">
                                    Renew
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                </article>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                <div class="card px-6 py-16 text-center">

                    <div class="mx-auto flex max-w-sm flex-col items-center">

                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                            <svg class="h-7 w-7"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M8 7h8M8 11h8M8 15h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />

                            </svg>

                        </div>

                        <h3 class="font-heading text-base font-semibold text-primary">
                            No contracts found
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Contracts created in the system will appear here.
                        </p>

                    </div>

                </div>

            <?php endif; ?>


            <?php if($contracts->hasPages()): ?>

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    <?php echo e($contracts->withQueryString()->links()); ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\marcj\Documents\fams-laravel-backup\resources\views/contracts/index.blade.php ENDPATH**/ ?>