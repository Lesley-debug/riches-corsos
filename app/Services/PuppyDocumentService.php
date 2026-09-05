<?php

namespace App\Services;

use App\Models\Puppy;
use App\Models\PuppyDocument;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PuppyDocumentService
{
    // ── Document number generation ────────────────────────────────────────────

    public function generateDocumentNumber(string $type): string
    {
        $year   = now()->format('Y');
        $prefix = match ($type) {
            PuppyDocument::TYPE_PUPPY_CERTIFICATE,
            PuppyDocument::TYPE_OWNERSHIP_CERT    => 'RC-CERT',
            default                               => 'RC-DOC',
        };

        // Use DB max to avoid race conditions
        $last = PuppyDocument::where('document_number', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->value('document_number');

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last);
            $seq   = ((int) end($parts)) + 1;
        }

        return sprintf('%s-%s-%06d', $prefix, $year, $seq);
    }

    // ── Build view data shared by all templates ───────────────────────────────

    public function buildViewData(Puppy $puppy, PuppyDocument $doc): array
    {
        $puppy->loadMissing(['images', 'videos', 'documents', 'parents.images']);

        $sire = $puppy->parents->firstWhere('pivot.role', 'sire');
        $dam  = $puppy->parents->firstWhere('pivot.role', 'dam');

        $order = $puppy->orders()
            ->with('user')
            ->whereIn('status', ['confirmed', 'completed', 'new'])
            ->latest()
            ->first();

        return [
            'puppy'    => $puppy,
            'doc'      => $doc,
            'sire'     => $sire,
            'dam'      => $dam,
            'order'    => $order,
            'settings' => SiteSetting::current(),
            'logoPath' => $this->logoPath(),
            'puppyImagePath' => $this->puppyImagePath($puppy),
            'sireImagePath'  => $sire ? $this->parentImagePath($sire) : null,
            'damImagePath'   => $dam  ? $this->parentImagePath($dam)  : null,
            'signaturePath'  => $this->signaturePath(),
            'issuedAt'       => $doc->issued_at ?? now(),
        ];
    }

    // ── Generate and persist a PDF ────────────────────────────────────────────

    public function generate(Puppy $puppy, string $type, array $extra = []): PuppyDocument
    {
        $docNumber = $this->generateDocumentNumber($type);
        $title     = PuppyDocument::$generatableTypes[$type] ?? ucwords(str_replace('_', ' ', $type));

        $doc = PuppyDocument::create([
            'puppy_id'        => $puppy->id,
            'document_type'   => $type,
            'title'           => $title,
            'document_number' => $docNumber,
            'status'          => PuppyDocument::STATUS_GENERATED,
            'source'          => PuppyDocument::SOURCE_GENERATED,
            'visibility'      => 'admin_only',
            'generated_at'    => now(),
            'issued_at'       => now(),
            'created_by'      => Auth::id(),
            'notes'           => $extra['notes'] ?? null,
            'file_path'       => '', // filled after save
        ]);

        $viewData = array_merge($this->buildViewData($puppy, $doc), $extra);
        $pdf      = $this->makePdf($type, $viewData);

        $relativePath = $this->storePdf($pdf, $puppy->id, $type, $docNumber);

        $doc->update([
            'file_path' => $relativePath,
            'mime_type' => 'application/pdf',
        ]);

        return $doc;
    }

    // ── Stream PDF to browser (preview) ──────────────────────────────────────

    public function preview(Puppy $puppy, PuppyDocument $doc): mixed
    {
        $viewData = $this->buildViewData($puppy, $doc);
        $pdf      = $this->makePdf($doc->document_type, $viewData);

        return $pdf->stream($this->filename($puppy, $doc));
    }

    // ── Force-download PDF ────────────────────────────────────────────────────

    public function download(Puppy $puppy, PuppyDocument $doc): mixed
    {
        // If already stored, serve from disk
        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            return response()->download(
                Storage::disk('public')->path($doc->file_path),
                $this->filename($puppy, $doc)
            );
        }

        // Regenerate on the fly
        $viewData = $this->buildViewData($puppy, $doc);
        $pdf      = $this->makePdf($doc->document_type, $viewData);

        return $pdf->download($this->filename($puppy, $doc));
    }

    // ── Regenerate and overwrite stored file ──────────────────────────────────

    public function regenerate(Puppy $puppy, PuppyDocument $doc): PuppyDocument
    {
        $viewData = $this->buildViewData($puppy, $doc);
        $pdf      = $this->makePdf($doc->document_type, $viewData);

        $relativePath = $this->storePdf($pdf, $puppy->id, $doc->document_type, $doc->document_number);

        $doc->update([
            'file_path'    => $relativePath,
            'generated_at' => now(),
            'status'       => PuppyDocument::STATUS_GENERATED,
        ]);

        return $doc;
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    private function makePdf(string $type, array $data): \Barryvdh\DomPDF\PDF
    {
        $view = "pdf.puppies.{$type}";

        // Fallback to generic template if specific one doesn't exist
        if (!view()->exists($view)) {
            $view = 'pdf.puppies.generic';
        }

        return Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 150);
    }

    private function storePdf(\Barryvdh\DomPDF\PDF $pdf, int $puppyId, string $type, string $docNumber): string
    {
        $dir      = "puppy-documents/{$puppyId}/{$type}";
        $filename = Str::slug($docNumber).'.pdf';
        $path     = "{$dir}/{$filename}";

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    private function filename(Puppy $puppy, PuppyDocument $doc): string
    {
        $puppySlug = Str::slug($puppy->name);
        $typeSlug  = Str::slug(str_replace('_', '-', $doc->document_type));

        return "riches-corsos-{$puppySlug}-{$typeSlug}.pdf";
    }

    // ── Asset path helpers ────────────────────────────────────────────────────

    public function logoPath(): ?string
    {
        $path = public_path('images/logo.png');

        return file_exists($path) ? $path : null;
    }

    public function signaturePath(): ?string
    {
        $sig = SiteSetting::current()->signature_image;
        if (!$sig) {
            return null;
        }

        $path = Storage::disk('public')->path($sig);

        return file_exists($path) ? $path : null;
    }

    public function puppyImagePath(Puppy $puppy): ?string
    {
        $featured = $puppy->images->firstWhere('is_featured', true)
            ?? $puppy->images->first();

        if (!$featured) {
            return null;
        }

        $path = Storage::disk('public')->path($featured->path);

        return file_exists($path) ? $path : null;
    }

    public function parentImagePath(\App\Models\ParentDog $parent): ?string
    {
        $img = $parent->images->firstWhere('is_primary', true)
            ?? $parent->images->first();

        if (!$img) {
            return null;
        }

        $path = Storage::disk('public')->path($img->path);

        return file_exists($path) ? $path : null;
    }

    // ── Encode image as base64 data URI for embedding in PDF ─────────────────

    public static function imageDataUri(?string $absolutePath): ?string
    {
        if (!$absolutePath || !file_exists($absolutePath)) {
            return null;
        }

        $mime = mime_content_type($absolutePath) ?: 'image/jpeg';
        $data = base64_encode(file_get_contents($absolutePath));

        return "data:{$mime};base64,{$data}";
    }
}
