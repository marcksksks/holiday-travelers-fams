<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveDocumentRequest;
use App\Http\Resources\ArchiveDocumentResource;
use App\Models\ArchiveDocument;
use App\Services\AuditService;
use App\Services\DocumentAccessService;
use Illuminate\Http\Request;

class ArchiveDocumentApiController extends Controller
{
    public function __construct(
        private DocumentAccessService $access,
        private AuditService $audit
    ) {}

    public function index(Request $request)
    {
        $query = $this->access->scopeAccessible(
            $request->user(),
            ArchiveDocument::query()
        );

        if ($request->filled('category')) {
            $query->where(
                'category',
                $request->string('category')
            );
        }

        $documents = $query
            ->orderByDesc('updated_at')
            ->paginate(20);

        return ArchiveDocumentResource::collection(
            $documents
        );
    }

    public function show(
        Request $request,
        ArchiveDocument $document
    ) {
        $this->access->assertCanAccess(
            $request->user(),
            $document
        );

        return new ArchiveDocumentResource(
            $document
        );
    }

    public function store(
        ArchiveDocumentRequest $request
    ) {
        abort_unless(
            $request->user()->can('manageDocuments'),
            403
        );

        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_uri'] = $request
                ->file('file')
                ->store(
                    'archive',
                    'documents'
                );

            $data['file_name'] = $request
                ->file('file')
                ->getClientOriginalName();
        }

        $data['uploaded_by_email'] =
            $request->user()->email;

        $document = ArchiveDocument::create(
            $data
        );

        $this->audit->log(
            $request->user(),
            'upload',
            'documents',
            "Document - {$document->title}",
            (string) $document->id
        );

        return (
            new ArchiveDocumentResource(
                $document
            )
        )
            ->response()
            ->setStatusCode(201);
    }

    public function requestLink(
        Request $request,
        ArchiveDocument $document
    ) {
        return response()->json([
            'signed_url' =>
                $this->access->signedUrl(
                    $request->user(),
                    $document
                ),
        ]);
    }
}