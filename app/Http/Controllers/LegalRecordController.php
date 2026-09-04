<?php

namespace App\Http\Controllers;

use App\Http\Requests\LegalRecordRequest;
use App\Models\AuditLog;
use App\Models\LegalRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegalRecordController extends Controller
{
    public function index(Request $request)
    {
        $records = LegalRecord::when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('updated_at')->paginate(15)->withQueryString();

        return view('legal.index', compact('records'));
    }

    public function store(LegalRecordRequest $request): RedirectResponse
    {
        abort_unless(
            $request->user()->can('manageLegal'),
            403
        );

        $data = $request->validated();
        if ($request->hasFile('file')) {
            $data['file_uri'] = $request->file('file')->store('legal', 'documents');
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        $record = LegalRecord::create($data);

        AuditLog::create([
            'actor_email' => $request->user()->email, 'actor_role' => $request->user()->app_role,
            'action' => 'create', 'module' => 'legal', 'record_label' => "Legal • {$record->title}",
            'record_id' => $record->id, 'created_at' => now(),
        ]);

        return redirect()->route('legal.index')->with('status', 'Legal record created.');
    }

    public function update(LegalRecordRequest $request, LegalRecord $legal): RedirectResponse
    {
        abort_unless(
            $request->user()->can('manageLegal'),
            403
        );

        $data = $request->validated();
        $oldPath = $legal->file_uri;
        if ($request->hasFile('file')) {
            $data['file_uri'] = $request->file('file')->store('legal', 'documents');
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }
        $legal->update($data);
        if ($request->hasFile('file') && $oldPath && $oldPath !== $legal->file_uri) {
            \Illuminate\Support\Facades\Storage::disk('documents')->delete($oldPath);
        }

        return redirect()->route('legal.index')->with('status', 'Legal record updated.');
    }

    public function review(
        Request $request,
        LegalRecord $legal
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('reviewLegal'),
            403
        );

        $data = $request->validate([
            'review_status' => [
                'required',
                'in:not_reviewed,in_review,reviewed,action_required',
            ],

            'legal_notes' => [
                'nullable',
                'string',
            ],
        ]);

        $legal->update([
            'review_status' =>
                $data['review_status'],

            'legal_notes' =>
                $data['legal_notes']
                ?? $legal->legal_notes,
        ]);

        AuditLog::create([
            'actor_email' =>
                $request->user()->email,

            'actor_role' =>
                $request->user()->app_role,

            'action' =>
                'update',

            'module' =>
                'legal',

            'record_label' =>
                "Legal • {$legal->title}",

            'record_id' =>
                $legal->id,

            'details' =>
                "Review: {$data['review_status']}",

            'created_at' =>
                now(),
        ]);

        return back()->with(
            'status',
            'Review recorded.'
        );
    }
}
