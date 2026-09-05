<?php

namespace App\Http\Controllers;

use App\Models\Puppy;
use App\Models\PuppyDocument;
use App\Services\PuppyDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PuppyDocumentController extends Controller
{
    public function __construct(private PuppyDocumentService $service) {}

    // ── Generate a new PDF document ───────────────────────────────────────────
    public function generate(Request $request, Puppy $puppy)
    {
        Gate::authorize('admin-only');

        $validated = $request->validate([
            'document_type' => ['required', 'string', 'in:'.implode(',', array_keys(PuppyDocument::$generatableTypes))],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $doc = $this->service->generate($puppy, $validated['document_type'], $validated);

        return response()->json([
            'message'  => 'Document generated successfully.',
            'document' => $this->docPayload($doc),
        ]);
    }

    // ── Preview (stream in browser) ───────────────────────────────────────────
    public function preview(Puppy $puppy, PuppyDocument $document)
    {
        Gate::authorize('admin-only');
        abort_unless($document->puppy_id === $puppy->id, 404);

        return $this->service->preview($puppy, $document);
    }

    // ── Download ──────────────────────────────────────────────────────────────
    public function download(Puppy $puppy, PuppyDocument $document)
    {
        Gate::authorize('admin-only');
        abort_unless($document->puppy_id === $puppy->id, 404);

        return $this->service->download($puppy, $document);
    }

    // ── Regenerate (overwrite stored file) ────────────────────────────────────
    public function regenerate(Puppy $puppy, PuppyDocument $document)
    {
        Gate::authorize('admin-only');
        abort_unless($document->puppy_id === $puppy->id, 404);
        abort_unless($document->isGenerated(), 422);

        $doc = $this->service->regenerate($puppy, $document);

        return response()->json([
            'message'  => 'Document regenerated.',
            'document' => $this->docPayload($doc),
        ]);
    }

    // ── Upload an external document ───────────────────────────────────────────
    public function upload(Request $request, Puppy $puppy)
    {
        Gate::authorize('admin-only');

        $validated = $request->validate([
            'file'          => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'document_type' => ['required', 'string'],
            'title'         => ['required', 'string', 'max:255'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $file     = $request->file('file');
        $dir      = "puppy-documents/{$puppy->id}/uploaded";
        $path     = $file->store($dir, 'public');
        $docNum   = $this->service->generateDocumentNumber('other');

        $doc = PuppyDocument::create([
            'puppy_id'        => $puppy->id,
            'document_type'   => $validated['document_type'],
            'title'           => $validated['title'],
            'document_number' => $docNum,
            'status'          => PuppyDocument::STATUS_UPLOADED,
            'source'          => PuppyDocument::SOURCE_UPLOADED,
            'file_path'       => $path,
            'mime_type'       => $file->getMimeType(),
            'visibility'      => 'admin_only',
            'uploaded_at'     => now(),
            'created_by'      => $request->user()?->id,
            'notes'           => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'message'  => 'Document uploaded successfully.',
            'document' => $this->docPayload($doc),
        ]);
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function destroy(Puppy $puppy, PuppyDocument $document)
    {
        Gate::authorize('admin-only');
        abort_unless($document->puppy_id === $puppy->id, 404);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['message' => 'Document deleted.']);
    }

    // ── List documents for a puppy ────────────────────────────────────────────
    public function index(Puppy $puppy)
    {
        Gate::authorize('admin-only');

        $docs = $puppy->documents()->latest()->get()->map(fn ($d) => $this->docPayload($d));

        return response()->json(['documents' => $docs]);
    }

    // ── Shared payload shape ──────────────────────────────────────────────────
    private function docPayload(PuppyDocument $doc): array
    {
        return [
            'id'              => $doc->id,
            'document_type'   => $doc->document_type,
            'type_label'      => $doc->type_label,
            'title'           => $doc->title,
            'document_number' => $doc->document_number,
            'status'          => $doc->status,
            'status_label'    => $doc->status_label,
            'source'          => $doc->source,
            'generated_at'    => $doc->generated_at?->format('d M Y'),
            'issued_at'       => $doc->issued_at?->format('d M Y'),
            'uploaded_at'     => $doc->uploaded_at?->format('d M Y'),
            'notes'           => $doc->notes,
            'preview_url'     => route('admin.puppies.documents.preview',  [$doc->puppy_id, $doc->id]),
            'download_url'    => route('admin.puppies.documents.download', [$doc->puppy_id, $doc->id]),
        ];
    }
}
