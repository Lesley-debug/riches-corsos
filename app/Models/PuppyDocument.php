<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PuppyDocument extends Model
{
    // ── Status constants ──────────────────────────────────────────────────────
    const STATUS_DRAFT     = 'draft';
    const STATUS_TEMPLATE  = 'template';
    const STATUS_GENERATED = 'generated';
    const STATUS_UPLOADED  = 'uploaded';
    const STATUS_ARCHIVED  = 'archived';

    // ── Source constants ──────────────────────────────────────────────────────
    const SOURCE_GENERATED = 'generated';
    const SOURCE_UPLOADED  = 'uploaded';

    // ── Document type constants ───────────────────────────────────────────────
    const TYPE_INFO_SHEET          = 'info_sheet';
    const TYPE_PUPPY_CERTIFICATE   = 'puppy_certificate';
    const TYPE_OWNERSHIP_CERT      = 'ownership_certificate';
    const TYPE_HEALTH_GUARANTEE    = 'health_guarantee';
    const TYPE_PURCHASE_AGREEMENT  = 'purchase_agreement';
    const TYPE_CARE_GUIDE          = 'care_guide';
    const TYPE_FEEDING_GUIDE       = 'feeding_guide';
    const TYPE_PEDIGREE            = 'pedigree';
    const TYPE_VACCINATION_RECORD  = 'vaccination_record';
    const TYPE_VET_CERTIFICATE     = 'vet_certificate';
    const TYPE_GENETIC_TEST        = 'genetic_test';
    const TYPE_OTHER               = 'other';

    public static array $generatableTypes = [
        self::TYPE_INFO_SHEET         => 'Puppy Information Sheet',
        self::TYPE_PUPPY_CERTIFICATE  => 'Puppy Certificate',
        self::TYPE_OWNERSHIP_CERT     => 'Ownership Certificate',
        self::TYPE_HEALTH_GUARANTEE   => 'Health Guarantee',
        self::TYPE_PURCHASE_AGREEMENT => 'Purchase Agreement',
        self::TYPE_CARE_GUIDE         => 'Puppy Care Guide',
        self::TYPE_FEEDING_GUIDE      => 'Feeding Guide',
        self::TYPE_PEDIGREE           => 'Pedigree / Family Tree',
        self::TYPE_VACCINATION_RECORD => 'Vaccination Record',
        self::TYPE_VET_CERTIFICATE    => 'Veterinary Health Certificate',
        self::TYPE_GENETIC_TEST       => 'Genetic Test Record',
    ];

    // Types that are Riches Corsos company documents (show RC signature)
    public static array $companyIssuedTypes = [
        self::TYPE_INFO_SHEET,
        self::TYPE_PUPPY_CERTIFICATE,
        self::TYPE_OWNERSHIP_CERT,
        self::TYPE_HEALTH_GUARANTEE,
        self::TYPE_PURCHASE_AGREEMENT,
        self::TYPE_CARE_GUIDE,
        self::TYPE_FEEDING_GUIDE,
        self::TYPE_PEDIGREE,
    ];

    protected $fillable = [
        'puppy_id', 'document_type', 'title', 'document_number',
        'status', 'source', 'file_path', 'mime_type', 'description',
        'visibility', 'generated_at', 'issued_at', 'uploaded_at',
        'created_by', 'notes',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'issued_at'    => 'datetime',
        'uploaded_at'  => 'datetime',
    ];

    public function puppy(): BelongsTo
    {
        return $this->belongsTo(Puppy::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCompanyIssued(): bool
    {
        return in_array($this->document_type, self::$companyIssuedTypes);
    }

    public function isGenerated(): bool
    {
        return $this->source === self::SOURCE_GENERATED;
    }

    public function isUploaded(): bool
    {
        return $this->source === self::SOURCE_UPLOADED;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT     => 'Draft',
            self::STATUS_TEMPLATE  => 'Template',
            self::STATUS_GENERATED => 'Generated',
            self::STATUS_UPLOADED  => 'Uploaded',
            self::STATUS_ARCHIVED  => 'Archived',
            default                => ucfirst($this->status),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return self::$generatableTypes[$this->document_type] ?? ucwords(str_replace('_', ' ', $this->document_type));
    }
}
