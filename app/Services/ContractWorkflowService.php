<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/** Port of base44/functions/contractWorkflow/entry.ts */
class ContractWorkflowService
{
    public function __construct(
        private AuditService $audit,
        private NotificationService $notifications,
    ) {}

    public function submitForReview(User $user, Contract $contract): Contract
    {
        $this->authorize($user, [User::ROLE_ADMIN_OFFICER, User::ROLE_SYS_ADMIN], 'Only the administrative officer can submit contracts for review.');
        if (! in_array($contract->status, ['draft', 'renewed'], true)) {
            throw ValidationException::withMessages(['status' => 'Only draft or renewed contracts can be submitted for review.']);
        }

        $contract->update([
            'status' => 'under_review',
            'legal_review_status' => 'pending',
            'approval_status' => 'not_submitted',
        ]);

        $legal = $this->notifications->usersByRole(User::ROLE_LEGAL_OFFICER);
        $this->notifications->notify($legal->map(fn ($l) => [
            'recipient_email' => $l->email,
            'title' => 'Contract awaiting legal review',
            'body' => "{$contract->title} was submitted for legal review.",
            'module' => 'contracts',
            'severity' => 'info',
            'link' => '/contracts',
        ])->all());

        $this->audit->log($user, 'update', 'contracts', $this->label($contract), (string) $contract->id, 'Submitted for legal review');

        return $contract->refresh();
    }

    public function legalReview(User $user, Contract $contract, string $outcome, ?string $comments = null): Contract
    {
        $this->authorize($user, [User::ROLE_LEGAL_OFFICER, User::ROLE_SYS_ADMIN], 'Only the legal officer can record a legal review.');

        if (! in_array($contract->status, ['under_review'], true)) {
            throw ValidationException::withMessages(['status' => 'This contract is not awaiting legal review.']);
        }

        if (! in_array($outcome, ['approved', 'objections', 'in_review'], true)) {
            throw ValidationException::withMessages(['outcome' => 'A review outcome is required.']);
        }

        $contract->update([
            'legal_review_status' => $outcome,
            'legal_review_notes' => $comments ?? '',
            'legal_reviewed_by' => $user->email,
            'legal_reviewed_at' => now(),
            'status' => $outcome === 'approved' ? 'pending_approval' : 'under_review',
            'approval_status' => $outcome === 'approved' ? 'pending' : 'not_submitted',
        ]);

        if ($outcome === 'approved') {
            $managers = $this->notifications->usersByRole(User::ROLE_MANAGER);
            $this->notifications->notify($managers->map(fn ($m) => [
                'recipient_email' => $m->email,
                'title' => 'Contract awaiting your approval',
                'body' => "{$contract->title} cleared legal review and needs management approval.",
                'module' => 'contracts',
                'severity' => 'warning',
                'link' => '/contracts',
            ])->all());
        }

        $this->notifications->notify([[
            'recipient_email' => $contract->responsible_officer_email,
            'title' => "Legal review: {$outcome}",
            'body' => "{$contract->title} — ".($comments ?: 'no comments'),
            'module' => 'contracts',
            'severity' => $outcome === 'objections' ? 'critical' : 'info',
            'link' => '/contracts',
        ]]);

        $this->audit->log($user, 'update', 'contracts', $this->label($contract), (string) $contract->id, "Legal review {$outcome}");

        return $contract->refresh();
    }

    public function decide(User $user, Contract $contract, bool $approved, ?string $comments = null): Contract
    {
        $this->authorize($user, [User::ROLE_MANAGER, User::ROLE_SYS_ADMIN], 'Only management can approve or reject contracts.');

        if ($contract->status !== 'pending_approval' || $contract->legal_review_status !== 'approved') {
            throw ValidationException::withMessages(['status' => 'Only contracts that passed legal review can be approved.']);
        }

        if (in_array($contract->legal_review_status, ['pending', 'in_review'], true)) {
            throw ValidationException::withMessages(['legal_review_status' => 'Legal review must be completed before approval.']);
        }

        $contract->update([
            'approval_status' => $approved ? 'approved' : 'rejected',
            'approved_by' => $user->email,
            'approved_at' => now(),
            'status' => $approved ? 'active' : 'draft',
        ]);

        $this->notifications->notify([[
            'recipient_email' => $contract->responsible_officer_email,
            'title' => 'Contract '.($approved ? 'approved' : 'rejected'),
            'body' => "{$contract->title} was ".($approved ? 'approved and is now active' : 'returned to draft').'.',
            'module' => 'contracts',
            'severity' => $approved ? 'success' : 'warning',
            'link' => '/contracts',
        ]]);

        $this->audit->log($user, $approved ? 'approve' : 'reject', 'contracts', $this->label($contract), (string) $contract->id, $comments ?? '');

        return $contract->refresh();
    }

    public function renew(User $user, Contract $contract, string $newEndDate, ?string $comments = null): Contract
    {
        $this->authorize($user, [User::ROLE_ADMIN_OFFICER, User::ROLE_MANAGER, User::ROLE_SYS_ADMIN], 'You are not authorised to renew contracts.');
        if (! in_array($contract->status, ['active', 'expired', 'renewed'], true)) {
            throw ValidationException::withMessages(['status' => 'Only active, expired, or renewed contracts can be renewed.']);
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $newEndDate)) {
            throw ValidationException::withMessages(['new_end_date' => 'A valid renewal end date is required.']);
        }
        if ($contract->start_date && $newEndDate < $contract->start_date->toDateString()) {
            throw ValidationException::withMessages(['new_end_date' => 'Renewal end date cannot be before the contract start date.']);
        }

        $renewals = $contract->renewals ?? [];
        $renewals[] = [
            'renewed_at' => now()->toISOString(),
            'by' => $user->email,
            'new_end_date' => $newEndDate,
            'note' => $comments ?? '',
        ];

        $contract->update([
            'end_date' => $newEndDate,
            'status' => 'renewed',
            'version' => ($contract->version ?? 1) + 1,
            'legal_review_status' => 'pending',
            'approval_status' => 'not_submitted',
            'renewals' => $renewals,
        ]);

        $this->audit->log($user, 'update', 'contracts', $this->label($contract), (string) $contract->id, "Renewed until {$newEndDate}");

        return $contract->refresh();
    }

    private function authorize(User $user, array $roles, string $message): void
    {
        if (! $user->hasRole($roles)) {
            throw ValidationException::withMessages(['app_role' => $message])->status(403);
        }
    }

    private function label(Contract $contract): string
    {
        return 'Contract • '.($contract->contract_number ?: $contract->title);
    }
}
