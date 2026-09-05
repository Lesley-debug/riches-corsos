<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puppy_documents', function (Blueprint $table) {
            $table->string('document_number')->nullable()->unique()->after('title');
            // status: draft | template | generated | uploaded | archived
            $table->string('status')->default('draft')->after('document_number');
            // source: generated | uploaded
            $table->string('source')->default('generated')->after('status');
            $table->string('mime_type')->nullable()->after('file_path');
            $table->timestamp('generated_at')->nullable()->after('mime_type');
            $table->timestamp('issued_at')->nullable()->after('generated_at');
            $table->timestamp('uploaded_at')->nullable()->after('issued_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('uploaded_at');
            $table->text('notes')->nullable()->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('puppy_documents', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'document_number', 'status', 'source', 'mime_type',
                'generated_at', 'issued_at', 'uploaded_at', 'created_by', 'notes',
            ]);
        });
    }
};
