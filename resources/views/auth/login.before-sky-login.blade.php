@extends('layouts.guest')
@section('title', 'Sign in')
@section('content')
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input">
        </div>
        <div>
            <label class="label">Password</label>
            <input type="password" name="password" required class="input">
        </div>
        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-slate-600 hover:underline">Forgot password?</a>
        </div>
        <button type="submit" class="btn-primary w-full justify-center">Sign in</button>
    </form>
    <p class="mt-4 text-center text-sm text-slate-500">
    Contact your administrator if you need an account.
</p>
