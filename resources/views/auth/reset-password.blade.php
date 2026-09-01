@extends('layouts.guest')
@section('title', 'Set a new password')
@section('content')
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus class="input">
        </div>
        <div>
            <label class="label">New password</label>
            <input type="password" name="password" required class="input">
        </div>
        <div>
            <label class="label">Confirm new password</label>
            <input type="password" name="password_confirmation" required class="input">
        </div>
        <button type="submit" class="btn-primary w-full justify-center">Reset password</button>
    </form>
