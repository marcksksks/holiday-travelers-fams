<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private AuditService $audit
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $aiProvider = config('services.ai_assist.provider', 'none');
        $aiModel = config('services.ai_assist.model');
        $aiConfigured = $aiProvider !== 'none'
            && filled(config('services.ai_assist.api_key'))
            && filled(config('services.ai_assist.endpoint'));

        return view('settings.index', [
            'user' => $user,
            'aiProvider' => $aiProvider,
            'aiModel' => $aiModel,
            'aiConfigured' => $aiConfigured,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'department' => [
                'nullable',
                'string',
                'max:255',
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        $user->update($data);

        $this->audit->log(
            $user,
            'update',
            'users',
            "User - {$user->full_name}",
            (string) $user->id,
            'Updated own profile settings'
        );

        return back()->with(
            'status',
            'Profile settings updated successfully.'
        );
    }
}