<?php

namespace App\Http\Controllers;

use App\Http\Requests\RetentionPolicyRequest;
use App\Models\RetentionPolicy;
use Illuminate\Http\RedirectResponse;

class RetentionPolicyController extends Controller
{
    public function store(RetentionPolicyRequest $request): RedirectResponse
    {
        RetentionPolicy::create($request->validated());

        return redirect()->route('retention.index')->with('status', 'Retention policy created.');
    }

    public function update(RetentionPolicyRequest $request, RetentionPolicy $policy): RedirectResponse
    {
        $policy->update($request->validated());

        return redirect()->route('retention.index')->with('status', 'Retention policy updated.');
    }
}
