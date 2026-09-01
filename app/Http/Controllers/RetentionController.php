<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecordRetentionRequest;
use App\Models\AuditLog;
use App\Models\RecordRetention;
use App\Models\RetentionPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RetentionController extends Controller
{
    public function index(Request $request)
    {
        $retentions = RecordRetention::with('policy')
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status', $request->string('status'))
            )
            ->when(
                $request->filled('compliance'),
                fn ($q) => $q->where('compliance_status', $request->string('compliance'))
            )
            ->when(
                $request->filled('record_type'),
                fn ($q) => $q->where('record_type', $request->string('record_type'))
            )
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        $policies = RetentionPolicy::where('is_active', true)
            ->orderBy('name')
            ->get();

        $allPolicies = RetentionPolicy::orderBy('name')->get();

        $stats = [
            'total' => RecordRetention::count(),

            'compliant' => RecordRetention::where(
                'compliance_status',
                'compliant'
            )->count(),

            'at_risk' => RecordRetention::where(
                'compliance_status',
                'at_risk'
            )->count(),

            'review_required' => RecordRetention::where(
                'status',
                'review_required'
            )->count(),
        ];

        return view(
            'retention.index',
            compact('retentions', 'policies', 'allPolicies', 'stats')
        );
    }

    public function store(RecordRetentionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['policy_id'])) {
            $policy = RetentionPolicy::findOrFail($data['policy_id']);
            $data['policy_name'] = $policy->name;
        } else {
            $data['policy_name'] = null;
        }

        $data['last_action_by'] = $request->user()->email;
        $data['last_action_at'] = now();

        $retention = RecordRetention::create($data);

        AuditLog::create([
            'actor_email' => $request->user()->email,
            'actor_role' => $request->user()->app_role,
            'action' => 'retention_action',
            'module' => 'retention',
            'record_label' => "Retention - {$retention->record_title}",
            'record_id' => $retention->id,
            'created_at' => now(),
        ]);

        return redirect()
            ->route('retention.index')
            ->with('status', 'Retention record created.');
    }

    public function update(
        RecordRetentionRequest $request,
        RecordRetention $retention
    ): RedirectResponse {
        $data = $request->validated();

        if (! empty($data['policy_id'])) {
            $policy = RetentionPolicy::findOrFail($data['policy_id']);
            $data['policy_name'] = $policy->name;
        } else {
            $data['policy_name'] = null;
        }

        $data['last_action_by'] = $request->user()->email;
        $data['last_action_at'] = now();

        $retention->update($data);

        AuditLog::create([
            'actor_email' => $request->user()->email,
            'actor_role' => $request->user()->app_role,
            'action' => 'retention_action',
            'module' => 'retention',
            'record_label' => "Retention - {$retention->record_title}",
            'record_id' => $retention->id,
            'details' => "Status: {$data['status']}",
            'created_at' => now(),
        ]);

        return redirect()
            ->route('retention.index')
            ->with('status', 'Retention record updated.');
    }
}