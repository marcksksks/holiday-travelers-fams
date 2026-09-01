@extends('layouts.app')
@section('title', 'Records Retention')
@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-1">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Track a Record</h2>
            <form method="POST" action="{{ route('retention.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Record title</label>
                    <input type="text" name="record_title" required class="input">
                </div>
                <div>
                    <label class="label">Record type</label>
                    <select name="record_type" class="input">
                        @foreach (['document','contract','legal_record','other'] as $t)
                            <option value="{{ $t }}">{{ str($t)->headline() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Policy</label>
                    <select name="policy_id" class="input">
                        <option value="">— none —</option>
                        @foreach ($policies as $policy)
                            <option value="{{ $policy->id }}">{{ $policy->name }} ({{ $policy->retention_years }}y)</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Start date</label>
                        <input type="date" name="start_date" class="input">
                    </div>
                    <div>
                        <label class="label">Review date</label>
                        <input type="date" name="review_date" class="input">
                    </div>
                </div>
                <input type="hidden" name="status" value="retained">
                <input type="hidden" name="compliance_status" value="compliant">
                <button type="submit" class="btn-primary w-full justify-center">Save</button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Record</th>
                            <th class="px-4 py-3">Policy</th>
                            <th class="px-4 py-3">Review date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Compliance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($retentions as $retention)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $retention->record_title }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $retention->policy_name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $retention->review_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-4 py-3"><span class="badge bg-slate-100 text-slate-700">{{ $retention->status }}</span></td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'badge',
                                        'bg-emerald-50 text-emerald-700' => $retention->compliance_status === 'compliant',
                                        'bg-amber-50 text-amber-700' => $retention->compliance_status === 'at_risk',
                                        'bg-red-50 text-red-700' => $retention->compliance_status === 'non_compliant',
                                    ])>{{ $retention->compliance_status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $retentions->links() }}</div>
        </div>
    </div>
@endsection
