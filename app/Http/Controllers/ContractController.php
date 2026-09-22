<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Models\AuditLog;
use App\Models\Contract;
use App\Services\ContractWorkflowService;
use App\Services\DocumentFileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct(
        private ContractWorkflowService $workflow,
        private DocumentFileStorageService $fileStorage
    ) {}

    public function index(Request $request)
    {
        $search =
            trim(
                $request
                    ->string('q')
                    ->toString()
            );

        $status =
            $request
                ->string('status')
                ->toString();

        $deadline =
            $request
                ->string('deadline')
                ->toString();

        $contracts =
            Contract::query()
                ->when(
                    $search !== '',
                    function ($query) use ($search): void {
                        $needle =
                            '%'.
                            mb_strtolower(
                                $search
                            ).
                            '%';

                        $query->where(
                            function ($searchQuery) use ($needle): void {
                                $searchQuery
                                    ->whereRaw(
                                        'LOWER(title) LIKE ?',
                                        [$needle]
                                    )
                                    ->orWhereRaw(
                                        'LOWER(COALESCE(contract_number, ?)) LIKE ?',
                                        ['', $needle]
                                    )
                                    ->orWhereRaw(
                                        'LOWER(COALESCE(responsible_officer_email, ?)) LIKE ?',
                                        ['', $needle]
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $status !== '',
                    fn ($query) => $query->where(
                        'status',
                        $status
                    )
                )
                ->when(
                    $deadline === 'attention',
                    fn ($query) => $query
                        ->whereIn(
                            'status',
                            [
                                'active',
                                'renewed',
                                'expired',
                            ]
                        )
                        ->whereNotNull(
                            'end_date'
                        )
                        ->whereDate(
                            'end_date',
                            '<=',
                            today()
                                ->addDays(30)
                                ->toDateString()
                        )
                )
                ->when(
                    $deadline === 'due_soon',
                    fn ($query) => $query
                        ->whereNotNull(
                            'end_date'
                        )
                        ->whereDate(
                            'end_date',
                            '>=',
                            today()
                                ->toDateString()
                        )
                        ->whereDate(
                            'end_date',
                            '<=',
                            today()
                                ->addDays(30)
                                ->toDateString()
                        )
                )
                ->when(
                    $deadline === 'expired',
                    fn ($query) => $query
                        ->whereNotNull(
                            'end_date'
                        )
                        ->whereDate(
                            'end_date',
                            '<',
                            today()
                                ->toDateString()
                        )
                )
                ->when(
                    $deadline === 'open_ended',
                    fn ($query) => $query->whereNull(
                        'end_date'
                    )
                )
                ->orderByDesc(
                    'updated_at'
                )
                ->paginate(15)
                ->withQueryString();

        $contractStats = [
            'total' => Contract::query()
                ->count(),

            'active' => Contract::query()
                ->where(
                    'status',
                    'active'
                )
                ->count(),

            'workflow' => Contract::query()
                ->where(
                    function ($query): void {
                        $query
                            ->whereIn(
                                'status',
                                [
                                    'under_review',
                                    'pending_approval',
                                ]
                            )
                            ->orWhere(
                                'legal_review_status',
                                'objections'
                            );
                    }
                )
                ->count(),

            'renewal_attention' => Contract::query()
                ->whereIn(
                    'status',
                    [
                        'active',
                        'renewed',
                        'expired',
                    ]
                )
                ->whereNotNull(
                    'end_date'
                )
                ->whereDate(
                    'end_date',
                    '<=',
                    today()
                        ->addDays(30)
                        ->toDateString()
                )
                ->count(),
        ];

        return view(
            'contracts.index',
            compact(
                'contracts',
                'contractStats',
                'search',
                'status',
                'deadline'
            )
        );
    }

    public function edit(Request $request, Contract $contract)
    {
        abort_unless(
            $request->user()->can('manageContracts'),
            403
        );

        return view('contracts.edit', compact('contract'));
    }

    public function store(ContractRequest $request): RedirectResponse
    {
        abort_unless(
            $request->user()->can('manageContracts'),
            403
        );

        $data = $request->validated();
        $file = $request->file('file');

        unset($data['file']);

        $persist = function (
            array $payload
        ) use ($request): Contract {
            $contract = Contract::create(
                $payload
            );

            AuditLog::create([
                'actor_email' => $request->user()->email,
                'actor_role' => $request->user()->app_role,
                'action' => 'create',
                'module' => 'contracts',
                'record_label' => "Contract • {$contract->title}",
                'record_id' => $contract->id,
                'created_at' => now(),
            ]);

            return $contract;
        };

        if ($file) {
            $fileName =
                $file->getClientOriginalName();

            $contract =
                $this->fileStorage->create(
                    $file,
                    'contracts',
                    function (
                        string $newPath
                    ) use (
                        $persist,
                        $data,
                        $fileName
                    ): Contract {
                        $payload = $data;

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
            $contract = $persist(
                $data
            );
        }

        return redirect()
            ->route('contracts.index')
            ->with(
                'status',
                'Contract created as draft.'
            );
    }

    public function update(
        ContractRequest $request,
        Contract $contract
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageContracts'),
            403
        );

        $data = $request->validated();
        $file = $request->file('file');

        unset($data['file']);

        if ($file) {
            $fileName =
                $file->getClientOriginalName();

            $this->fileStorage->replace(
                $file,
                'contracts',
                $contract->file_uri,
                function (
                    string $newPath
                ) use (
                    $contract,
                    $data,
                    $fileName
                ): Contract {
                    $payload = $data;

                    $payload['file_uri'] =
                        $newPath;

                    $payload['file_name'] =
                        $fileName;

                    $contract->update(
                        $payload
                    );

                    return $contract;
                }
            );
        } else {
            $contract->update(
                $data
            );
        }

        return redirect()
            ->route('contracts.index')
            ->with(
                'status',
                'Contract updated.'
            );
    }

    public function submitForReview(Request $request, Contract $contract): RedirectResponse
    {
        $this->workflow->submitForReview($request->user(), $contract);

        return back()->with('status', 'Submitted for legal review.');
    }

    public function legalReview(Request $request, Contract $contract): RedirectResponse
    {
        $data = $request->validate([
            'outcome' => ['required', 'in:approved,objections,in_review'],
            'comments' => ['nullable', 'string'],
        ]);

        $this->workflow->legalReview($request->user(), $contract, $data['outcome'], $data['comments'] ?? null);

        return back()->with('status', 'Legal review recorded.');
    }

    public function decide(Request $request, Contract $contract): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'comments' => ['nullable', 'string'],
        ]);

        $this->workflow->decide($request->user(), $contract, $data['decision'] === 'approve', $data['comments'] ?? null);

        return back()->with('status', 'Decision recorded.');
    }

    public function renew(Request $request, Contract $contract): RedirectResponse
    {
        abort_unless(
            $request->user()->can('manageContracts'),
            403
        );

        $data = $request->validate([
            'new_end_date' => ['required', 'date', 'after:today'],
            'comments' => ['nullable', 'string'],
        ]);

        $this->workflow->renew($request->user(), $contract, $data['new_end_date'], $data['comments'] ?? null);

        return back()->with('status', 'Contract renewed.');
    }
}
