<?php

namespace App\Http\Controllers;

use App\Http\Requests\LegalRecordRequest;
use App\Models\AuditLog;
use App\Models\LegalRecord;
use App\Services\DocumentFileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegalRecordController extends Controller
{
    public function __construct(
        private DocumentFileStorageService $fileStorage
    ) {}
    public function index(Request $request)
    {
        $records = LegalRecord::when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('updated_at')->paginate(15)->withQueryString();

        return view('legal.index', compact('records'));
    }

    public function edit(Request $request, LegalRecord $legal)
    {
        abort_unless(
            $request->user()->can('manageLegal'),
            403
        );

        return view('legal.edit', compact('legal'));
    }

    public function store(LegalRecordRequest $request): RedirectResponse
    {
        abort_unless(
            $request->user()->can('manageLegal'),
            403
        );

        $data = $request->validated();
        $file = $request->file('file');

        unset($data['file']);

        $persist = function (
            array $payload
        ) use ($request): LegalRecord {
            $record = LegalRecord::create(
                $payload
            );

            AuditLog::create([
                'actor_email' => $request->user()->email,
                'actor_role' => $request->user()->app_role,
                'action' => 'create',
                'module' => 'legal',
                'record_label' => "Legal • {$record->title}",
                'record_id' => $record->id,
                'created_at' => now(),
            ]);

            return $record;
        };

        if ($file) {
            $fileName =
                $file->getClientOriginalName();

            $record =
                $this->fileStorage->create(
                    $file,
                    'legal',
                    function (
                        string $newPath
                    ) use (
                        $persist,
                        $data,
                        $fileName
                    ): LegalRecord {
                        $payload = $data;

                        $payload['file_uri'] =
                            $newPath;

                        $payload['file_name'] =
                            $fileName;

                        return $persist(
                            $payload
                        );
                    }
                );
        } else {
            $record = $persist(
                $data
            );
        }

        return redirect()
            ->route('legal.index')
            ->with(
                'status',
                'Legal record created.'
            );
    }
    public function update(
        LegalRecordRequest $request,
        LegalRecord $legal
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageLegal'),
            403
        );

        $data = $request->validated();
        $file = $request->file('file');

        unset($data['file']);

        if ($file) {
            $fileName =
                $file->getClientOriginalName();

            $this->fileStorage->replace(
                $file,
                'legal',
                $legal->file_uri,
                function (
                    string $newPath
                ) use (
                    $legal,
                    $data,
                    $fileName
                ): LegalRecord {
                    $payload = $data;

                    $payload['file_uri'] =
                        $newPath;

                    $payload['file_name'] =
                        $fileName;

                    $legal->update(
                        $payload
                    );

                    return $legal;
                }
            );
        } else {
            $legal->update(
                $data
            );
        }

        return redirect()
            ->route('legal.index')
            ->with(
                'status',
                'Legal record updated.'
            );
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
