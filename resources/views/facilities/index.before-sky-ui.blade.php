@extends('layouts.app')
@section('title', 'Facilities')
@section('content')
    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-slate-500">Meeting rooms and shared spaces available for booking.</p>
        @can('manageFacilities')
            <a href="{{ route('facilities.create') }}" class="btn-primary">+ Add Facility</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Location</th>
                    <th class="px-4 py-3">Capacity</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($facilities as $facility)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $facility->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ str($facility->facility_type)->headline() }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $facility->location }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $facility->capacity }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'badge',
                                'bg-emerald-50 text-emerald-700' => $facility->status === 'available',
                                'bg-amber-50 text-amber-700' => $facility->status === 'maintenance',
                                'bg-slate-100 text-slate-600' => in_array($facility->status, ['unavailable', 'archived']),
                            ])>{{ $facility->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('manageFacilities')
                                <a href="{{ route('facilities.edit', $facility) }}" class="text-slate-600 hover:underline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $facilities->links() }}</div>
@endsection
