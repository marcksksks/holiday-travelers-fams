@extends('layouts.app')
@section('title', 'Audit Trail')
@section('content')
    <form method="GET" class="mb-4 flex flex-wrap gap-3">
        <input type="text" name="actor_email" value="{{ request('actor_email') }}" placeholder="Filter by actor email" class="input max-w-xs">
        <select name="module" class="input max-w-xs">
            <option value="">All modules</option>
            @foreach (['facilities','appointments','visitors','documents','legal','contracts','retention','users','system'] as $module)
                <option value="{{ $module }}" @selected(request('module') === $module)>{{ str($module)->headline() }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary">Filter</button>
    </form>

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">When</th>
                    <th class="px-4 py-3">Actor</th>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">Module</th>
                    <th class="px-4 py-3">Record</th>
                    <th class="px-4 py-3">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($logs as $log)
                    <tr>
                        <td class="px-4 py-3 text-slate-500">{{ $log->created_at->format('M d, H:i') }}</td>
                        <td class="px-4 py-3">{{ $log->actor_email }} <span class="text-xs text-slate-400">({{ $log->actor_role }})</span></td>
                        <td class="px-4 py-3"><span class="badge bg-slate-100 text-slate-700">{{ $log->action }}</span></td>
                        <td class="px-4 py-3 text-slate-600">{{ $log->module }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $log->record_label }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $log->details }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
