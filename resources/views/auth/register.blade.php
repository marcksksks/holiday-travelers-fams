@extends('layouts.guest')
@section('title', 'Create account')
@section('content')
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Full name</label>
            <input type="text" name="full_name" value="{{ old('full_name') }}" required autofocus class="input">
        </div>
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="input">
        </div>
        <div>
            <label class="label">Password</label>
            <input type="password" name="password" required class="input">
        </div>
        <div>
            <label class="label">Confirm password</label>
            <input type="password" name="password_confirmation" required class="input">
        </div>
        <button type="submit" class="btn-primary w-full justify-center">Create account</button>
    </form>
    <p class="mt-4 text-center text-sm text-slate-500">
        Already have an account? <a href="{{ route('login') }}" class="font-medium text-slate-900 hover:underline">Sign in</a>
    </p>
