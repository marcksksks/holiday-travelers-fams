<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function edit()
    {
        $forced = (bool) request()->user()->force_password_change;

        return view('auth.change-password', compact('forced'));
    }

    public function update(ChangePasswordRequest $request)
    {
        $request->user()->forceFill([
            'password' => Hash::make($request->string('password')),
            'force_password_change' => false,
        ])->save();

        return redirect()->route('dashboard')->with('status', 'Password updated.');
    }
}

