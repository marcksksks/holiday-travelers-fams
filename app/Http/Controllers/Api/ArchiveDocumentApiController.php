<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveDocumentRequest;
use App\Http\Resources\ArchiveDocumentResource;
use App\Models\ArchiveDocument;
use App\Services\AuditService;
use App\Services\DocumentAccessService;
use App\Services\DocumentFileStorageService;
use Illuminate\Http\Request;

class ArchiveDocumentApiController extends Controller
{
    public function __construct(
        private DocumentAccessService $access,
        private AuditService $audit,
        private DocumentFileStorageService $fileStorage
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
        $file = $request->file('file');

        unset($data['file']);

        $data['uploaded_by_email'] =
            $request->user()->email;

        $persist = function (
            array $payload
        ) use ($request): ArchiveDocument {
            $document = ArchiveDocument::create(
                $payload
            );

            $this->audit->log(
                $request->user(),
                'upload',
                'documents',
                "Document - {$document->title}",
                (string) $document->id
            );

            return $document;
        };

        if ($file) {
            $fileName =
                $file->getClientOriginalName();

            $document =
                $this->fileStorage->create(
                    $file,
                    'archive',
                    function (
                        string $newPath
                    ) use (
                        $persist,
                        $data,
                        $fileName
                    ): ArchiveDocument {
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
            $document = $persist(
                $data
            );
        }

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