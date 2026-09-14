<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecordRetentionRequest;
use App\Models\ArchiveDocument;
use App\Models\AuditLog;
use App\Models\RecordRetention;
use App\Models\RetentionPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Validation\ValidationException;

class RetentionController extends Controller
{
    public function __construct(
        private NotificationService $notifications
    ) {}


    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                \Illuminate\Validation\Rule::in([
                    'retained',
                    'review_required',
                    'extended',
                    'archived',
                    'marked_for_disposal',
                ]),
            ],

            'compliance' => [
                'nullable',
                \Illuminate\Validation\Rule::in([
                    'compliant',
                    'at_risk',
                    'non_compliant',
                ]),
            ],

            'record_type' => [
                'nullable',
                \Illuminate\Validation\Rule::in([
                    'document',
                    'contract',
                    'legal_record',
                    'other',
                ]),
            ],

            'attention' => [
                'nullable',
                \Illuminate\Validation\Rule::in([
                    'overdue',
                    'due_soon',
                    'needs_review',
                ]),
            ],

            'sort' => [
                'nullable',
                \Illuminate\Validation\Rule::in([
                    'updated_desc',
                    'review_asc',
                    'review_desc',
                    'title_asc',
                    'title_desc',
                ]),
            ],
        ]);

        $search = trim($filters['q'] ?? '');

        $status = $filters['status'] ?? null;
        $compliance = $filters['compliance'] ?? null;
        $recordType = $filters['record_type'] ?? null;
        $attention = $filters['attention'] ?? null;
        $sort = $filters['sort'] ?? 'updated_desc';

        $retentionQuery = RecordRetention::with('policy')
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $pattern = '%' . $search . '%';

                    $query->where(
                        function ($searchQuery) use ($search, $pattern) {
                            $searchQuery
                                ->whereRaw(
                                    'record_title ILIKE ?',
                                    [$pattern]
                                )
                                ->orWhereRaw(
                                    'policy_name ILIKE ?',
                                    [$pattern]
                                )
                                ->orWhereHas(
                                    'policy',
                                    function ($policyQuery) use ($pattern) {
                                        $policyQuery->whereRaw(
                                            'name ILIKE ?',
                                            [$pattern]
                                        );
                                    }
                                );

                            if (ctype_digit($search)) {
                                $searchQuery->orWhereRaw(
                                    'CAST(record_id AS TEXT) = ?',
                                    [$search]
                                );
                            }
                        }
                    );
                }
            )
            ->when(
                $status,
                fn ($query) =>
                    $query->where(
                        'status',
                        $status
                    )
            )
            ->when(
                $compliance,
                fn ($query) =>
                    $query->where(
                        'compliance_status',
                        $compliance
                    )
            )
            ->when(
                $recordType,
                fn ($query) =>
                    $query->where(
                        'record_type',
                        $recordType
                    )
            )
            ->when(
                $attention === 'overdue',
                fn ($query) =>
                    $query
                        ->whereNotNull('review_date')
                        ->whereDate(
                            'review_date',
                            '<',
                            today()
                        )
            )
            ->when(
                $attention === 'due_soon',
                fn ($query) =>
                    $query
                        ->whereNotNull('review_date')
                        ->whereDate(
                            'review_date',
                            '>=',
                            today()
                        )
                        ->whereDate(
                            'review_date',
                            '<=',
                            today()->addDays(30)
                        )
            )
            ->when(
                $attention === 'needs_review',
                fn ($query) =>
                    $query->where(
                        'status',
                        'review_required'
                    )
            );

        switch ($sort) {
            case 'review_asc':
                $retentionQuery
                    ->orderByRaw(
                        'review_date ASC NULLS LAST'
                    )
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id');

                break;

            case 'review_desc':
                $retentionQuery
                    ->orderByRaw(
                        'review_date DESC NULLS LAST'
                    )
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id');

                break;

            case 'title_asc':
                $retentionQuery
                    ->orderBy('record_title')
                    ->orderByDesc('id');

                break;

            case 'title_desc':
                $retentionQuery
                    ->orderByDesc('record_title')
                    ->orderByDesc('id');

                break;

            case 'updated_desc':
            default:
                $retentionQuery
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id');

                break;
        }

        $retentions = $retentionQuery
            ->paginate(15)
            ->withQueryString();

        $policies = RetentionPolicy::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();

        $allPolicies = RetentionPolicy::orderBy('name')
            ->get();

        $pendingDisposals = RecordRetention::with('policy')
            ->where(
                'disposition_status',
                'pending'
            )
            ->orderBy('disposition_requested_at')
            ->get();

        $stats = [
            'total' =>
                RecordRetention::count(),

            'compliant' =>
                RecordRetention::where(
                    'compliance_status',
                    'compliant'
                )->count(),

            'at_risk' =>
                RecordRetention::where(
                    'compliance_status',
                    'at_risk'
                )->count(),

            'review_required' =>
                RecordRetention::where(
                    'status',
                    'review_required'
                )->count(),
        ];

        return view(
            'retention.index',
            compact(
                'retentions',
                'policies',
                'allPolicies',
                'pendingDisposals',
                'stats'
            )
        );
    }

    public function store(RecordRetentionRequest $request): RedirectResponse
    {
        abort_unless(
            $request->user()->can('manageRetention'),
            403
        );

        $data = $request->validated();

        if (! empty($data['policy_id'])) {
            $policy = RetentionPolicy::findOrFail($data['policy_id']);
            $data['policy_name'] = $policy->name;
        } else {
            $data['policy_name'] = null;
        }

        $data['last_action_by'] = $request->user()->email;
        $data['last_action_at'] = now();

        $retention = RecordRetention::create($data);

        AuditLog::create([
            'actor_email' => $request->user()->email,
            'actor_role' => $request->user()->app_role,
            'action' => 'retention_action',
            'module' => 'retention',
            'record_label' => "Retention - {$retention->record_title}",
            'record_id' => $retention->id,
            'created_at' => now(),
        ]);

        return redirect()
            ->route('retention.index')
            ->with('status', 'Retention record created.');
    }

    public function update(
        RecordRetentionRequest $request,
        RecordRetention $retention
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageRetention'),
            403
        );

        $data = $request->validated();

        if (! empty($data['policy_id'])) {
            $policy = RetentionPolicy::findOrFail($data['policy_id']);
            $data['policy_name'] = $policy->name;
        } else {
            $data['policy_name'] = null;
        }

        $data['last_action_by'] = $request->user()->email;
        $data['last_action_at'] = now();

        $retention->update($data);

        AuditLog::create([
            'actor_email' => $request->user()->email,
            'actor_role' => $request->user()->app_role,
            'action' => 'retention_action',
            'module' => 'retention',
            'record_label' => "Retention - {$retention->record_title}",
            'record_id' => $retention->id,
            'details' => "Status: {$data['status']}",
            'created_at' => now(),
        ]);

        return redirect()
            ->route('retention.index')
            ->with('status', 'Retention record updated.');
    }

    public function assignDocument(
        Request $request,
        ArchiveDocument $document
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageRetention'),
            403
        );

        $data = $request->validate([
            'policy_id' => [
                'required',
                'integer',
                'exists:retention_policies,id',
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $policy = RetentionPolicy::query()
            ->whereKey($data['policy_id'])
            ->where('is_active', true)
            ->firstOrFail();

        if ($policy->record_category !== $document->category) {
            return back()->withErrors([
                'policy_id' =>
                    'The selected retention policy does not match this document category.',
            ]);
        }

        $startDate = ! empty($data['start_date'])
            ? \Illuminate\Support\Carbon::parse(
                $data['start_date']
            )->startOfDay()
            : (
                $document->document_date
                ?? $document->created_at
                ?? now()
            )->copy()->startOfDay();

        $reviewDate = $startDate
            ->copy()
            ->addYears(
                $policy->retention_years
            );

        $reviewRequired =
            $reviewDate->lte(
                now()->startOfDay()
            );

        $retention = RecordRetention::updateOrCreate(
            [
                'record_type' => 'document',
                'record_id' => $document->id,
            ],
            [
                'record_title' => $document->title,
                'policy_id' => $policy->id,
                'policy_name' => $policy->name,
                'start_date' => $startDate,
                'review_date' => $reviewDate,

                'status' =>
                    $reviewRequired
                        ? 'review_required'
                        : 'retained',

                'compliance_status' =>
                    $reviewRequired
                        ? 'at_risk'
                        : 'compliant',

                'last_action_by' =>
                    $request->user()->email,

                'last_action_at' => now(),

                'notes' =>
                    $data['notes'] ?? null,
            ]
        );

        AuditLog::create([
            'actor_email' =>
                $request->user()->email,

            'actor_role' =>
                $request->user()->app_role,

            'action' =>
                'retention_action',

            'module' =>
                'retention',

            'record_label' =>
                "Retention - {$document->title}",

            'record_id' =>
                $retention->id,

            'details' =>
                "Policy assigned: {$policy->name}",

            'created_at' => now(),
        ]);

        return redirect()
            ->route(
                'documents.show',
                $document
            )
            ->with(
                'status',
                'Retention policy assigned.'
            );
    }

    public function review(
        Request $request,
        RecordRetention $retention
    ) {
        abort_unless(
            $request->user()->can('manageRetention'),
            403
        );

        $retention->load('policy');

        $document = null;

        if (
            $retention->record_type === 'document' &&
            $retention->record_id
        ) {
            $document = ArchiveDocument::find(
                $retention->record_id
            );
        }

        return view(
            'retention.review',
            compact(
                'retention',
                'document'
            )
        );
    }


    public function decision(
        Request $request,
        RecordRetention $retention
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageRetention'),
            403
        );

        if (
            $retention->disposition_status ===
            'pending'
        ) {
            throw ValidationException::withMessages([
                'disposition' =>
                    'This record already has a pending disposal request. The authorized disposal review must be completed before another retention decision can be recorded.',
            ]);
        }

        $data = $request->validate([
            'decision' => [
                'required',
                'in:retain,extend,archive,mark_for_disposal',
            ],

            'review_date' => [
                'nullable',
                'date',
            ],

            'compliance_status' => [
                'required',
                'in:compliant,at_risk,non_compliant',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        if (
            in_array(
                $data['decision'],
                ['retain', 'extend'],
                true
            )
        ) {
            if (empty($data['review_date'])) {
                throw ValidationException::withMessages([
                    'review_date' =>
                        'A new review date is required when retaining or extending a record.',
                ]);
            }

            $reviewDate =
                \Illuminate\Support\Carbon::parse(
                    $data['review_date']
                )->startOfDay();

            if ($reviewDate->lte(today())) {
                throw ValidationException::withMessages([
                    'review_date' =>
                        'The next review date must be a future date.',
                ]);
            }
        } else {
            $reviewDate =
                $retention->review_date;
        }

        $newStatus = match (
            $data['decision']
        ) {
            'retain' =>
                'retained',

            'extend' =>
                'extended',

            'archive' =>
                'archived',

            'mark_for_disposal' =>
                'marked_for_disposal',
        };

        $decisionLabel = match (
            $data['decision']
        ) {
            'retain' =>
                'Retained',

            'extend' =>
                'Retention Extended',

            'archive' =>
                'Archived',

            'mark_for_disposal' =>
                'Marked for Disposal',
        };

        $dispositionData = [
            'disposition_status' => null,
            'disposition_requested_by' => null,
            'disposition_requested_at' => null,
            'disposition_reason' => null,
            'disposition_decided_by' => null,
            'disposition_decided_at' => null,
            'disposition_decision_notes' => null,
        ];

        if ($data['decision'] === 'mark_for_disposal') {
            $dispositionData = [
                'disposition_status' => 'pending',

                'disposition_requested_by' =>
                    $request->user()->email,

                'disposition_requested_at' =>
                    now(),

                'disposition_reason' =>
                    $data['notes']
                    ?: 'Record marked for disposal after retention review.',

                'disposition_decided_by' => null,
                'disposition_decided_at' => null,
                'disposition_decision_notes' => null,
            ];
        }

        $retention->update(array_merge([
            'status' =>
                $newStatus,

            'compliance_status' =>
                $data['compliance_status'],

            'review_date' =>
                $reviewDate,

            'last_action_by' =>
                $request->user()->email,

            'last_action_at' =>
                now(),

            'notes' =>
                $data['notes']
                ?: $retention->notes,
        ], $dispositionData));


        /*
         * Notify authorized disposal approvers.
         */
        if (
            $data['decision'] ===
            'mark_for_disposal'
        ) {
            $retention->refresh();

            $notifications = app(
                \App\Services\NotificationService::class
            );

            $requestKey =
                $retention
                    ->disposition_requested_at
                    ?->format('YmdHis')
                ?? now()->format('YmdHis');

            $approvers = $notifications
                ->usersWithPermission(
                    'approveRetentionDisposal'
                )
                ->reject(
                    fn ($user) =>
                        $user->email ===
                        $request->user()->email
                )
                ->values();

            $notifications->notifyOnce(
                $approvers
                    ->map(
                        fn ($user) => [
                            'recipient_email' =>
                                $user->email,

                            'title' =>
                                'Pending Disposal Approval',

                            'body' =>
                                "{$retention->record_title} has been marked for disposal and requires your approval.",

                            'module' =>
                                'retention',

                            'severity' =>
                                'warning',

                            'link' =>
                                route(
                                    'retention.disposition',
                                    $retention,
                                    false
                                ),

                            'key' =>
                                "disposal-request:{$retention->id}:{$requestKey}",
                        ]
                    )
                    ->all()
            );
        }


        /*
         * Synchronize linked Document Management
         * record without deleting the actual file.
         */
        if (
            $retention->record_type === 'document' &&
            $retention->record_id
        ) {
            $document = ArchiveDocument::find(
                $retention->record_id
            );

            if ($document) {

                $documentStatus =
                    $document->status;

                if (
                    in_array(
                        $data['decision'],
                        ['retain', 'extend'],
                        true
                    ) &&
                    $document->status ===
                        'needs_review'
                ) {
                    $documentStatus = 'active';
                }

                if (
                    $data['decision'] ===
                    'archive'
                ) {
                    $documentStatus = 'archived';
                }

                if (
                    $data['decision'] ===
                    'mark_for_disposal' &&
                    $document->status !== 'archived'
                ) {
                    /*
                     * Disposal requires another
                     * controlled step. Do not delete.
                     */
                    $documentStatus =
                        'needs_review';
                }

                $history =
                    $document->history ?? [];

                $history[] = [
                    'version' =>
                        $document->version ?? 1,

                    'action' =>
                        'retention_decision',

                    'by' =>
                        $request->user()->email,

                    'at' =>
                        now()->toISOString(),

                    'note' =>
                        "Retention decision: {$decisionLabel}." .
                        (
                            ! empty($data['notes'])
                            ? ' '.$data['notes']
                            : ''
                        ),
                ];

                $document->update([
                    'status' =>
                        $documentStatus,

                    'history' =>
                        $history,
                ]);
            }
        }


        AuditLog::create([
            'actor_email' =>
                $request->user()->email,

            'actor_role' =>
                $request->user()->app_role,

            'action' =>
                'retention_decision',

            'module' =>
                'retention',

            'record_label' =>
                "Retention - {$retention->record_title}",

            'record_id' =>
                $retention->id,

            'details' =>
                "Decision: {$decisionLabel}; " .
                "Compliance: {$data['compliance_status']}",

            'created_at' =>
                now(),
        ]);

        return redirect()
            ->route('retention.index')
            ->with(
                'status',
                "Retention decision saved: {$decisionLabel}."
            );
    }

    public function dispositionReview(
        Request $request,
        RecordRetention $retention
    ) {
        abort_unless(
            $request->user()->can(
                'approveRetentionDisposal'
            ),
            403
        );

        if (
            $retention->disposition_status !==
            'pending'
        ) {
            return redirect()
                ->route('retention.index')
                ->with(
                    'status',
                    'This record does not have a pending disposal request.'
                );
        }

        $retention->load('policy');

        $document = null;

        if (
            $retention->record_type === 'document' &&
            $retention->record_id
        ) {
            $document = ArchiveDocument::find(
                $retention->record_id
            );
        }

        return view(
            'retention.disposition',
            compact(
                'retention',
                'document'
            )
        );
    }


    public function approveDisposal(
        Request $request,
        RecordRetention $retention
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'approveRetentionDisposal'
            ),
            403
        );

        if (
            $retention->disposition_status !==
            'pending'
        ) {
            throw ValidationException::withMessages([
                'disposition' =>
                    'This disposal request is no longer pending.',
            ]);
        }

        if (
            $retention->disposition_requested_by ===
            $request->user()->email
        ) {
            throw ValidationException::withMessages([
                'disposition' =>
                    'You cannot approve your own disposal request.',
            ]);
        }

        $data = $request->validate([
            'decision_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $retention->update([
            'disposition_status' =>
                'approved',

            'disposition_decided_by' =>
                $request->user()->email,

            'disposition_decided_at' =>
                now(),

            'disposition_decision_notes' =>
                $data['decision_notes']
                ?? null,

            'last_action_by' =>
                $request->user()->email,

            'last_action_at' =>
                now(),
        ]);

        if (
            $retention->record_type === 'document' &&
            $retention->record_id
        ) {
            $document = ArchiveDocument::find(
                $retention->record_id
            );

            if ($document) {
                $history =
                    $document->history ?? [];

                $history[] = [
                    'version' =>
                        $document->version ?? 1,

                    'action' =>
                        'disposal_approved',

                    'by' =>
                        $request->user()->email,

                    'at' =>
                        now()->toISOString(),

                    'note' =>
                        'Retention disposal request approved. No file has been permanently deleted.',
                ];

                $document->update([
                    'status' =>
                        'needs_review',

                    'history' =>
                        $history,
                ]);
            }
        }

        AuditLog::create([
            'actor_email' =>
                $request->user()->email,

            'actor_role' =>
                $request->user()->app_role,

            'action' =>
                'disposal_approved',

            'module' =>
                'retention',

            'record_label' =>
                "Retention - {$retention->record_title}",

            'record_id' =>
                $retention->id,

            'details' =>
                'Disposal request approved. Permanent disposal has not been executed.',

            'created_at' =>
                now(),
        ]);

        /*
         * Notify the user who originally
         * requested the disposal.
         */
        if ($retention->disposition_requested_by) {

            $notifications = app(
                \App\Services\NotificationService::class
            );

            $requestKey =
                $retention
                    ->disposition_requested_at
                    ?->format('YmdHis')
                ?? (string) $retention->id;

            $notifications->notifyOnce([[
                'recipient_email' =>
                    $retention->disposition_requested_by,

                'title' =>
                    'Disposal Request Approved',

                'body' =>
                    "The disposal request for {$retention->record_title} has been approved. No file has been permanently deleted.",

                'module' =>
                    'retention',

                'severity' =>
                    'success',

                'link' =>
                    route(
                        'retention.review',
                        $retention,
                        false
                    ),

                'key' =>
                    "disposal-approved:{$retention->id}:{$requestKey}",
            ]]);
        }

        return redirect()
            ->route('retention.index')
            ->with(
                'status',
                'Disposal request approved. The record has not been permanently deleted.'
            );
    }

    public function rejectDisposal(
        Request $request,
        RecordRetention $retention
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'approveRetentionDisposal'
            ),
            403
        );

        if (
            $retention->disposition_status !==
            'pending'
        ) {
            throw ValidationException::withMessages([
                'disposition' =>
                    'This disposal request is no longer pending.',
            ]);
        }

        if (
            $retention->disposition_requested_by ===
            $request->user()->email
        ) {
            throw ValidationException::withMessages([
                'disposition' =>
                    'You cannot reject your own disposal request.',
            ]);
        }

        $data = $request->validate([
            'decision_notes' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $retention->update([
            'status' =>
                'review_required',

            'compliance_status' =>
                'at_risk',

            'disposition_status' =>
                'rejected',

            'disposition_decided_by' =>
                $request->user()->email,

            'disposition_decided_at' =>
                now(),

            'disposition_decision_notes' =>
                $data['decision_notes'],

            'last_action_by' =>
                $request->user()->email,

            'last_action_at' =>
                now(),
        ]);

        if (
            $retention->record_type === 'document' &&
            $retention->record_id
        ) {
            $document = ArchiveDocument::find(
                $retention->record_id
            );

            if ($document) {
                $history =
                    $document->history ?? [];

                $history[] = [
                    'version' =>
                        $document->version ?? 1,

                    'action' =>
                        'disposal_rejected',

                    'by' =>
                        $request->user()->email,

                    'at' =>
                        now()->toISOString(),

                    'note' =>
                        'Retention disposal request rejected. ' .
                        $data['decision_notes'],
                ];

                $document->update([
                    'status' =>
                        'needs_review',

                    'history' =>
                        $history,
                ]);
            }
        }

        AuditLog::create([
            'actor_email' =>
                $request->user()->email,

            'actor_role' =>
                $request->user()->app_role,

            'action' =>
                'disposal_rejected',

            'module' =>
                'retention',

            'record_label' =>
                "Retention - {$retention->record_title}",

            'record_id' =>
                $retention->id,

            'details' =>
                'Disposal request rejected: ' .
                $data['decision_notes'],

            'created_at' =>
                now(),
        ]);

        /*
         * Notify the user who originally
         * requested disposal.
         */
        if ($retention->disposition_requested_by) {

            $notifications = app(
                \App\Services\NotificationService::class
            );

            $requestKey =
                $retention
                    ->disposition_requested_at
                    ?->format('YmdHis')
                ?? (string) $retention->id;

            $notifications->notifyOnce([[
                'recipient_email' =>
                    $retention->disposition_requested_by,

                'title' =>
                    'Disposal Request Rejected',

                'body' =>
                    "The disposal request for {$retention->record_title} was rejected and returned for review.",

                'module' =>
                    'retention',

                'severity' =>
                    'warning',

                'link' =>
                    route(
                        'retention.review',
                        $retention,
                        false
                    ),

                'key' =>
                    "disposal-rejected:{$retention->id}:{$requestKey}",
            ]]);
        }

        return redirect()
            ->route('retention.index')
            ->with(
                'status',
                'Disposal request rejected and returned for review.'
            );
    }
}