<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puppy_videos', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('video_url');
            // 'upload' = direct file, 'url' = YouTube/Vimeo link
            $table->string('source')->default('url')->after('file_path');
            $table->string('video_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('puppy_videos', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'source']);
            $table->string('video_url')->nullable(false)->change();
        });
    }
};
