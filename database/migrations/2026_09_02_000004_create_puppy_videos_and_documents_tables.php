<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puppy_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puppy_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('video_url');
            $table->string('thumbnail_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('puppy_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puppy_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->string('title');
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->string('visibility')->default('admin_only'); // admin_only | buyer | public
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puppy_documents');
        Schema::dropIfExists('puppy_videos');
    }
};
