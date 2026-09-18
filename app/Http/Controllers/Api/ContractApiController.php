<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Services\AuditService;
use App\Services\ContractWorkflowService;
use App\Services\DocumentFileStorageService;
use Illuminate\Http\Request;

class ContractApiController extends Controller
{
    public function __construct(
        private ContractWorkflowService $workflow,
        private AuditService $audit,
        private DocumentFileStorageService $fileStorage
    ) {}

    public function index(Request $request)
    {
        abort_unless(
            $request->user()->can('viewContracts'),
            403
        );

        $contracts = Contract::query()
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where(
                    'status',
                    $request->string('status')
                )
            )
            ->orderByDesc('updated_at')
            ->paginate(20);

        return ContractResource::collection(
            $contracts
        );
    }

    public function show(
        Request $request,
        Contract $contract
    ) {
        abort_unless(
            $request->user()->can('viewContracts'),
            403
        );

        return new ContractResource(
            $contract
        );
    }

    public function store(
        ContractRequest $request
    ) {
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

            $this->audit->log(
                $request->user(),
                'create',
                'contracts',
                "Contract \u{2022} {$contract->title}",
                (string) $contract->id
            );

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

        return (
            new ContractResource($contract)
        )
            ->response()
            ->setStatusCode(201);
    }
    public function update(
        ContractRequest $request,
        Contract $contract
    ) {
        abort_unless(
            $request->user()->can('manageContracts'),
            403
        );

        $data = $request->validated();
        $file = $request->file('file');

        unset($data['file']);

        $persist = function (
            array $payload
        ) use (
            $request,
            $contract
        ): Contract {
            $contract->update(
                $payload
            );

            $this->audit->log(
                $request->user(),
                'update',
                'contracts',
                "Contract \u{2022} {$contract->title}",
                (string) $contract->id
            );

            return $contract;
        };

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
            $persist(
                $data
            );
        }

        return new ContractResource(
            $contract->refresh()
        );
    }
    public function submitForReview(
        Request $request,
        Contract $contract
    ) {
        $contract = $this->workflow
            ->submitForReview(
                $request->user(),
                $contract
            );

        return new ContractResource(
            $contract
        );
    }

    public function legalReview(
        Request $request,
        Contract $contract
    ) {
        $data = $request->validate([
            'outcome' => [
                'required',
                'in:approved,objections,in_review',
            ],

            'comments' => [
                'nullable',
                'string',
            ],
        ]);

        $contract = $this->workflow
            ->legalReview(
                $request->user(),
                $contract,
                $data['outcome'],
                $data['comments'] ?? null
            );

        return new ContractResource(
            $contract
        );
    }

    public function decide(
        Request $request,
        Contract $contract
    ) {
        $data = $request->validate([
            'decision' => [
                'required',
                'in:approve,reject',
            ],

            'comments' => [
                'nullable',
                'string',
            ],
        ]);

        $contract = $this->workflow
            ->decide(
                $request->user(),
                $contract,
                $data['decision'] === 'approve',
                $data['comments'] ?? null
            );

        return new ContractResource(
            $contract
        );
    }
}