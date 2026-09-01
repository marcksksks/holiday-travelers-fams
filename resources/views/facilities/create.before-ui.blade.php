@extends('layouts.app')
@section('title', 'Add Facility')
@section('content')
    <div class="card max-w-xl p-6">
        <form method="POST" action="{{ route('facilities.store') }}" class="space-y-4">
            @csrf
            @include('facilities._form', ['facility' => null])
            <button type="submit" class="btn-primary">Save Facility</button>
        </form>
    </div>
@endsection
