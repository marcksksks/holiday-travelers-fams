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
        $contracts = Contract::when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('updated_at')->paginate(15)->withQueryString();

        return view('contracts.index', compact('contracts'));
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
