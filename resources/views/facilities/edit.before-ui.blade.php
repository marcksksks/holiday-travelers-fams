@extends('layouts.app')
@section('title', 'Edit Facility')
@section('content')
    <div class="card max-w-xl p-6">
        <form method="POST" action="{{ route('facilities.update', $facility) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('facilities._form', ['facility' => $facility])
            <div class="flex items-center justify-between">
                <button type="submit" class="btn-primary">Update Facility</button>
                <button type="submit" form="archive-form" class="text-sm text-red-600 hover:underline">Archive</button>
            </div>
        </form>
        <form id="archive-form" method="POST" action="{{ route('facilities.destroy', $facility) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
