<?php

namespace App\Http\Controllers;

use App\Http\Requests\RetentionPolicyRequest;
use App\Models\RetentionPolicy;
use Illuminate\Http\RedirectResponse;

class RetentionPolicyController extends Controller
{
    public function store(
        RetentionPolicyRequest $request
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageRetention'),
            403
        );

        RetentionPolicy::create(
            $request->validated()
        );

        return redirect()
            ->route('retention.index', ['tab' => 'policies'])
            ->with(
                'status',
                'Retention policy created.'
            );
    }

    public function update(
        RetentionPolicyRequest $request,
        RetentionPolicy $policy
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageRetention'),
            403
        );

        $policy->update(
            $request->validated()
        );

        return redirect()
            ->route('retention.index', ['tab' => 'policies'])
            ->with(
                'status',
                'Retention policy updated.'
            );
    }
}