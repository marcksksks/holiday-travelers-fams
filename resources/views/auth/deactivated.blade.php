@extends('layouts.guest')
@section('title', 'Account deactivated')
@section('content')
    <div class="text-center">
        <p class="mb-4 text-sm text-slate-600">Your account has been deactivated. Please contact your system administrator.</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-secondary w-full justify-center">Log out</button>
        </form>
    </div>
