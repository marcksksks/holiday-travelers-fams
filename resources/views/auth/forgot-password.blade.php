@extends('layouts.guest')
@section('title', 'Reset password')
@section('content')
    <p class="mb-4 text-sm text-slate-600">Enter your email and we'll send you a password reset link.</p>
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" required autofocus class="input">
        </div>
        <button type="submit" class="btn-primary w-full justify-center">Send reset link</button>
    </form>
