@extends('layouts.guest')
@section('title', 'Change your password')
@section('content')
    <p class="mb-4 text-sm text-slate-600">Your administrator requires you to set a new password before continuing.</p>
    <form method="POST" action="{{ route('password.change.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="label">Current password</label>
            <input type="password" name="current_password" required autofocus class="input">
        </div>
        <div>
            <label class="label">New password</label>
            <input type="password" name="password" required class="input">
        </div>
        <div>
            <label class="label">Confirm new password</label>
            <input type="password" name="password_confirmation" required class="input">
        </div>
        <button type="submit" class="btn-primary w-full justify-center">Update password</button>
    </form>
