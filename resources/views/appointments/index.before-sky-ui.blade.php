@extends('layouts.app')
@section('title', 'Appointments')
@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-1">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Schedule a Visit</h2>
            <form method="POST" action="{{ route('appointments.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Visitor name</label>
                    <input type="text" name="visitor_name" required class="input">
                </div>
                <div>
                    <label class="label">Organization</label>
                    <input type="text" name="visitor_organization" class="input">
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
                    <label class="label">Date</label>
                    <input type="date" name="date" required class="input">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Start</label>
                        <input type="time" name="start_time" required class="input">
                    </div>
                    <div>
                        <label class="label">End</label>
                        <input type="time" name="end_time" class="input">
                    </div>
                </div>
                <div>
                    <label class="label">Room (optional)</label>
                    <select name="facility_id" class="input">
                        <option value="">— none —</option>
                        @foreach ($facilities as $facility)
                            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="status" value="scheduled">
                <button type="submit" class="btn-primary w-full justify-center">Schedule</button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Visitor</th>
                            <th class="px-4 py-3">Host</th>
                            <th class="px-4 py-3">When</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($appointments as $appointment)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $appointment->visitor_name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $appointment->host_name ?: $appointment->host_email }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $appointment->date->format('M d') }}, {{ $appointment->start_time }}</td>
                                <td class="px-4 py-3"><span class="badge bg-slate-100 text-slate-700">{{ $appointment->status }}</span></td>
                                <td class="px-4 py-3 text-right">
                                    @if (! in_array($appointment->status, ['cancelled', 'completed']))
                                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $appointments->links() }}</div>
        </div>
    </div>
@endsection
