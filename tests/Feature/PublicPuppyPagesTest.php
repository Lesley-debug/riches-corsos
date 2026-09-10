<?php

namespace Tests\Feature;

use App\Models\Puppy;
use App\Models\PuppyDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPuppyPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_puppy_page_includes_public_generated_and_uploaded_documents(): void
    {
        $puppy = Puppy::factory()->create();

        PuppyDocument::create([
            'puppy_id' => $puppy->id,
            'document_type' => PuppyDocument::TYPE_INFO_SHEET,
            'title' => 'Information Sheet',
            'document_number' => 'RC-DOC-2026-000001',
            'status' => PuppyDocument::STATUS_GENERATED,
            'source' => PuppyDocument::SOURCE_GENERATED,
            'file_path' => 'puppy-documents/test/information-sheet.pdf',
            'visibility' => 'public',
        ]);

        PuppyDocument::create([
            'puppy_id' => $puppy->id,
            'document_type' => PuppyDocument::TYPE_VET_CERTIFICATE,
            'title' => 'Vet Certificate',
            'document_number' => 'RC-DOC-2026-000002',
            'status' => PuppyDocument::STATUS_UPLOADED,
            'source' => PuppyDocument::SOURCE_UPLOADED,
            'file_path' => 'puppy-documents/test/vet-certificate.pdf',
            'visibility' => 'public',
        ]);

        PuppyDocument::create([
            'puppy_id' => $puppy->id,
            'document_type' => PuppyDocument::TYPE_CARE_GUIDE,
            'title' => 'Private Care Guide',
            'document_number' => 'RC-DOC-2026-000003',
            'status' => PuppyDocument::STATUS_GENERATED,
            'source' => PuppyDocument::SOURCE_GENERATED,
            'file_path' => 'puppy-documents/test/private-care-guide.pdf',
            'visibility' => 'admin_only',
        ]);

        PuppyDocument::create([
            'puppy_id' => $puppy->id,
            'document_type' => PuppyDocument::TYPE_FEEDING_GUIDE,
            'title' => 'Draft Feeding Guide',
            'document_number' => 'RC-DOC-2026-000004',
            'status' => PuppyDocument::STATUS_DRAFT,
            'source' => PuppyDocument::SOURCE_GENERATED,
            'file_path' => 'puppy-documents/test/draft-feeding-guide.pdf',
            'visibility' => 'public',
        ]);

        $response = $this->get(route('puppies.show', $puppy->slug));

        $response->assertStatus(200);

        $documents = collect($response->inertiaProps('puppy.documents'));

        $this->assertCount(2, $documents);
        $this->assertEqualsCanonicalizing(
            ['Information Sheet', 'Vet Certificate'],
            $documents->pluck('title')->all()
        );
        $this->assertSame(
            'Puppy Information Sheet',
            $documents->firstWhere('title', 'Information Sheet')['type_label']
        );
        $this->assertSame(
            'Uploaded',
            $documents->firstWhere('title', 'Vet Certificate')['status_label']
        );
    }

    public function test_unpublished_puppy_page_is_not_publicly_viewable(): void
    {
        $puppy = Puppy::factory()->create(['visibility' => 'private']);

        $this->get(route('puppies.show', $puppy->slug))->assertNotFound();
    }
}
