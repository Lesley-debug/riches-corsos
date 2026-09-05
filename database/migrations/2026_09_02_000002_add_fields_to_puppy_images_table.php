<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puppy_images', function (Blueprint $table) {
            $table->string('alt_text')->nullable()->after('path');
            $table->boolean('is_featured')->default(false)->after('alt_text');
        });
    }

    public function down(): void
    {
        Schema::table('puppy_images', function (Blueprint $table) {
            $table->dropColumn(['alt_text', 'is_featured']);
        });
    }
};
