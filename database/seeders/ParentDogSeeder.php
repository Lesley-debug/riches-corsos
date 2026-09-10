<?php

namespace Database\Seeders;

use App\Models\ParentDog;
use App\Models\ParentImage;
use Illuminate\Database\Seeder;

class ParentDogSeeder extends Seeder
{
    public function run(): void
    {
        $sire = ParentDog::firstOrCreate(
            ['slug' => 'goliath-of-riches'],
            [
                'name' => 'Goliath of Riches',
                'parent_type' => 'sire',
                'breed' => 'Cane Corso',
                'date_of_birth' => now()->subYears(3),
                'description' => 'Goliath is our premier champion stud with an exceptional muscular build, stable guardian temperament, and world-class Italian working bloodlines.',
                'color' => 'Black Brindle',
                'weight' => '135 lbs',
                'height' => '27.5 in',
                'registration_organization' => 'AKC / ICCF',
                'registration_number' => 'WS78901201',
                'health_tests' => [
                    'hips' => 'OFA Excellent',
                    'elbows' => 'OFA Normal',
                    'cardiac' => 'Echocardiogram Clear',
                    'd_locus' => 'Non-Carrier',
                ],
                'health_notes' => '100% clear genetic panel. Annual cardiac checks certified clear by board-certified veterinary cardiologist.',
                'titles' => ['AKC Champion', 'ICCF Grand Champion', 'CGC Tested'],
            ]
        );

        if ($sire->images()->count() === 0) {
            ParentImage::create([
                'parent_id' => $sire->id,
                'path' => 'parents/01M233DG0NSN1BJY7NP0FDM0Q5.jpeg',
                'alt_text' => 'Goliath - Cane Corso Sire',
                'sort_order' => 1,
                'is_primary' => true,
            ]);
        }

        $dam = ParentDog::firstOrCreate(
            ['slug' => 'bella-luna-of-riches'],
            [
                'name' => 'Bella Luna of Riches',
                'parent_type' => 'dam',
                'breed' => 'Cane Corso',
                'date_of_birth' => now()->subYears(2)->subMonths(6),
                'description' => 'Bella Luna is a nurturing, athletic female with an affectionate family disposition, alert protective instinct, and pristine movement.',
                'color' => 'Formentino',
                'weight' => '110 lbs',
                'height' => '25.5 in',
                'registration_organization' => 'AKC / ICCF',
                'registration_number' => 'WS89012304',
                'health_tests' => [
                    'hips' => 'OFA Good',
                    'elbows' => 'OFA Normal',
                    'eyes' => 'CAER Clear',
                    'cardiac' => 'OFA Clear',
                ],
                'health_notes' => 'Complete health clearances. Outstanding maternal instincts and calm indoor demeanor.',
                'titles' => ['AKC Major Pointed', 'Canine Good Citizen'],
            ]
        );

        if ($dam->images()->count() === 0) {
            ParentImage::create([
                'parent_id' => $dam->id,
                'path' => 'parents/01M233V38PS2C3KBFKXBP313DZ.webp',
                'alt_text' => 'Bella Luna - Cane Corso Dam',
                'sort_order' => 1,
                'is_primary' => true,
            ]);
        }
    }
}
