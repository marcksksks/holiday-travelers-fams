@extends('layouts.app')
@section('title', 'Staff Accounts')
@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-1">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Create Account</h2>
            <form method="POST" action="{{ route('users.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Full name</label>
                    <input type="text" name="full_name" required class="input">
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" required class="input">
                </div>
                <div>
                    <label class="label">Temporary password</label>
                    <input type="text" name="password" required minlength="8" class="input">
                </div>
                <div>
                    <label class="label">Role</label>
                    <select name="app_role" class="input">
                        @foreach (\App\Models\User::ROLES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary w-full justify-center">Create Account</button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($staff as $person)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $person->full_name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $person->email }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('users.set-role', $person) }}">
                                        @csrf
                                        <select name="app_role" onchange="this.form.submit()" class="input py-1 text-xs">
                                            @foreach (\App\Models\User::ROLES as $value => $label)
                                                <option value="{{ $value }}" @selected($person->app_role === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge {{ $person->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $person->is_active ? 'Active' : 'Deactivated' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('users.toggle-active', $person) }}">
                                        @csrf
                                        <button class="text-slate-600 hover:underline">{{ $person->is_active ? 'Deactivate' : 'Reactivate' }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $staff->links() }}</div>
        </div>
    </div>
@endsection
