<?php

namespace App\Http\Controllers;

use App\Http\Requests\LegalRecordRequest;
use App\Models\AuditLog;
use App\Models\LegalRecord;
use App\Models\User;
use App\Services\DocumentFileStorageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalRecordController extends Controller
{
    public function __construct(
        private DocumentFileStorageService $fileStorage
    ) {}

    public function index(
        Request $request
    ): View {
        $filters = $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:255',
            ],

            'record_type' => [
                'nullable',
                'in:permit,license,legal_case,requirement,legal_document',
            ],

            'status' => [
                'nullable',
                'in:active,pending,expiring_soon,expired,renewed,closed',
            ],

            'review_status' => [
                'nullable',
                'in:not_reviewed,in_review,reviewed,action_required',
            ],

            'priority' => [
                'nullable',
                'in:low,medium,high,critical',
            ],

            'assigned_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'deadline' => [
                'nullable',
                'in:overdue,due_today,due_soon,expired,expiring_soon',
            ],
        ]);

        $recordsQuery =
            LegalRecord::query()
                ->with([
                    'assignedOfficer:id,full_name,email,app_role',
                ])
                ->withCount(
                    'archiveDocuments'
                );

        $this->applyFilters(
            $recordsQuery,
            $filters
        );

        $records =
            $recordsQuery
                ->orderByRaw(
                    "CASE priority
                        WHEN 'critical' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                        ELSE 5
                    END"
                )
                ->orderByDesc('updated_at')
                ->paginate(15)
                ->withQueryString();

        $today =
            now()->startOfDay();

        $kpis = [
            'total' => LegalRecord::count(),

            'active' => LegalRecord::where(
                'status',
                'active'
            )->count(),

            'action_required' => LegalRecord::where(
                'review_status',
                'action_required'
            )->count(),

            'due_soon' => LegalRecord::query()
                ->where(
                    'status',
                    '!=',
                    'closed'
                )
                ->where(
                    function (
                        Builder $query
                    ) use ($today): void {
                        $query
                            ->whereBetween(
                                'due_date',
                                [
                                    $today->toDateString(),
                                    $today
                                        ->copy()
                                        ->addDays(7)
                                        ->toDateString(),
                                ]
                            )
                            ->orWhereBetween(
                                'expiration_date',
                                [
                                    $today->toDateString(),
                                    $today
                                        ->copy()
                                        ->addDays(30)
                                        ->toDateString(),
                                ]
                            );
                    }
                )
                ->count(),

            'overdue' => LegalRecord::query()
                ->where(
                    'status',
                    '!=',
                    'closed'
                )
                ->where(
                    function (
                        Builder $query
                    ) use ($today): void {
                        $query
                            ->whereDate(
                                'due_date',
                                '<',
                                $today
                            )
                            ->orWhereDate(
                                'expiration_date',
                                '<',
                                $today
                            );
                    }
                )
                ->count(),
        ];

        $assignableOfficers =
            User::query()
                ->where(
                    'is_active',
                    true
                )
                ->whereIn(
                    'app_role',
                    [
                        User::ROLE_ADMIN_OFFICER,
                        User::ROLE_LEGAL_OFFICER,
                        User::ROLE_SYS_ADMIN,
                    ]
                )
                ->orderBy('full_name')
                ->get([
                    'id',
                    'full_name',
                    'email',
                    'app_role',
                ]);

        $recordIds =
            $records
                ->getCollection()
                ->pluck('id')
                ->map(
                    fn ($id) => (string) $id
                );

        $activities =
            AuditLog::query()
                ->where(
                    'module',
                    'legal'
                )
                ->whereIn(
                    'record_id',
                    $recordIds
                )
                ->orderByDesc(
                    'created_at'
                )
                ->get()
                ->groupBy(
                    fn (AuditLog $log) => (string) $log->record_id
                );

        return view(
            'legal.index',
            compact(
                'records',
                'kpis',
                'assignableOfficers',
                'activities',
                'filters'
            )
        );
    }

    public function edit(
        Request $request,
        LegalRecord $legal
    ): View {
        abort_unless(
            $request->user()
                ->can('manageLegal'),
            403
        );

        $assignableOfficers =
            User::query()
                ->where(
                    'is_active',
                    true
                )
                ->whereIn(
                    'app_role',
                    [
                        User::ROLE_ADMIN_OFFICER,
                        User::ROLE_LEGAL_OFFICER,
                        User::ROLE_SYS_ADMIN,
                    ]
                )
                ->orderBy('full_name')
                ->get([
                    'id',
                    'full_name',
                    'email',
                    'app_role',
                ]);

        return view(
            'legal.edit',
            compact(
                'legal',
                'assignableOfficers'
            )
        );
    }

    public function store(
        LegalRecordRequest $request
    ): RedirectResponse {
        abort_unless(
            $request->user()
                ->can('manageLegal'),
            403
        );

        $data =
            $this->preparePayload(
                $request->validated()
            );

        $file =
            $request->file('file');

        unset(
            $data['file']
        );

        $persist =
            function (
                array $payload
            ) use (
                $request
            ): LegalRecord {
                $record =
                    LegalRecord::create(
                        $payload
                    );

                AuditLog::create([
                    'actor_email' => $request->user()->email,

                    'actor_role' => $request->user()->app_role,

                    'action' => 'create',

                    'module' => 'legal',

                    'record_label' => "Legal • {$record->title}",

                    'record_id' => $record->id,

                    'details' => "Priority: {$record->priority}",

                    'created_at' => now(),
                ]);

                return $record;
            };

        if ($file) {
            $fileName =
                $file
                    ->getClientOriginalName();

            $this->fileStorage
                ->create(
                    $file,
                    'legal',
                    function (
                        string $newPath
                    ) use (
                        $persist,
                        $data,
                        $fileName
                    ): LegalRecord {
                        $payload =
                            $data;

                        $payload['file_uri'] =
                            $newPath;

                        $payload['file_name'] =
                            $fileName;

                        return $persist(
                            $payload
                        );
                    }
                );
        } else {
            $persist(
                $data
            );
        }

        return redirect()
            ->route(
                'legal.index'
            )
            ->with(
                'status',
                'Legal record created.'
            );
    }

    public function update(
        LegalRecordRequest $request,
        LegalRecord $legal
    ): RedirectResponse {
        abort_unless(
            $request->user()
                ->can('manageLegal'),
            403
        );

        $data =
            $this->preparePayload(
                $request->validated(),
                $legal
            );

        $file =
            $request->file('file');

        unset(
            $data['file']
        );

        $persist =
            function (
                array $payload
            ) use (
                $request,
                $legal
            ): LegalRecord {
                $legal->update(
                    $payload
                );

                AuditLog::create([
                    'actor_email' => $request->user()->email,

                    'actor_role' => $request->user()->app_role,

                    'action' => 'update',

                    'module' => 'legal',

                    'record_label' => "Legal • {$legal->title}",

                    'record_id' => $legal->id,

                    'details' => "Legal record updated • Priority: {$legal->priority}",

                    'created_at' => now(),
                ]);

                return $legal;
            };

        if ($file) {
            $fileName =
                $file
                    ->getClientOriginalName();

            $this->fileStorage
                ->replace(
                    $file,
                    'legal',
                    $legal->file_uri,
                    function (
                        string $newPath
                    ) use (
                        $persist,
                        $data,
                        $fileName
                    ): LegalRecord {
                        $payload =
                            $data;

                        $payload['file_uri'] =
                            $newPath;

                        $payload['file_name'] =
                            $fileName;

                        return $persist(
                            $payload
                        );
                    }
                );
        } else {
            $persist(
                $data
            );
        }

        return redirect()
            ->route(
                'legal.index'
            )
            ->with(
                'status',
                'Legal record updated.'
            );
    }

    public function review(
        Request $request,
        LegalRecord $legal
    ): RedirectResponse {
        abort_unless(
            $request->user()
                ->can('reviewLegal'),
            403
        );

        $data =
            $request->validate([
                'review_status' => [
                    'required',
                    'in:not_reviewed,in_review,reviewed,action_required',
                ],

                'legal_notes' => [
                    'nullable',
                    'string',
                ],
            ]);

        $legal->update([
            'review_status' => $data['review_status'],

            'legal_notes' => $data['legal_notes']
                ?? $legal->legal_notes,
        ]);

        AuditLog::create([
            'actor_email' => $request->user()->email,

            'actor_role' => $request->user()->app_role,

            'action' => 'update',

            'module' => 'legal',

            'record_label' => "Legal • {$legal->title}",

            'record_id' => $legal->id,

            'details' => "Review: {$data['review_status']}",

            'created_at' => now(),
        ]);

        return back()->with(
            'status',
            'Legal review recorded.'
        );
    }

    private function applyFilters(
        Builder $query,
        array $filters
    ): void {
        if (
            filled(
                $filters['q']
                ?? null
            )
        ) {
            $search =
                '%'.
                mb_strtolower(
                    trim(
                        $filters['q']
                    )
                ).
                '%';

            $query->where(
                function (
                    Builder $query
                ) use ($search): void {
                    $query
                        ->whereRaw(
                            'LOWER(title) LIKE ?',
                            [$search]
                        )
                        ->orWhereRaw(
                            'LOWER(reference_number) LIKE ?',
                            [$search]
                        )
                        ->orWhereRaw(
                            'LOWER(issuing_authority) LIKE ?',
                            [$search]
                        )
                        ->orWhereRaw(
                            'LOWER(legal_category) LIKE ?',
                            [$search]
                        )
                        ->orWhereRaw(
                            'LOWER(jurisdiction) LIKE ?',
                            [$search]
                        );
                }
            );
        }

        foreach (
            [
                'record_type',
                'status',
                'review_status',
                'priority',
                'assigned_user_id',
            ] as $filter
        ) {
            if (
                filled(
                    $filters[$filter]
                    ?? null
                )
            ) {
                $query->where(
                    $filter,
                    $filters[$filter]
                );
            }
        }

        $deadline =
            $filters['deadline']
            ?? null;

        $today =
            now()->startOfDay();

        if (
            $deadline ===
            'overdue'
        ) {
            $query
                ->where(
                    'status',
                    '!=',
                    'closed'
                )
                ->whereDate(
                    'due_date',
                    '<',
                    $today
                );
        }

        if (
            $deadline ===
            'due_today'
        ) {
            $query->whereDate(
                'due_date',
                $today
            );
        }

        if (
            $deadline ===
            'due_soon'
        ) {
            $query
                ->whereDate(
                    'due_date',
                    '>=',
                    $today
                )
                ->whereDate(
                    'due_date',
                    '<=',
                    $today
                        ->copy()
                        ->addDays(7)
                );
        }

        if (
            $deadline ===
            'expired'
        ) {
            $query
                ->where(
                    'status',
                    '!=',
                    'closed'
                )
                ->whereDate(
                    'expiration_date',
                    '<',
                    $today
                );
        }

        if (
            $deadline ===
            'expiring_soon'
        ) {
            $query
                ->whereDate(
                    'expiration_date',
                    '>=',
                    $today
                )
                ->whereDate(
                    'expiration_date',
                    '<=',
                    $today
                        ->copy()
                        ->addDays(30)
                );
        }
    }

    private function preparePayload(
        array $data,
        ?LegalRecord $legal = null
    ): array {
        if (
            filled(
                $data['assigned_user_id']
                ?? null
            )
        ) {
            $email =
                User::query()
                    ->whereKey(
                        $data['assigned_user_id']
                    )
                    ->value('email');

            if ($email) {
                $data['responsible_officer_email'] =
                    $email;
            }
        }

        if (
            array_key_exists(
                'status',
                $data
            )
        ) {
            if (
                $data['status'] ===
                'closed'
            ) {
                $data['closed_at'] =
                    $legal?->closed_at
                    ?? now();
            } else {
                $data['closed_at'] =
                    null;
            }
        }

        return $data;
    }
}
