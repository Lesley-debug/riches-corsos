<?php

namespace Database\Seeders;

use App\Models\ParentDog;
use App\Models\Puppy;
use App\Models\PuppyDocument;
use App\Models\PuppyImage;
use Illuminate\Database\Seeder;

class PuppySeeder extends Seeder
{
    public function run(): void
    {
        $sire = ParentDog::where('slug', 'goliath-of-riches')->first();
        $dam = ParentDog::where('slug', 'bella-luna-of-riches')->first();

        // ── 1. Rex ──
        $rex = Puppy::updateOrCreate(
            ['slug' => 'rex'],
            [
                'name' => 'Rex',
                'breed' => 'Cane Corso',
                'date_of_birth' => now()->subWeeks(9),
                'sex' => 'male',
                'price' => 3500.00,
                'status' => 'available',
                'description' => 'Rex is a confident, bold male with deep solid black coat and broad bone structure. He displays fantastic curiosity, calm nerve stability, and natural engagement with people.',
                'color' => 'Black',
                'markings' => 'Small white chest patch',
                'weight' => '22 lbs',
                'expected_adult_weight' => '130-140 lbs',
                'temperament' => ['Confident', 'Affectionate', 'Loyal', 'Calm'],
                'energy_level' => 'Moderate',
                'compatibility' => ['Children', 'Dogs', 'Active Families'],
                'training_progress' => ['Crate Foundations', 'Leash Intro', 'Potty Routine Started'],
                'vaccination_status' => 'Up to date (DHPP 1 & 2)',
                'vaccination_notes' => 'Completed initial core vaccinations and wellness exam.',
                'dewormed' => true,
                'vet_checked' => true,
                'vet_check_date' => now()->subWeeks(1),
                'microchipped' => true,
                'microchip_number' => '985141002948210',
                'health_guarantee' => true,
                'health_guarantee_notes' => '2-year genetic health guarantee included with signed adoption agreement.',
                'available_date' => now()->subDays(3),
                'deposit_required' => true,
                'deposit_amount' => 500.00,
                'featured' => true,
                'badges' => ['Champion Bloodline', 'Health Tested', 'Microchipped', 'AKC Registrable'],
                'visibility' => 'published',
                'seo_title' => 'Rex — Champion Bloodline Male Cane Corso Puppy',
                'meta_description' => 'Reserve Rex, a purebred black male Cane Corso puppy with champion lineage and health clearance.',
            ]
        );

        if ($rex->images()->count() === 0) {
            PuppyImage::create([
                'puppy_id' => $rex->id,
                'path' => 'puppies/01M1QCC8RT3NSACRHY73N9A75F.jpeg',
                'alt_text' => 'Rex - Front portrait',
                'sort_order' => 1,
                'is_featured' => true,
            ]);
            PuppyImage::create([
                'puppy_id' => $rex->id,
                'path' => 'puppies/01M1QCC8RVMCNBPNSZACG45FST.jpg',
                'alt_text' => 'Rex - Outdoor stance',
                'sort_order' => 2,
                'is_featured' => false,
            ]);
        }

        if ($sire && $dam) {
            $rex->parents()->syncWithoutDetaching([
                $sire->id => ['role' => 'sire'],
                $dam->id => ['role' => 'dam'],
            ]);
        }

        // ── 2. Ramzy ──
        $ramzy = Puppy::updateOrCreate(
            ['slug' => 'ramzy'],
            [
                'name' => 'Ramzy',
                'breed' => 'Cane Corso',
                'date_of_birth' => now()->subWeeks(9),
                'sex' => 'male',
                'price' => 3800.00,
                'status' => 'available',
                'description' => 'Ramzy is an extraordinary Formentino male with striking amber eyes and rich mask definition. Sweet-natured, very attentive, and eager to please.',
                'color' => 'Formentino',
                'markings' => 'Grey mask with fawn coat',
                'weight' => '20 lbs',
                'expected_adult_weight' => '120-130 lbs',
                'temperament' => ['Gentle', 'Alert', 'People-Oriented', 'Playful'],
                'energy_level' => 'Moderate',
                'compatibility' => ['Children', 'Dogs', 'Cats with training'],
                'training_progress' => ['Early Neurological Stimulation', 'Mandatory Eye Contact', 'Sit Started'],
                'vaccination_status' => 'Up to date (DHPP 1 & 2)',
                'vaccination_notes' => 'Vaccinated, microchipped, and wormed on 2/4/6/8 week protocol.',
                'dewormed' => true,
                'vet_checked' => true,
                'vet_check_date' => now()->subWeeks(1),
                'microchipped' => true,
                'microchip_number' => '985141002948211',
                'health_guarantee' => true,
                'health_guarantee_notes' => '2-year genetic health guarantee and veterinary clearance letter.',
                'available_date' => now()->subDays(3),
                'deposit_required' => true,
                'deposit_amount' => 500.00,
                'featured' => true,
                'badges' => ['Rare Color', 'Champion Bloodline', 'Vet Inspected', 'Health Guaranteed'],
                'visibility' => 'published',
                'seo_title' => 'Ramzy — Formentino Male Cane Corso Puppy',
                'meta_description' => 'Reserve Ramzy, a gorgeous Formentino male Cane Corso puppy raised in family home environment.',
            ]
        );

        if ($ramzy->images()->count() === 0) {
            PuppyImage::create([
                'puppy_id' => $ramzy->id,
                'path' => 'puppies/01M232SVJ95PWNCGT82FKEBC06.jpeg',
                'alt_text' => 'Ramzy - Portrait shot',
                'sort_order' => 1,
                'is_featured' => true,
            ]);
            PuppyImage::create([
                'puppy_id' => $ramzy->id,
                'path' => 'puppies/01M232SVJCFA2DW8P0SPCT2K97.jpeg',
                'alt_text' => 'Ramzy - Side profile',
                'sort_order' => 2,
                'is_featured' => false,
            ]);
        }

        if ($sire && $dam) {
            $ramzy->parents()->syncWithoutDetaching([
                $sire->id => ['role' => 'sire'],
                $dam->id => ['role' => 'dam'],
            ]);
        }
    }
}
