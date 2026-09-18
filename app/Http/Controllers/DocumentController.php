<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArchiveDocumentRequest;
use App\Models\ArchiveDocument;
use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\DocumentContainer;
use App\Models\LegalRecord;
use App\Models\Reservation;
use App\Models\RetentionPolicy;
use App\Models\Visitor;
use App\Services\DocumentAccessService;
use App\Services\DocumentAutomationService;
use App\Services\DocumentFileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    public function __construct(
        private DocumentAccessService $access,
        private DocumentAutomationService $automation,
        private DocumentFileStorageService $fileStorage
    ) {}

    public function index(Request $request)
    {
        $this->automation->ensureBaseContainers();

        $selectedContainer = null;

        if ($request->filled('container')) {
            $selectedContainer = DocumentContainer::find(
                $request->integer('container')
            );

            if ($selectedContainer) {
                $this->assertContainerVisible(
                    $request,
                    $selectedContainer
                );
            }
        }

        $query = $this
            ->visibleDocumentsQuery($request)
            ->with([
            'container',
            'linkedContract',
            'linkedLegalRecord',
            'linkedReservation',
            'linkedVisitor',
            'retentionRecord.policy',
        ]);


        if ($selectedContainer) {
            $containerIds = DocumentContainer::query()
                ->where('path', $selectedContainer->path)
                ->orWhere(
                    'path',
                    'like',
                    $selectedContainer->path.'/%'
                )
                ->pluck('id');

            $query->whereIn(
                'container_id',
                $containerIds
            );
        }

        if ($request->filled('category')) {
            $query->where(
                'category',
                $request->input('category')
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        if ($request->filled('search')) {
            $search = trim(
                (string) $request->input('search')
            );

            $query->where(function ($q) use ($search) {
                $q->where(
                    'title',
                    'ilike',
                    "%{$search}%"
                )
                ->orWhere(
                    'description',
                    'ilike',
                    "%{$search}%"
                )
                ->orWhere(
                    'file_name',
                    'ilike',
                    "%{$search}%"
                );
            });
        }

        $documents = $query
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        $rootContainers = DocumentContainer::query()
            ->whereNull('parent_id')
            ->with([
                'children.children.children.children'
            ])
            ->orderBy('name')
            ->get();

        $allContainers = DocumentContainer::query()
            ->orderBy('path')
            ->get();

        $breadcrumbs = $this->breadcrumbs(
            $selectedContainer
        );

        $counts = [
            'total' => $this
                ->visibleDocumentsQuery($request)
                ->count(),

            'active' => $this
                ->visibleDocumentsQuery($request)
                ->where(
                    'status',
                    'active'
                )
                ->count(),

            'needs_review' => $this
                ->visibleDocumentsQuery($request)
                ->where(
                    'status',
                    'needs_review'
                )
                ->count(),

            'archived' => $this
                ->visibleDocumentsQuery($request)
                ->where(
                    'status',
                    'archived'
                )
                ->count(),
        ];

        $relatedData = $this->relatedRecordData(
            $request
        );

        return view(
            'documents.index',
            array_merge(
                compact(
                    'documents',
                    'rootContainers',
                    'allContainers',
                    'selectedContainer',
                    'breadcrumbs',
                    'counts'
                ),
                $relatedData
            )
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

        $document->load([
            'container',
            'linkedContract',
            'linkedLegalRecord',
            'linkedReservation',
            'linkedVisitor',
            'retentionRecord.policy',
        ]);

        $retentionPolicies = RetentionPolicy::query()
            ->where('is_active', true)
            ->where(
                'record_category',
                $document->category
            )
            ->orderBy('name')
            ->get();

        $breadcrumbs = $this->breadcrumbs(
            $document->container
        );

        return view(
            'documents.show',
            compact(
                'document',
                'breadcrumbs',
                'retentionPolicies'
            )
        );
    }


    public function edit(
        Request $request,
        ArchiveDocument $document
    ) {
        abort_unless(
            $request->user()->can('manageDocuments'),
            403
        );

        abort_if(
            $document->is_system_generated,
            403,
            'System-generated records must be edited from their source module.'
        );

        $this->automation->ensureBaseContainers();

        $document->load([
            'container',
            'linkedContract',
            'linkedLegalRecord',
            'linkedReservation',
            'linkedVisitor',
            'retentionRecord.policy',
        ]);

        $retentionPolicies = RetentionPolicy::query()
            ->where('is_active', true)
            ->where(
                'record_category',
                $document->category
            )
            ->orderBy('name')
            ->get();

        $allContainers = DocumentContainer::query()
            ->orderBy('path')
            ->get();

        $relatedData = $this->relatedRecordData(
            $request
        );

        return view(
            'documents.edit',
            array_merge(
                compact(
                    'document',
                    'allContainers'
                ),
                $relatedData
            )
        );
    }

    public function store(
        ArchiveDocumentRequest $request
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageDocuments'),
            403
        );

        $data = $request->validated();

        $relatedType =
            $data['related_type'] ?? null;

        $relatedId =
            $data['related_id'] ?? null;

        $file = $request->file('file');

        unset(
            $data['related_type'],
            $data['related_id'],
            $data['file']
        );

        $data = $this->applyRelatedRecord(
            $data,
            $relatedType,
            $relatedId
        );

        $data['uploaded_by_email'] =
            $request->user()->email;

        $data['is_system_generated'] = false;

        $data['history'] = [[
            'version' => 1,
            'action' => 'upload',
            'by' => $request->user()->email,
            'at' => now()->toISOString(),
            'note' => 'Initial upload',
        ]];

        $persist = function (
            array $payload
        ) use ($request): ArchiveDocument {
            $document = ArchiveDocument::create(
                $payload
            );

            AuditLog::create([
                'actor_email' =>
                    $request->user()->email,

                'actor_role' =>
                    $request->user()->app_role,

                'action' => 'upload',

                'module' => 'documents',

                'record_label' =>
                    "Document - {$document->title}",

                'record_id' =>
                    $document->id,

                'created_at' =>
                    now(),
            ]);

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

        return redirect()
            ->route(
                'documents.show',
                $document
            )
            ->with(
                'status',
                'Document added to Document Management.'
            );
    }
    public function update(
        Request $request,
        ArchiveDocument $document
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageDocuments'),
            403
        );

        abort_if(
            $document->is_system_generated,
            403,
            'System-generated records must be edited from their source module.'
        );

        $data = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'category' => [
                'required',
                'in:administrative,contract,legal,permit,license,compliance,partnership,financial,operational,other',
            ],

            'department' => [
                'nullable',
                'string',
                'max:255',
            ],

            'owner_email' => [
                'nullable',
                'email',
            ],

            'confidentiality' => [
                'required',
                'in:general,restricted,confidential',
            ],

            'document_date' => [
                'nullable',
                'date',
            ],

            'expiration_date' => [
                'nullable',
                'date',
                'after_or_equal:document_date',
            ],

            'related_type' => [
                'nullable',
                'in:contract,legal,reservation,visitor',
            ],

            'related_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $relatedType =
            $data['related_type'] ?? null;

        $relatedId =
            isset($data['related_id'])
                ? (int) $data['related_id']
                : null;

        $this->validateRelatedSelection(
            $request,
            $relatedType,
            $relatedId
        );

        unset(
            $data['related_type'],
            $data['related_id']
        );

        $data = $this->applyRelatedRecord(
            $data,
            $relatedType,
            $relatedId
        );

        $history = $document->history ?? [];

        $history[] = [
            'version' =>
                $document->version ?? 1,

            'action' => 'metadata_update',
            'by' => $request->user()->email,
            'at' => now()->toISOString(),
            'note' =>
                'Document metadata updated.',
        ];

        $data['history'] = $history;

        $document->update($data);

        AuditLog::create([
            'actor_email' => $request->user()->email,
            'actor_role' => $request->user()->app_role,
            'action' => 'update',
            'module' => 'documents',
            'record_label' =>
                "Document - {$document->title}",
            'record_id' => $document->id,
            'details' => 'Metadata updated',
            'created_at' => now(),
        ]);

        return redirect()
            ->route(
                'documents.show',
                $document
            )
            ->with(
                'status',
                'Document metadata updated.'
            );
    }

    public function bulkAction(
        Request $request
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageDocuments'),
            403
        );

        $data = $request->validate([
            'action' => [
                'required',
                'in:archive,restore',
            ],

            'document_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'document_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:archive_documents,id',
            ],

        ]);

        $documentIds = collect(
            $data['document_ids']
        )
            ->map(
                fn ($id) => (int) $id
            )
            ->unique()
            ->values();

        $documents = $this
            ->visibleDocumentsQuery($request)
            ->whereIn(
                'id',
                $documentIds
            )
            ->with('container')
            ->get()
            ->keyBy('id');

        abort_unless(
            $documents->count()
                === $documentIds->count(),
            403,
            'One or more selected documents are not accessible.'
        );

        $updated = 0;
        $skipped = 0;

        \Illuminate\Support\Facades\DB::transaction(
            function () use (
                $request,
                $data,
                $documentIds,
                $documents,
                &$updated,
                &$skipped
            ) {
                foreach ($documentIds as $documentId) {
                    $document =
                        $documents->get($documentId);

                    if (! $document) {
                        $skipped++;
                        continue;
                    }

                    $history =
                        $document->history ?? [];


                    $auditAction = null;
                    $auditDetails = null;

                    switch ($data['action']) {
                        case 'archive':
                            if (
                                $document->status
                                === 'archived'
                            ) {
                                $skipped++;
                                continue 2;
                            }

                            $history[] = [
                                'version' =>
                                    $document->version
                                    ?? 1,

                                'action' =>
                                    'archive',

                                'by' =>
                                    $request
                                        ->user()
                                        ->email,

                                'at' =>
                                    now()
                                        ->toISOString(),

                                'note' =>
                                    'Document archived via bulk action.',
                            ];

                            $document->update([
                                'status' =>
                                    'archived',

                                'history' =>
                                    $history,
                            ]);

                            $auditAction =
                                'archive';

                            $auditDetails =
                                'Bulk archive action.';

                            break;


                        case 'restore':
                            if (
                                $document->status
                                !== 'archived'
                            ) {
                                $skipped++;
                                continue 2;
                            }

                            $history[] = [
                                'version' =>
                                    $document->version
                                    ?? 1,

                                'action' =>
                                    'restore',

                                'by' =>
                                    $request
                                        ->user()
                                        ->email,

                                'at' =>
                                    now()
                                        ->toISOString(),

                                'note' =>
                                    'Document restored via bulk action.',
                            ];

                            $document->update([
                                'status' =>
                                    'active',

                                'history' =>
                                    $history,
                            ]);

                            $auditAction =
                                'restore';

                            $auditDetails =
                                'Bulk restore action.';

                            break;
                    }

                    AuditLog::create([
                        'actor_email' =>
                            $request
                                ->user()
                                ->email,

                        'actor_role' =>
                            $request
                                ->user()
                                ->app_role,

                        'action' =>
                            $auditAction,

                        'module' =>
                            'documents',

                        'record_label' =>
                            "Document - {$document->title}",

                        'record_id' =>
                            $document->id,

                        'details' =>
                            $auditDetails,

                        'created_at' =>
                            now(),
                    ]);

                    $updated++;
                }
            }
        );

        $message =
            "{$updated} document"
            .($updated === 1 ? '' : 's')
            .' updated.';

        if ($skipped > 0) {
            $message .=
                " {$skipped} skipped because the selected action was not applicable.";
        }

        return back()->with(
            'status',
            $message
        );
    }

    public function move(
        Request $request,
        ArchiveDocument $document
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageDocuments'),
            403
        );

        abort_if(
            $document->is_system_generated,
            403,
            'System-generated records cannot be moved manually.'
        );

        $data = $request->validate([
            'container_id' => [
                'nullable',
                'integer',
                'exists:document_containers,id',
            ],
        ]);

        $oldContainer =
            $document->container?->name
            ?? 'Unfiled';

        $newContainer = null;

        if (! empty($data['container_id'])) {
            $newContainer = DocumentContainer::findOrFail(
                $data['container_id']
            );

            $this->assertContainerVisible(
                $request,
                $newContainer
            );
        }

        $newContainerName =
            $newContainer?->name
            ?? 'Unfiled';

        $history = $document->history ?? [];

        $history[] = [
            'version' =>
                $document->version ?? 1,

            'action' => 'move',
            'by' => $request->user()->email,
            'at' => now()->toISOString(),

            'note' =>
                "Moved from {$oldContainer} to {$newContainerName}.",
        ];

        $document->update([
            'container_id' =>
                $newContainer?->id,

            'history' => $history,
        ]);

        AuditLog::create([
            'actor_email' => $request->user()->email,
            'actor_role' => $request->user()->app_role,
            'action' => 'move',
            'module' => 'documents',
            'record_label' =>
                "Document - {$document->title}",
            'record_id' => $document->id,

            'details' =>
                "{$oldContainer} -> {$newContainerName}",

            'created_at' => now(),
        ]);

        return redirect()
            ->route(
                'documents.show',
                $document
            )
            ->with(
                'status',
                'Document moved successfully.'
            );
    }

    public function uploadVersion(
        Request $request,
        ArchiveDocument $document
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageDocuments'),
            403
        );

        abort_if(
            $document->is_system_generated,
            403,
            'System-generated files are managed by their source module.'
        );

        $data = $request->validate([
            'file' =>
                \App\Support\DocumentUploadPolicy::rules(
                    true
                ),

            'version_note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $file = $request->file('file');

        $oldPath =
            $document->file_uri;

        $newVersion =
            ($document->version ?? 1) + 1;

        $history =
            $document->history ?? [];

        $history[] = [
            'version' =>
                $newVersion,

            'action' =>
                'version_upload',

            'by' =>
                $request->user()->email,

            'at' =>
                now()->toISOString(),

            'note' =>
                $data['version_note']
                ?: 'New document version uploaded.',
        ];

        $fileName =
            $file->getClientOriginalName();

        $this->fileStorage->replace(
            $file,
            'archive',
            $oldPath,
            function (
                string $newPath
            ) use (
                $request,
                $document,
                $newVersion,
                $history,
                $fileName
            ): ArchiveDocument {
                $document->update([
                    'file_uri' =>
                        $newPath,

                    'file_name' =>
                        $fileName,

                    'version' =>
                        $newVersion,

                    'history' =>
                        $history,
                ]);

                AuditLog::create([
                    'actor_email' =>
                        $request->user()->email,

                    'actor_role' =>
                        $request->user()->app_role,

                    'action' =>
                        'version_upload',

                    'module' =>
                        'documents',

                    'record_label' =>
                        "Document - {$document->title}",

                    'record_id' =>
                        $document->id,

                    'details' =>
                        "Version {$newVersion} uploaded",

                    'created_at' =>
                        now(),
                ]);

                return $document;
            }
        );

        return redirect()
            ->route(
                'documents.show',
                $document
            )
            ->with(
                'status',
                "Version {$newVersion} uploaded successfully."
            );
    }
    public function archive(
        Request $request,
        ArchiveDocument $document
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageDocuments'),
            403
        );

        if ($document->status === 'archived') {
            return back()->with(
                'status',
                'Document is already archived.'
            );
        }

        $history =
            $document->history ?? [];

        $history[] = [
            'version' =>
                $document->version ?? 1,

            'action' => 'archive',
            'by' => $request->user()->email,
            'at' => now()->toISOString(),
            'note' => 'Document archived.',
        ];

        $document->update([
            'status' => 'archived',
            'history' => $history,
        ]);

        AuditLog::create([
            'actor_email' => $request->user()->email,
            'actor_role' => $request->user()->app_role,
            'action' => 'archive',
            'module' => 'documents',

            'record_label' =>
                "Document - {$document->title}",

            'record_id' => $document->id,
            'created_at' => now(),
        ]);

        return back()->with(
            'status',
            'Document archived. It can be restored later.'
        );
    }

    public function restore(
        Request $request,
        ArchiveDocument $document
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('manageDocuments'),
            403
        );

        if ($document->status !== 'archived') {
            return back()->with(
                'status',
                'Document is not archived.'
            );
        }

        $history =
            $document->history ?? [];

        $history[] = [
            'version' =>
                $document->version ?? 1,

            'action' => 'restore',
            'by' => $request->user()->email,
            'at' => now()->toISOString(),
            'note' => 'Document restored.',
        ];

        $document->update([
            'status' => 'active',
            'history' => $history,
        ]);

        AuditLog::create([
            'actor_email' => $request->user()->email,
            'actor_role' => $request->user()->app_role,
            'action' => 'restore',
            'module' => 'documents',

            'record_label' =>
                "Document - {$document->title}",

            'record_id' => $document->id,
            'created_at' => now(),
        ]);

        return back()->with(
            'status',
            'Document restored to the active library.'
        );
    }

    public function requestLink(
        Request $request,
        ArchiveDocument $document
    ) {
        abort_if(
            blank($document->file_uri),
            404
        );

        $url = $this->access->signedUrl(
            $request->user(),
            $document
        );

        return response()->json([
            'signed_url' => $url,
        ]);
    }

    public function download(
        Request $request,
        ArchiveDocument $document
    ) {
        abort_unless(
            $request->hasValidSignature(),
            403
        );

        abort_if(
            blank($document->file_uri),
            404
        );

        $this->access->assertCanAccess(
            $request->user(),
            $document
        );

        return Storage::disk('documents')->download(
            $document->file_uri,
            $document->file_name
        );
    }

    private function visibleDocumentsQuery(
        Request $request
    ) {
        $query = ArchiveDocument::query();

        $user = $request->user();

        /*
         * Visitor records must not leak into
         * Document Management for users who
         * cannot access Visitor Management.
         */
        if (! $user->can('viewVisitors')) {
            $query
                ->where(function ($q) {
                    $q->whereNull('source_module')
                        ->orWhere(
                            'source_module',
                            '<>',
                            'visitors'
                        );
                })
                ->whereNull(
                    'linked_visitor_id'
                );
        }

        /*
         * Contract records.
         */
        if (! $user->can('viewContracts')) {
            $query
                ->where(function ($q) {
                    $q->whereNull('source_module')
                        ->orWhere(
                            'source_module',
                            '<>',
                            'contracts'
                        );
                })
                ->whereNull(
                    'linked_contract_id'
                );
        }

        /*
         * Legal records.
         */
        if (! $user->can('viewLegal')) {
            $query
                ->where(function ($q) {
                    $q->whereNull('source_module')
                        ->orWhere(
                            'source_module',
                            '<>',
                            'legal'
                        );
                })
                ->whereNull(
                    'linked_legal_record_id'
                );
        }

        return $query;
    }


    private function assertContainerVisible(
        Request $request,
        DocumentContainer $container
    ): void {
        $path = trim(
            strtolower($container->path),
            '/'
        );

        $permission = match (true) {
            $path === 'visitors',
            str_starts_with(
                $path,
                'visitors/'
            ) => 'viewVisitors',

            $path === 'contracts',
            str_starts_with(
                $path,
                'contracts/'
            ) => 'viewContracts',

            $path === 'legal',
            str_starts_with(
                $path,
                'legal/'
            ) => 'viewLegal',

            default => null,
        };

        if (
            $permission &&
            ! $request->user()->can($permission)
        ) {
            abort(403);
        }
    }


    private function relatedRecordData(
        Request $request
    ): array {
        $contracts =
            $request->user()->can('viewContracts')
            ? Contract::query()
                ->select(
                    'id',
                    'contract_number',
                    'title'
                )
                ->orderByDesc('id')
                ->limit(200)
                ->get()
            : collect();

        $legalRecords =
            $request->user()->can('viewLegal')
            ? LegalRecord::query()
                ->select(
                    'id',
                    'reference_number',
                    'title'
                )
                ->orderByDesc('id')
                ->limit(200)
                ->get()
            : collect();

        $reservations = Reservation::query()
            ->select(
                'id',
                'facility_name',
                'requester_name',
                'date',
                'start_time'
            )
            ->orderByDesc('date')
            ->limit(200)
            ->get();

        $visitors =
            $request->user()->can('viewVisitors')
            ? Visitor::query()
                ->select(
                    'id',
                    'full_name',
                    'organization',
                    'check_in_at',
                    'created_at'
                )
                ->orderByDesc('id')
                ->limit(200)
                ->get()
            : collect();

        return compact(
            'contracts',
            'legalRecords',
            'reservations',
            'visitors'
        );
    }

    private function breadcrumbs(
        ?DocumentContainer $container
    ): Collection {
        $items = collect();

        while ($container) {
            $items->prepend($container);

            $container = $container->parent;
        }

        return $items;
    }

    private function validateRelatedSelection(
        Request $request,
        ?string $type,
        ?int $id
    ): void {
        if (! $type && $id) {
            throw ValidationException::withMessages([
                'related_type' =>
                    'Select a related module.'
            ]);
        }

        if ($type && ! $id) {
            throw ValidationException::withMessages([
                'related_id' =>
                    'Select a related record.'
            ]);
        }

        if (! $type || ! $id) {
            return;
        }

        $permission = match ($type) {
            'contract' => 'viewContracts',
            'legal' => 'viewLegal',
            'visitor' => 'viewVisitors',
            default => null,
        };

        if (
            $permission &&
            ! $request->user()->can($permission)
        ) {
            abort(403);
        }

        $exists = match ($type) {
            'contract' =>
                Contract::whereKey($id)->exists(),

            'legal' =>
                LegalRecord::whereKey($id)->exists(),

            'reservation' =>
                Reservation::whereKey($id)->exists(),

            'visitor' =>
                Visitor::whereKey($id)->exists(),
        };

        if (! $exists) {
            throw ValidationException::withMessages([
                'related_id' =>
                    'The selected related record no longer exists.'
            ]);
        }
    }

    private function applyRelatedRecord(
        array $data,
        ?string $type,
        mixed $id
    ): array {
        $data['linked_contract_id'] = null;
        $data['linked_legal_record_id'] = null;
        $data['linked_reservation_id'] = null;
        $data['linked_visitor_id'] = null;

        if (! $type || ! $id) {
            return $data;
        }

        $column = match ($type) {
            'contract' => 'linked_contract_id',
            'legal' => 'linked_legal_record_id',
            'reservation' => 'linked_reservation_id',
            'visitor' => 'linked_visitor_id',
        };

        $data[$column] = (int) $id;

        return $data;
    }
}