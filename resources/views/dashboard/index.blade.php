@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div
    data-dashboard-realtime-root
    data-dashboard-partial-url="{{ route('dashboard', ['partial' => 1]) }}"
    data-dashboard-refresh-interval="15000">

    @include('dashboard._workspace')

</div>


@include('dashboard._realtime-script')

@endsection