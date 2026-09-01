<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArchiveDocumentRequest;
use App\Models\ArchiveDocument;
use App\Models\AuditLog;
use App\Services\DocumentAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(private DocumentAccessService $access) {}

    public function index(Request $request)
    {
        $documents = ArchiveDocument::when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->orderByDesc('updated_at')
            ->paginate(15)->withQueryString();

        return view('documents.index', compact('documents'));
    }

    public function store(ArchiveDocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('archive', 'documents');
            $data['file_uri'] = $path;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        $data['uploaded_by_email'] = $request->user()->email;
        $data['history'] = [[
            'version' => 1, 'action' => 'upload', 'by' => $request->user()->email,
            'at' => now()->toISOString(), 'note' => 'Initial upload',
        ]];

        $document = ArchiveDocument::create($data);

        AuditLog::create([
            'actor_email' => $request->user()->email, 'actor_role' => $request->user()->app_role,
            'action' => 'upload', 'module' => 'documents', 'record_label' => "Document • {$document->title}",
            'record_id' => $document->id, 'created_at' => now(),
        ]);

        return redirect()->route('documents.index')->with('status', 'Document archived.');
    }

    public function update(ArchiveDocumentRequest $request, ArchiveDocument $document): RedirectResponse
    {
        $data = $request->validated();
        $history = $document->history ?? [];

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('archive', 'documents');
            $data['file_uri'] = $path;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
            $data['version'] = ($document->version ?? 1) + 1;
            $history[] = [
                'version' => $data['version'], 'action' => 'update', 'by' => $request->user()->email,
                'at' => now()->toISOString(), 'note' => 'New version uploaded',
            ];
        }
        $data['history'] = $history;

        $document->update($data);
        if (isset($oldPath) && $oldPath && $oldPath !== $document->file_uri) {
            Storage::disk('documents')->delete($oldPath);
        }

        return redirect()->route('documents.index')->with('status', 'Document updated.');
    }

    /** Authorise + return a short-lived signed download link. */
    public function requestLink(Request $request, ArchiveDocument $document)
    {
        $url = $this->access->signedUrl($request->user(), $document);

        return response()->json(['signed_url' => $url]);
    }

    /** Signed-route target: streams the file only via a valid temporary signature. */
    public function download(Request $request, ArchiveDocument $document)
    {
        abort_unless($request->hasValidSignature(), 403);
        $this->access->assertCanAccess($request->user(), $document);
        abort_unless($document->file_uri && Storage::disk('documents')->exists($document->file_uri), 404);

        return Storage::disk('documents')->download($document->file_uri, $document->file_name);
    }
}
