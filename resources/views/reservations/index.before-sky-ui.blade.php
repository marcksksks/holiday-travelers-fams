@extends('layouts.app')
@section('title', 'Reservations')
@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-1">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Request a Facility</h2>
            <form method="POST" action="{{ route('reservations.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Facility</label>
                    <select name="facility_id" required class="input">
                        @foreach ($facilities as $facility)
                            <option value="{{ $facility->id }}">{{ $facility->name }} ({{ $facility->capacity }} pax)</option>
                        @endforeach
                    </select>
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
                        <input type="time" name="end_time" required class="input">
                    </div>
                </div>
                <div>
                    <label class="label">Attendees</label>
                    <input type="number" name="attendees" min="1" class="input">
                </div>
                <div>
                    <label class="label">Purpose</label>
                    <textarea name="purpose" rows="2" class="input"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full justify-center">Submit Request</button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Facility</th>
                            <th class="px-4 py-3">When</th>
                            <th class="px-4 py-3">Requester</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $reservation->facility_name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $reservation->date->format('M d, Y') }}, {{ $reservation->start_time }}–{{ $reservation->end_time }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $reservation->requester_name }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'badge',
                                        'bg-amber-50 text-amber-700' => $reservation->status === 'pending',
                                        'bg-emerald-50 text-emerald-700' => $reservation->status === 'approved',
                                        'bg-red-50 text-red-700' => $reservation->status === 'rejected',
                                        'bg-slate-100 text-slate-600' => in_array($reservation->status, ['cancelled', 'completed']),
                                    ])>{{ $reservation->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @can('decideReservations')
                                        @if ($reservation->status === 'pending' && $reservation->requester_email !== auth()->user()->email)
                                            <form method="POST" action="{{ route('reservations.decide', $reservation) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="decision" value="approved">
                                                <button class="text-emerald-600 hover:underline">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('reservations.decide', $reservation) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="decision" value="rejected">
                                                <button class="ml-2 text-red-600 hover:underline">Reject</button>
                                            </form>
                                        @endif
                                    @endcan
                                    @if ($reservation->status === 'pending' && $reservation->requester_email === auth()->user()->email)
                                        <form method="POST" action="{{ route('reservations.cancel', $reservation) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-slate-500 hover:underline">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $reservations->links() }}</div>
        </div>
    </div>
@endsection
