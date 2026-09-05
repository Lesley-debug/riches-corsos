<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('company_name')->default('Riches Corsos')->after('hero_image');
            $table->string('tagline')->nullable()->after('company_name');
            $table->string('phone')->nullable()->after('tagline');
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
            $table->string('address')->nullable()->after('website');
            $table->string('whatsapp')->nullable()->after('address');
            $table->string('facebook')->nullable()->after('whatsapp');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('tiktok')->nullable()->after('instagram');
            $table->string('representative_name')->nullable()->after('tiktok');
            $table->string('representative_title')->nullable()->after('representative_name');
            $table->string('signature_image')->nullable()->after('representative_title');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'company_name', 'tagline', 'phone', 'email', 'website',
                'address', 'whatsapp', 'facebook', 'instagram', 'tiktok',
                'representative_name', 'representative_title', 'signature_image',
            ]);
        });
    }
};
