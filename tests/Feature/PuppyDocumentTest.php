<?php

namespace Tests\Feature;

use App\Models\Puppy;
use App\Models\PuppyDocument;
use App\Models\PuppyImage;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\PuppyDocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PuppyDocumentTest extends TestCase
{
    use RefreshDatabase;

    private User  $admin;
    private User  $customer;
    private Puppy $puppy;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake notifications so PuppyObserver doesn't try to send real mails
        // (which would query User::all() and potentially fail during DB refresh)
        Notification::fake();

        Storage::fake('public');

        $this->admin    = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->puppy    = Puppy::factory()->create([
            'name'          => 'Apollo',
            'date_of_birth' => now()->subWeeks(10),
        ]);

        // Ensure a SiteSetting row exists
        SiteSetting::firstOrCreate([]);
    }

    // ── Document number generation ────────────────────────────────────────────

    public function test_document_number_is_unique_and_formatted(): void
    {
        $this->actingAs($this->admin);
        $service = app(PuppyDocumentService::class);

        // Generate two actual documents so each number is persisted before the next is created
        $doc1 = $service->generate($this->puppy, PuppyDocument::TYPE_INFO_SHEET);
        $doc2 = $service->generate($this->puppy, PuppyDocument::TYPE_INFO_SHEET);

        $this->assertStringStartsWith('RC-DOC-'.now()->format('Y').'-', $doc1->document_number);
        $this->assertNotSame($doc1->document_number, $doc2->document_number);
    }

    public function test_certificate_document_number_uses_cert_prefix(): void
    {
        $service = app(PuppyDocumentService::class);
        $num     = $service->generateDocumentNumber(PuppyDocument::TYPE_PUPPY_CERTIFICATE);

        $this->assertStringStartsWith('RC-CERT-', $num);
    }

    // ── Service: generate ─────────────────────────────────────────────────────

    public function test_service_generates_document_and_stores_file(): void
    {
        $this->actingAs($this->admin);

        $service = app(PuppyDocumentService::class);
        $doc     = $service->generate($this->puppy, PuppyDocument::TYPE_INFO_SHEET);

        $this->assertInstanceOf(PuppyDocument::class, $doc);
        $this->assertSame(PuppyDocument::STATUS_GENERATED, $doc->status);
        $this->assertSame(PuppyDocument::SOURCE_GENERATED, $doc->source);
        $this->assertNotEmpty($doc->document_number);
        $this->assertNotEmpty($doc->file_path);
        Storage::disk('public')->assertExists($doc->file_path);
    }

    public function test_service_generates_document_without_puppy_image(): void
    {
        $this->actingAs($this->admin);

        // Puppy has no images — should not throw
        $service = app(PuppyDocumentService::class);
        $doc     = $service->generate($this->puppy, PuppyDocument::TYPE_PUPPY_CERTIFICATE);

        $this->assertSame(PuppyDocument::STATUS_GENERATED, $doc->status);
    }

    public function test_service_generates_document_with_puppy_image(): void
    {
        $this->actingAs($this->admin);

        // Create a fake image in storage
        $fakeImage = UploadedFile::fake()->image('apollo.jpg', 400, 400);
        $path      = $fakeImage->store('puppies', 'public');

        PuppyImage::create([
            'puppy_id'    => $this->puppy->id,
            'path'        => $path,
            'sort_order'  => 0,
            'is_featured' => true,
        ]);

        $service = app(PuppyDocumentService::class);
        $doc     = $service->generate($this->puppy, PuppyDocument::TYPE_INFO_SHEET);

        $this->assertSame(PuppyDocument::STATUS_GENERATED, $doc->status);
        Storage::disk('public')->assertExists($doc->file_path);
    }

    public function test_service_generates_all_document_types(): void
    {
        $this->actingAs($this->admin);
        $service = app(PuppyDocumentService::class);

        foreach (array_keys(PuppyDocument::$generatableTypes) as $type) {
            $doc = $service->generate($this->puppy, $type);
            $this->assertSame(PuppyDocument::STATUS_GENERATED, $doc->status, "Failed for type: {$type}");
        }
    }

    // ── Service: regenerate ───────────────────────────────────────────────────

    public function test_service_regenerates_document(): void
    {
        $this->actingAs($this->admin);
        $service = app(PuppyDocumentService::class);

        $doc         = $service->generate($this->puppy, PuppyDocument::TYPE_CARE_GUIDE);
        $originalPath = $doc->file_path;

        $regenerated = $service->regenerate($this->puppy, $doc);

        $this->assertSame($originalPath, $regenerated->file_path);
        Storage::disk('public')->assertExists($regenerated->file_path);
    }

    // ── HTTP routes: authorization ────────────────────────────────────────────

    public function test_guest_cannot_access_document_routes(): void
    {
        $response = $this->postJson(
            route('admin.puppies.documents.generate', $this->puppy),
            ['document_type' => PuppyDocument::TYPE_INFO_SHEET]
        );

        $response->assertStatus(401);
    }

    public function test_customer_cannot_generate_documents(): void
    {
        $this->actingAs($this->customer);

        $response = $this->postJson(
            route('admin.puppies.documents.generate', $this->puppy),
            ['document_type' => PuppyDocument::TYPE_INFO_SHEET]
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_generate_document_via_route(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(
            route('admin.puppies.documents.generate', $this->puppy),
            ['document_type' => PuppyDocument::TYPE_INFO_SHEET]
        );

        $response->assertOk()->assertJsonStructure(['message', 'document' => ['id', 'document_number', 'status']]);
    }

    // ── HTTP routes: upload ───────────────────────────────────────────────────

    public function test_admin_can_upload_document(): void
    {
        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->create('vet-cert.pdf', 500, 'application/pdf');

        $response = $this->postJson(
            route('admin.puppies.documents.upload', $this->puppy),
            [
                'file'          => $file,
                'document_type' => PuppyDocument::TYPE_VET_CERTIFICATE,
                'title'         => 'Vet Health Certificate',
            ]
        );

        $response->assertOk();
        $this->assertDatabaseHas('puppy_documents', [
            'puppy_id' => $this->puppy->id,
            'source'   => PuppyDocument::SOURCE_UPLOADED,
            'status'   => PuppyDocument::STATUS_UPLOADED,
        ]);
    }

    public function test_upload_rejects_invalid_mime_type(): void
    {
        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->create('script.exe', 100, 'application/octet-stream');

        $response = $this->postJson(
            route('admin.puppies.documents.upload', $this->puppy),
            [
                'file'          => $file,
                'document_type' => PuppyDocument::TYPE_OTHER,
                'title'         => 'Bad File',
            ]
        );

        $response->assertStatus(422);
    }

    // ── HTTP routes: preview & download ──────────────────────────────────────

    public function test_admin_can_preview_document(): void
    {
        $this->actingAs($this->admin);
        $service = app(PuppyDocumentService::class);
        $doc     = $service->generate($this->puppy, PuppyDocument::TYPE_INFO_SHEET);

        $response = $this->get(route('admin.puppies.documents.preview', [$this->puppy, $doc]));

        $response->assertOk();
        $this->assertStringContainsString('pdf', strtolower($response->headers->get('Content-Type') ?? ''));
    }

    public function test_admin_can_download_document(): void
    {
        $this->actingAs($this->admin);
        $service = app(PuppyDocumentService::class);
        $doc     = $service->generate($this->puppy, PuppyDocument::TYPE_HEALTH_GUARANTEE);

        $response = $this->get(route('admin.puppies.documents.download', [$this->puppy, $doc]));

        $response->assertOk();
    }

    // ── HTTP routes: delete ───────────────────────────────────────────────────

    public function test_admin_can_delete_document(): void
    {
        $this->actingAs($this->admin);
        $service = app(PuppyDocumentService::class);
        $doc     = $service->generate($this->puppy, PuppyDocument::TYPE_FEEDING_GUIDE);

        $response = $this->deleteJson(
            route('admin.puppies.documents.destroy', [$this->puppy, $doc])
        );

        $response->assertOk();
        $this->assertDatabaseMissing('puppy_documents', ['id' => $doc->id]);
    }

    // ── Document belongs to correct puppy ─────────────────────────────────────

    public function test_cannot_access_document_belonging_to_different_puppy(): void
    {
        $this->actingAs($this->admin);

        $otherPuppy = Puppy::factory()->create(['date_of_birth' => now()->subWeeks(8)]);
        $service    = app(PuppyDocumentService::class);
        $doc        = $service->generate($otherPuppy, PuppyDocument::TYPE_INFO_SHEET);

        // Try to preview doc via wrong puppy
        $response = $this->get(route('admin.puppies.documents.preview', [$this->puppy, $doc]));

        $response->assertNotFound();
    }

    // ── Logo & signature helpers ──────────────────────────────────────────────

    public function test_logo_path_returns_existing_file(): void
    {
        $service = app(PuppyDocumentService::class);
        $path    = $service->logoPath();

        // Logo exists at public/images/logo.png
        $this->assertNotNull($path);
        $this->assertFileExists($path);
    }

    public function test_signature_path_returns_null_when_not_configured(): void
    {
        $service = app(PuppyDocumentService::class);
        $path    = $service->signaturePath();

        $this->assertNull($path);
    }

    public function test_image_data_uri_returns_null_for_missing_file(): void
    {
        $result = PuppyDocumentService::imageDataUri('/nonexistent/path/image.jpg');
        $this->assertNull($result);
    }

    public function test_image_data_uri_returns_base64_for_existing_file(): void
    {
        $logoPath = public_path('images/logo.png');

        if (!file_exists($logoPath)) {
            $this->markTestSkipped('Logo file not present.');
        }

        $result = PuppyDocumentService::imageDataUri($logoPath);
        $this->assertStringStartsWith('data:image/', $result);
        $this->assertStringContainsString('base64,', $result);
    }

    // ── Model helpers ─────────────────────────────────────────────────────────

    public function test_puppy_document_model_constants_are_correct(): void
    {
        $this->assertSame('generated', PuppyDocument::SOURCE_GENERATED);
        $this->assertSame('uploaded',  PuppyDocument::SOURCE_UPLOADED);
        $this->assertSame('generated', PuppyDocument::STATUS_GENERATED);
        $this->assertSame('uploaded',  PuppyDocument::STATUS_UPLOADED);
    }

    public function test_is_company_issued_returns_true_for_rc_documents(): void
    {
        $doc = new PuppyDocument(['document_type' => PuppyDocument::TYPE_HEALTH_GUARANTEE]);
        $this->assertTrue($doc->isCompanyIssued());
    }

    public function test_is_company_issued_returns_false_for_third_party_documents(): void
    {
        $doc = new PuppyDocument(['document_type' => PuppyDocument::TYPE_VET_CERTIFICATE]);
        $this->assertFalse($doc->isCompanyIssued());
    }

    public function test_type_label_attribute_returns_human_readable_label(): void
    {
        $doc = new PuppyDocument(['document_type' => PuppyDocument::TYPE_PUPPY_CERTIFICATE]);
        $this->assertSame('Puppy Certificate', $doc->type_label);
    }
}
