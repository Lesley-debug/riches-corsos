<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puppies', function (Blueprint $table) {
            // status: add 'not_available' option — recreate as string to avoid enum ALTER issues
            $table->string('status')->default('available')->change();

            // Puppy Details
            $table->string('color')->nullable()->after('description');
            $table->string('markings')->nullable()->after('color');
            $table->string('weight')->nullable()->after('markings');
            $table->string('expected_adult_weight')->nullable()->after('weight');

            // Personality & Temperament (stored as JSON arrays)
            $table->json('temperament')->nullable()->after('expected_adult_weight');
            $table->string('energy_level')->nullable()->after('temperament');
            $table->json('compatibility')->nullable()->after('energy_level');
            $table->json('training_progress')->nullable()->after('compatibility');

            // Health
            $table->string('vaccination_status')->nullable()->after('training_progress');
            $table->text('vaccination_notes')->nullable()->after('vaccination_status');
            $table->boolean('dewormed')->default(false)->after('vaccination_notes');
            $table->boolean('vet_checked')->default(false)->after('dewormed');
            $table->date('vet_check_date')->nullable()->after('vet_checked');
            $table->boolean('microchipped')->default(false)->after('vet_check_date');
            $table->string('microchip_number')->nullable()->after('microchipped');
            $table->boolean('health_guarantee')->default(false)->after('microchip_number');
            $table->text('health_guarantee_notes')->nullable()->after('health_guarantee');

            // Availability
            $table->date('available_date')->nullable()->after('health_guarantee_notes');
            $table->boolean('deposit_required')->default(false)->after('available_date');
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('deposit_required');
            $table->boolean('featured')->default(false)->after('deposit_amount');

            // Badges & Visibility
            $table->json('badges')->nullable()->after('featured');
            $table->string('visibility')->default('published')->after('badges');

            // SEO (rename meta_title → seo_title for clarity, keep meta_description)
            $table->string('seo_title')->nullable()->after('visibility');
        });

        // Drop old meta_title column (data migrated to seo_title via raw SQL below)
        Schema::table('puppies', function (Blueprint $table) {
            $table->dropColumn('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('puppies', function (Blueprint $table) {
            $table->dropColumn([
                'color','markings','weight','expected_adult_weight',
                'temperament','energy_level','compatibility','training_progress',
                'vaccination_status','vaccination_notes','dewormed','vet_checked',
                'vet_check_date','microchipped','microchip_number',
                'health_guarantee','health_guarantee_notes',
                'available_date','deposit_required','deposit_amount',
                'featured','badges','visibility','seo_title',
            ]);
            $table->string('meta_title')->nullable();
        });
    }
};
