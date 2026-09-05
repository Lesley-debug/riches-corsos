<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('parent_type'); // sire | dam
            $table->string('breed')->default('Cane Corso');
            $table->date('date_of_birth')->nullable();
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->string('registration_organization')->nullable();
            $table->string('registration_number')->nullable();
            $table->json('health_tests')->nullable(); // {hip,elbow,heart,genetic} => result string
            $table->text('health_notes')->nullable();
            $table->json('titles')->nullable(); // array of title strings
            $table->timestamps();
        });

        Schema::create('parent_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('parent_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('video_url');
            $table->string('thumbnail_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Pivot: a puppy has one sire and one dam (both optional)
        Schema::create('puppy_parent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puppy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->string('role'); // sire | dam
            $table->unique(['puppy_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puppy_parent');
        Schema::dropIfExists('parent_videos');
        Schema::dropIfExists('parent_images');
        Schema::dropIfExists('parents');
    }
};
