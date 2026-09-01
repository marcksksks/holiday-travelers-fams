<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegalRecordRequest;
use App\Http\Resources\LegalRecordResource;
use App\Models\LegalRecord;
use Illuminate\Http\Request;

class LegalRecordApiController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('manageLegal') || $request->user()->can('viewLegal'), 403);
        $records = LegalRecord::when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('updated_at')->paginate(20);

        return LegalRecordResource::collection($records);
    }

    public function show(Request $request, LegalRecord $legal)
    {
        abort_unless($request->user()->can('manageLegal') || $request->user()->can('viewLegal'), 403);
        return new LegalRecordResource($legal);
    }

    public function store(LegalRecordRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_uri'] = $request->file('file')->store('legal', 'documents');
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        $record = LegalRecord::create($data);

        return (new LegalRecordResource($record))->response()->setStatusCode(201);
    }

    public function update(LegalRecordRequest $request, LegalRecord $legal)
    {
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

        return new LegalRecordResource($legal);
    }
}
