@extends('layouts.app')
@section('title', 'Reports')
@section('content')
    <form method="GET" class="mb-6 flex flex-wrap items-end gap-3">
        <div>
            <label class="label">From</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="input">
        </div>
        <div>
            <label class="label">To</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="input">
        </div>
        <button type="submit" class="btn-secondary">Update</button>
    </form>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="card p-5">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Reservations by Status</h2>
            <ul class="space-y-1 text-sm">
                @forelse ($reservationsByStatus as $status => $count)
                    <li class="flex justify-between"><span>{{ str($status)->headline() }}</span><span class="font-medium">{{ $count }}</span></li>
                @empty
                    <li class="text-slate-400">No data for this period.</li>
                @endforelse
            </ul>
        </div>
        <div class="card p-5">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Visitors by Type</h2>
            <ul class="space-y-1 text-sm">
                @forelse ($visitorsByType as $type => $count)
                    <li class="flex justify-between"><span>{{ str($type)->headline() }}</span><span class="font-medium">{{ $count }}</span></li>
                @empty
                    <li class="text-slate-400">No data for this period.</li>
                @endforelse
            </ul>
        </div>
        <div class="card p-5">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Appointments by Status</h2>
            <ul class="space-y-1 text-sm">
                @forelse ($appointmentsByStatus as $status => $count)
                    <li class="flex justify-between"><span>{{ str($status)->headline() }}</span><span class="font-medium">{{ $count }}</span></li>
                @empty
                    <li class="text-slate-400">No data for this period.</li>
                @endforelse
            </ul>
        </div>
        <div class="card p-5">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Contracts by Status</h2>
            <ul class="space-y-1 text-sm">
                @forelse ($contractsByStatus as $status => $count)
                    <li class="flex justify-between"><span>{{ str($status)->headline() }}</span><span class="font-medium">{{ $count }}</span></li>
                @empty
                    <li class="text-slate-400">No data.</li>
                @endforelse
            </ul>
        </div>
        <div class="card p-5 md:col-span-2">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Facility Utilization (approved bookings)</h2>
            <ul class="space-y-1 text-sm">
                @forelse ($facilityUtilization as $row)
                    <li class="flex justify-between"><span>{{ $row->facility->name ?? '—' }}</span><span class="font-medium">{{ $row->bookings }} bookings</span></li>
                @empty
                    <li class="text-slate-400">No approved bookings for this period.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
