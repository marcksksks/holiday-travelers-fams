@extends('layouts.app')
@section('title', 'Visitor Desk')
@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-1">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Log a Visitor</h2>
            <form method="POST" action="{{ route('visitors.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Full name</label>
                    <input type="text" name="full_name" required class="input">
                </div>
                <div>
                    <label class="label">Organization</label>
                    <input type="text" name="organization" class="input">
                </div>
                <div>
                    <label class="label">Visitor type</label>
                    <select name="visitor_type" class="input">
                        @foreach (['customer','business_partner','supplier','government','applicant','guest','other'] as $type)
                            <option value="{{ $type }}">{{ str($type)->headline() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Host email</label>
                    <input type="email" name="host_email" class="input">
                </div>
                <div>
                    <label class="label">Purpose</label>
                    <textarea name="purpose" rows="2" class="input"></textarea>
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_walk_in" value="1" checked> Walk-in (no prior appointment)
                </label>
                <button type="submit" class="btn-primary w-full justify-center">Log Visitor</button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="mb-3 flex gap-2 text-sm">
                @foreach (['' => 'All', 'expected' => 'Expected', 'checked_in' => 'Checked In', 'completed' => 'Completed'] as $value => $label)
                    <a href="{{ route('visitors.index', array_filter(['status' => $value])) }}"
                       class="rounded-full px-3 py-1 {{ request('status', '') === $value ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="card overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Visitor</th>
                            <th class="px-4 py-3">Host</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Check-in</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($visitors as $visitor)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $visitor->full_name }}<div class="text-xs text-slate-400">{{ $visitor->organization }}</div></td>
                                <td class="px-4 py-3 text-slate-600">{{ $visitor->host_name ?: $visitor->host_email }}</td>
                                <td class="px-4 py-3"><span class="badge bg-slate-100 text-slate-700">{{ $visitor->status }}</span></td>
                                <td class="px-4 py-3 text-slate-600">{{ optional($visitor->check_in_at)->format('H:i') }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    @can('operateVisitorDesk')
                                        @if (in_array($visitor->status, ['expected', 'awaiting_host']))
                                            <form method="POST" action="{{ route('visitors.check-in', $visitor) }}" class="inline">
                                                @csrf
                                                <button class="text-emerald-600 hover:underline">Check in</button>
                                            </form>
                                        @endif
                                        @if ($visitor->status === 'checked_in')
                                            <form method="POST" action="{{ route('visitors.check-out', $visitor) }}" class="inline">
                                                @csrf
                                                <button class="text-slate-600 hover:underline">Check out</button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $visitors->links() }}</div>
        </div>
    </div>
@endsection
