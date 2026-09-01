<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveDocumentRequest;
use App\Http\Resources\ArchiveDocumentResource;
use App\Models\ArchiveDocument;
use App\Services\DocumentAccessService;
use Illuminate\Http\Request;

class ArchiveDocumentApiController extends Controller
{
    public function __construct(private DocumentAccessService $access) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->can('viewDocuments'), 403);
        $documents = ArchiveDocument::when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->orderByDesc('updated_at')->paginate(20);

        return ArchiveDocumentResource::collection($documents);
    }

    public function show(Request $request, ArchiveDocument $document)
    {
        abort_unless($request->user()->can('viewDocuments'), 403);
        return new ArchiveDocumentResource($document);
    }

    public function store(ArchiveDocumentRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('file')) {
            $data['file_uri'] = $request->file('file')->store('archive', 'documents');
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }
        $data['uploaded_by_email'] = $request->user()->email;

        $document = ArchiveDocument::create($data);

        return (new ArchiveDocumentResource($document))->response()->setStatusCode(201);
    }

    public function requestLink(Request $request, ArchiveDocument $document)
    {
        abort_unless($request->user()->can('viewDocuments'), 403);
        return response()->json(['signed_url' => $this->access->signedUrl($request->user(), $document)]);
    }
}
