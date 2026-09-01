@extends('layouts.app')
@section('title', 'Legal Records')
@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        @can('manageLegal')
        <div class="card p-5 lg:col-span-1">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Add Legal Record</h2>
            <form method="POST" action="{{ route('legal.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Title</label>
                    <input type="text" name="title" required class="input">
                </div>
                <div>
                    <label class="label">Type</label>
                    <select name="record_type" class="input">
                        @foreach (['permit','license','legal_case','requirement','legal_document'] as $t)
                            <option value="{{ $t }}">{{ str($t)->headline() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Reference number</label>
                    <input type="text" name="reference_number" class="input">
                </div>
                <div>
                    <label class="label">Issuing authority</label>
                    <input type="text" name="issuing_authority" class="input">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Issue date</label>
                        <input type="date" name="issue_date" class="input">
                    </div>
                    <div>
                        <label class="label">Expiration</label>
                        <input type="date" name="expiration_date" class="input">
                    </div>
                </div>
                <input type="hidden" name="status" value="active">
                <button type="submit" class="btn-primary w-full justify-center">Save</button>
            </form>
        </div>
        @endcan

        <div class="{{ auth()->user()->can('manageLegal') ? 'lg:col-span-2' : 'lg:col-span-3' }}">
            <div class="card overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Expiration</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Review</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($records as $record)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $record->title }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ str($record->record_type)->headline() }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $record->expiration_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-4 py-3"><span class="badge bg-slate-100 text-slate-700">{{ $record->status }}</span></td>
                                <td class="px-4 py-3">
                                    @can('reviewLegal')
                                        <form method="POST" action="{{ route('legal.review', $record) }}" class="flex items-center gap-2">
                                            @csrf
                                            <select name="review_status" onchange="this.form.submit()" class="input py-1 text-xs">
                                                @foreach (['not_reviewed','in_review','reviewed','action_required'] as $status)
                                                    <option value="{{ $status }}" @selected($record->review_status === $status)>{{ str($status)->headline() }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @else
                                        <span class="text-slate-500">{{ $record->review_status }}</span>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $records->links() }}</div>
        </div>
    </div>
@endsection
