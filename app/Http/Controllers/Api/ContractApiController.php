<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Services\ContractWorkflowService;
use Illuminate\Http\Request;

class ContractApiController extends Controller
{
    public function __construct(private ContractWorkflowService $workflow) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->can('manageContracts') || $request->user()->can('viewContracts'), 403);
        $contracts = Contract::when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('updated_at')->paginate(20);

        return ContractResource::collection($contracts);
    }

    public function show(Request $request, Contract $contract)
    {
        abort_unless($request->user()->can('manageContracts') || $request->user()->can('viewContracts'), 403);
        return new ContractResource($contract);
    }

    public function store(ContractRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_uri'] = $request->file('file')->store('contracts', 'documents');
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        $contract = Contract::create($data);

        return (new ContractResource($contract))->response()->setStatusCode(201);
    }

    public function update(ContractRequest $request, Contract $contract)
    {
        $data = $request->validated();
        $oldPath = $contract->file_uri;

        if ($request->hasFile('file')) {
            $data['file_uri'] = $request->file('file')->store('contracts', 'documents');
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        $contract->update($data);
        if ($request->hasFile('file') && $oldPath && $oldPath !== $contract->file_uri) {
            \Illuminate\Support\Facades\Storage::disk('documents')->delete($oldPath);
        }

        return new ContractResource($contract);
    }

    public function submitForReview(Request $request, Contract $contract)
    {
        return new ContractResource($this->workflow->submitForReview($request->user(), $contract));
    }

    public function legalReview(Request $request, Contract $contract)
    {
        $data = $request->validate(['outcome' => ['required', 'in:approved,objections,in_review'], 'comments' => ['nullable', 'string']]);

        return new ContractResource($this->workflow->legalReview($request->user(), $contract, $data['outcome'], $data['comments'] ?? null));
    }

    public function decide(Request $request, Contract $contract)
    {
        $data = $request->validate(['decision' => ['required', 'in:approve,reject'], 'comments' => ['nullable', 'string']]);

        return new ContractResource($this->workflow->decide($request->user(), $contract, $data['decision'] === 'approve', $data['comments'] ?? null));
    }
}
