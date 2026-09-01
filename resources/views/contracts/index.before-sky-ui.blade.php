@extends('layouts.app')
@section('title', 'Contracts')
@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        @can('manageContracts')
        <div class="card p-5 lg:col-span-1">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">New Contract (Draft)</h2>
            <form method="POST" action="{{ route('contracts.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Title</label>
                    <input type="text" name="title" required class="input">
                </div>
                <div>
                    <label class="label">Type</label>
                    <select name="contract_type" class="input">
                        @foreach (['hotel','tour_operator','transportation','supplier','partnership','service','other'] as $t)
                            <option value="{{ $t }}">{{ str($t)->headline() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Start date</label>
                        <input type="date" name="start_date" class="input">
                    </div>
                    <div>
                        <label class="label">End date</label>
                        <input type="date" name="end_date" class="input">
                    </div>
                </div>
                <div>
                    <label class="label">Value</label>
                    <input type="number" step="0.01" name="value" class="input">
                </div>
                <div>
                    <label class="label">Responsible officer</label>
                    <input type="email" name="responsible_officer_email" value="{{ auth()->user()->email }}" class="input">
                </div>
                <div>
                    <label class="label">File</label>
                    <input type="file" name="file" class="input">
                </div>
                <button type="submit" class="btn-primary w-full justify-center">Save Draft</button>
            </form>
        </div>
        @endcan

        <div class="{{ auth()->user()->can('manageContracts') ? 'lg:col-span-2' : 'lg:col-span-3' }} space-y-4">
            @foreach ($contracts as $contract)
                <div class="card p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium">{{ $contract->title }} <span class="text-xs text-slate-400">v{{ $contract->version }}</span></p>
                            <p class="text-xs text-slate-500">{{ str($contract->contract_type)->headline() }} · {{ $contract->end_date?->format('M d, Y') }}</p>
                        </div>
                        <span class="badge bg-slate-100 text-slate-700">{{ $contract->status }}</span>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-500">
                        <span>Legal review: <strong>{{ $contract->legal_review_status }}</strong></span>
                        <span>·</span>
                        <span>Approval: <strong>{{ $contract->approval_status }}</strong></span>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">
                        @can('manageContracts')
                            @if ($contract->status === 'draft')
                                <form method="POST" action="{{ route('contracts.submit-review', $contract) }}">
                                    @csrf
                                    <button class="btn-secondary text-xs">Submit for legal review</button>
                                </form>
                            @endif
                        @endcan
                        @can('reviewLegal')
                            @if (in_array($contract->legal_review_status, ['pending', 'in_review']))
                                <form method="POST" action="{{ route('contracts.legal-review', $contract) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="outcome" value="approved">
                                    <button class="btn-secondary text-xs">Clear legal review</button>
                                </form>
                                <form method="POST" action="{{ route('contracts.legal-review', $contract) }}">
                                    @csrf
                                    <input type="hidden" name="outcome" value="objections">
                                    <button class="text-xs text-red-600 hover:underline">Raise objections</button>
                                </form>
                            @endif
                        @endcan
                        @can('approveContracts')
                            @if ($contract->approval_status === 'pending')
                                <form method="POST" action="{{ route('contracts.decide', $contract) }}">
                                    @csrf
                                    <input type="hidden" name="decision" value="approve">
                                    <button class="btn-secondary text-xs">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('contracts.decide', $contract) }}">
                                    @csrf
                                    <input type="hidden" name="decision" value="reject">
                                    <button class="text-xs text-red-600 hover:underline">Reject</button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>
            @endforeach
            <div>{{ $contracts->links() }}</div>
        </div>
    </div>
@endsection
