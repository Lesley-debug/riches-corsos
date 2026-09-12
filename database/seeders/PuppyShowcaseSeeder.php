<?php

namespace Database\Seeders;

use App\Models\ParentDog;
use App\Models\Puppy;
use Illuminate\Database\Seeder;

class PuppyShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── PARENTS ───────────────────────────────────────────────────────────

        $rex = ParentDog::firstOrCreate(
            ['slug' => 'rex-di-casa-riches'],
            [
                'name' => 'Rex Di Casa Riches',
                'parent_type' => 'sire',
                'breed' => 'Cane Corso',
                'color' => 'Black Brindle',
                'weight' => '128 lbs',
                'height' => '27.5 inches',
                'description' => 'Rex is our foundation sire — a large-boned, commanding Cane Corso with an impeccable pedigree from Italian champion lines. His temperament is calm, loyal, and deeply protective. Every litter he produces shows exceptional structural balance, wide skulls, and stable nerves.',
                'registration_organization' => 'ICCF / AKC',
                'registration_number' => 'ILP-RC-0042',
                'health_tests' => [
                    'Hip Evaluation' => 'OFA Good',
                    'Elbow Evaluation' => 'OFA Normal',
                    'Cardiac' => 'OFA Normal',
                    'Eye Exam' => 'CAER Normal',
                    'DNA Profile' => 'On File',
                ],
                'titles' => ['Italian Import', 'ICCF Champion', 'Working Title'],
            ]
        );

        $bella = ParentDog::firstOrCreate(
            ['slug' => 'bella-regina-corsos'],
            [
                'name' => 'Bella Regina Corsos',
                'parent_type' => 'dam',
                'breed' => 'Cane Corso',
                'color' => 'Gray',
                'weight' => '97 lbs',
                'height' => '24.5 inches',
                'description' => 'Bella is our premier dam — a refined gray Corso with exceptional feminine structure, a wide chest, and the most gentle disposition. She is wonderful with children and produces puppies with outstanding temperament and confirmation.',
                'registration_organization' => 'AKC / ICCF',
                'registration_number' => 'AKC-RC-0015',
                'health_tests' => [
                    'Hip Evaluation' => 'OFA Excellent',
                    'Elbow Evaluation' => 'OFA Normal',
                    'Cardiac' => 'OFA Normal',
                    'Eye Exam' => 'CAER Normal',
                ],
                'titles' => ['AKC Grand Champion', 'Best of Breed 2024'],
            ]
        );

        $titan = ParentDog::firstOrCreate(
            ['slug' => 'titan-forza-italiana'],
            [
                'name' => 'Titan Forza Italiana',
                'parent_type' => 'sire',
                'breed' => 'Cane Corso',
                'color' => 'Fawn',
                'weight' => '132 lbs',
                'height' => '28 inches',
                'description' => 'Titan is an imposing fawn Corso with a massive head, deep-set eyes, and a lineage tracing back to renowned Italian working lines. His pups consistently inherit his powerful build and confident, trainable temperament.',
                'registration_organization' => 'ICCF',
                'registration_number' => 'ICCF-TFI-0088',
                'health_tests' => [
                    'Hip Evaluation' => 'OFA Good',
                    'Elbow Evaluation' => 'OFA Normal',
                    'Cardiac' => 'OFA Normal',
                ],
                'titles' => ['Italian Import', 'ICCF Working Title'],
            ]
        );

        $luna = ParentDog::firstOrCreate(
            ['slug' => 'luna-nera-riches'],
            [
                'name' => 'Luna Nera Riches',
                'parent_type' => 'dam',
                'breed' => 'Cane Corso',
                'color' => 'Black',
                'weight' => '93 lbs',
                'height' => '24 inches',
                'description' => 'Luna is a stunning black Corso dam raised in our home since puppyhood. She has a sweet, affectionate personality while maintaining the natural guarding instinct of the breed. Her puppies are social, confident, and beautifully structured.',
                'registration_organization' => 'AKC',
                'registration_number' => 'AKC-LNR-0033',
                'health_tests' => [
                    'Hip Evaluation' => 'OFA Good',
                    'Elbow Evaluation' => 'OFA Normal',
                    'Eye Exam' => 'CAER Normal',
                ],
                'titles' => ['AKC Champion'],
            ]
        );

        // ── PUPPIES ───────────────────────────────────────────────────────────

        $puppies = [
            [
                'puppy' => [
                    'name' => 'Apollo',
                    'slug' => 'apollo',
                    'breed' => 'Cane Corso',
                    'date_of_birth' => now()->subWeeks(8)->toDateString(),
                    'sex' => 'male',
                    'color' => 'Black Brindle',
                    'markings' => 'Black mask, faint brindle striping across body',
                    'weight' => '14 lbs',
                    'expected_adult_weight' => '120–130 lbs',
                    'price' => 3500.00,
                    'deposit_required' => true,
                    'deposit_amount' => 500.00,
                    'status' => 'available',
                    'visibility' => 'published',
                    'featured' => true,
                    'description' => "Apollo is a bold, confident black brindle male with extraordinary bone structure and a wide, blocky head. He has been raised inside our home from day one — socialized with children, other dogs, and everyday household activity. His temperament is everything you want in a Corso: fearless yet gentle, loyal to his family, and calm under pressure.\n\nApollo has already shown natural guardian instincts alongside a playful, affectionate personality. He will thrive in an active family that provides structure and leadership. He is crate trained, introduced to basic commands, and ready to go to his forever home.",
                    'temperament' => ['Confident', 'Protective', 'Loyal', 'Playful', 'Intelligent'],
                    'energy_level' => 'moderate',
                    'compatibility' => ['Good with Children', 'Family Friendly'],
                    'training_progress' => ['Basic Handling', 'Crate Introduced', 'Socialization Started', 'Basic Commands Started'],
                    'vaccination_status' => 'first_vaccination',
                    'vaccination_notes' => 'First round of puppy vaccines completed. Next vaccination due at 12 weeks — schedule with your vet.',
                    'dewormed' => true,
                    'vet_checked' => true,
                    'vet_check_date' => now()->subDays(5)->toDateString(),
                    'microchipped' => true,
                    'health_guarantee' => true,
                    'health_guarantee_notes' => '2-year genetic health guarantee covering hereditary conditions. Full details provided in the purchase agreement.',
                    'badges' => ['available_now', 'champion_bloodline', 'health_tested', 'family_raised'],
                    'seo_title' => 'Apollo — Black Brindle Cane Corso Puppy | Riches Corsos',
                    'meta_description' => 'Meet Apollo, a bold black brindle male Cane Corso puppy from champion bloodlines. Health tested, family raised, available now at Riches Corsos.',
                ],
                'sire' => $rex,
                'dam' => $bella,
            ],
            [
                'puppy' => [
                    'name' => 'Athena',
                    'slug' => 'athena',
                    'breed' => 'Cane Corso',
                    'date_of_birth' => now()->subWeeks(9)->toDateString(),
                    'sex' => 'female',
                    'color' => 'Gray',
                    'markings' => 'Light gray with darker gray mask',
                    'weight' => '11 lbs',
                    'expected_adult_weight' => '88–98 lbs',
                    'price' => 3800.00,
                    'deposit_required' => true,
                    'deposit_amount' => 500.00,
                    'status' => 'available',
                    'visibility' => 'published',
                    'featured' => true,
                    'description' => "Athena is a breathtaking gray female — one of the most striking puppies we have ever produced. Her silver-gray coat, dark mask, and feminine yet powerful build make her absolutely unforgettable. She carries the best of both parents: Bella's elegance and Rex's substance.\n\nShe is sweet-natured, highly intelligent, and incredibly bonded to people. Athena has been exposed to children, other animals, and various environments from an early age. She is ideal for families looking for a female Corso with show and breeding potential.",
                    'temperament' => ['Affectionate', 'Intelligent', 'Calm', 'Loyal', 'Gentle'],
                    'energy_level' => 'moderate',
                    'compatibility' => ['Good with Children', 'Good with Other Dogs', 'Family Friendly', 'First-Time Owner Friendly'],
                    'training_progress' => ['Basic Handling', 'Crate Introduced', 'Potty Training Started', 'Socialization Started'],
                    'vaccination_status' => 'first_vaccination',
                    'vaccination_notes' => 'First puppy vaccines administered. Vet cleared her as perfectly healthy.',
                    'dewormed' => true,
                    'vet_checked' => true,
                    'vet_check_date' => now()->subDays(6)->toDateString(),
                    'microchipped' => true,
                    'health_guarantee' => true,
                    'health_guarantee_notes' => '2-year genetic health guarantee. Health and vaccination records provided at pickup.',
                    'badges' => ['available_now', 'champion_bloodline', 'health_tested', 'family_raised'],
                    'seo_title' => 'Athena — Gray Female Cane Corso Puppy | Riches Corsos',
                    'meta_description' => 'Meet Athena, a stunning gray female Cane Corso puppy from champion bloodlines. Sweet, intelligent, and family raised at Riches Corsos.',
                ],
                'sire' => $rex,
                'dam' => $bella,
            ],
            [
                'puppy' => [
                    'name' => 'Maximus',
                    'slug' => 'maximus',
                    'breed' => 'Cane Corso',
                    'date_of_birth' => now()->subWeeks(7)->toDateString(),
                    'sex' => 'male',
                    'color' => 'Fawn',
                    'markings' => 'Black mask, white patch on chest',
                    'weight' => '13 lbs',
                    'expected_adult_weight' => '125–135 lbs',
                    'price' => 3500.00,
                    'deposit_required' => true,
                    'deposit_amount' => 500.00,
                    'status' => 'available',
                    'visibility' => 'published',
                    'featured' => false,
                    'description' => "Maximus is a powerfully built fawn male with a striking black mask and a chest that already shows the depth and breadth that defines the best Cane Corsos. He is Titan's son and has clearly inherited his sire's massive frame and commanding presence.\n\nDespite his imposing looks, Maximus is a gentle giant with his family — playful, curious, and incredibly affectionate. He responds well to training and is already showing strong food motivation, which will make obedience work a joy.",
                    'temperament' => ['Confident', 'Playful', 'Loyal', 'Energetic', 'Curious'],
                    'energy_level' => 'high',
                    'compatibility' => ['Good with Children', 'Family Friendly'],
                    'training_progress' => ['Basic Handling', 'Crate Introduced', 'Basic Commands Started', 'Socialization Started'],
                    'vaccination_status' => 'first_vaccination',
                    'vaccination_notes' => 'First vaccination completed. Dewormed on schedule.',
                    'dewormed' => true,
                    'vet_checked' => true,
                    'vet_check_date' => now()->subDays(4)->toDateString(),
                    'microchipped' => true,
                    'health_guarantee' => true,
                    'health_guarantee_notes' => '2-year genetic health guarantee included.',
                    'badges' => ['available_now', 'champion_bloodline', 'health_tested', 'family_raised', 'new_arrival'],
                    'seo_title' => 'Maximus — Fawn Male Cane Corso Puppy | Riches Corsos',
                    'meta_description' => 'Meet Maximus, a powerfully built fawn Cane Corso male from champion Italian lines. Available now at Riches Corsos.',
                ],
                'sire' => $titan,
                'dam' => $luna,
            ],
            [
                'puppy' => [
                    'name' => 'Zara',
                    'slug' => 'zara',
                    'breed' => 'Cane Corso',
                    'date_of_birth' => now()->subWeeks(7)->toDateString(),
                    'sex' => 'female',
                    'color' => 'Black',
                    'markings' => 'Solid black with a tiny white star on chest',
                    'weight' => '10 lbs',
                    'expected_adult_weight' => '85–95 lbs',
                    'price' => 3800.00,
                    'deposit_required' => true,
                    'deposit_amount' => 500.00,
                    'status' => 'available',
                    'visibility' => 'published',
                    'featured' => false,
                    'description' => "Zara is a sleek, solid black female with a tiny white star on her chest — a natural beauty with a personality to match. She is Luna's daughter and has inherited her dam's warmth, sociability, and gentle nature.\n\nZara is highly observant and quick to learn. She has been around children from birth and is wonderfully tolerant and patient. She would thrive as both a family companion and a natural protector. Her jet black coat is low-maintenance and stunning.",
                    'temperament' => ['Affectionate', 'Calm', 'Loyal', 'Friendly', 'Gentle'],
                    'energy_level' => 'moderate',
                    'compatibility' => ['Good with Children', 'Good with Cats', 'Family Friendly', 'First-Time Owner Friendly'],
                    'training_progress' => ['Basic Handling', 'Crate Introduced', 'Potty Training Started', 'Leash Introduced', 'Socialization Started'],
                    'vaccination_status' => 'first_vaccination',
                    'vaccination_notes' => 'First puppy vaccination done. All clear from vet check.',
                    'dewormed' => true,
                    'vet_checked' => true,
                    'vet_check_date' => now()->subDays(3)->toDateString(),
                    'microchipped' => true,
                    'health_guarantee' => true,
                    'health_guarantee_notes' => '2-year genetic health guarantee. Full records included.',
                    'badges' => ['available_now', 'health_tested', 'family_raised', 'new_arrival'],
                    'seo_title' => 'Zara — Black Female Cane Corso Puppy | Riches Corsos',
                    'meta_description' => 'Meet Zara, a gorgeous solid black female Cane Corso puppy. Sweet, calm, and family raised at Riches Corsos in Dallas, TX.',
                ],
                'sire' => $titan,
                'dam' => $luna,
            ],
            [
                'puppy' => [
                    'name' => 'Bruno',
                    'slug' => 'bruno',
                    'breed' => 'Cane Corso',
                    'date_of_birth' => now()->subWeeks(10)->toDateString(),
                    'sex' => 'male',
                    'color' => 'Brindle',
                    'markings' => 'Classic brindle tiger-stripe pattern, black mask',
                    'weight' => '16 lbs',
                    'expected_adult_weight' => '115–125 lbs',
                    'price' => 3200.00,
                    'deposit_required' => true,
                    'deposit_amount' => 500.00,
                    'status' => 'available',
                    'visibility' => 'published',
                    'featured' => false,
                    'description' => "Bruno is a classic brindle male — the kind of Cane Corso that turns heads everywhere he goes. His tiger-stripe brindle pattern is rich and well-defined, and at 10 weeks he already carries himself with the natural authority the breed is known for.\n\nHe is outgoing, bold, and loves to play but also knows how to settle down and be calm indoors. Bruno has been raised with our family, has met many visitors, and handles new environments with confidence. He is ready for a family that will channel his energy and intelligence into training and bonding.",
                    'temperament' => ['Confident', 'Protective', 'Energetic', 'Playful', 'Intelligent'],
                    'energy_level' => 'high',
                    'compatibility' => ['Good with Children', 'Family Friendly'],
                    'training_progress' => ['Basic Handling', 'Crate Introduced', 'Potty Training Started', 'Basic Commands Started', 'Socialization Started', 'Leash Introduced'],
                    'vaccination_status' => 'second_vaccination',
                    'vaccination_notes' => 'Second round of vaccines completed. Fully on schedule and vet cleared.',
                    'dewormed' => true,
                    'vet_checked' => true,
                    'vet_check_date' => now()->subDays(7)->toDateString(),
                    'microchipped' => true,
                    'health_guarantee' => true,
                    'health_guarantee_notes' => '2-year genetic health guarantee. All vet and vaccination records provided.',
                    'badges' => ['available_now', 'champion_bloodline', 'health_tested', 'family_raised', 'ready_soon'],
                    'seo_title' => 'Bruno — Brindle Male Cane Corso Puppy | Riches Corsos',
                    'meta_description' => 'Meet Bruno, a classic brindle male Cane Corso puppy from champion lines. Bold, healthy, and ready for his forever home at Riches Corsos.',
                ],
                'sire' => $rex,
                'dam' => $luna,
            ],
        ];

        foreach ($puppies as $entry) {
            $puppy = Puppy::firstOrCreate(
                ['slug' => $entry['puppy']['slug']],
                $entry['puppy']
            );

            // Sync parents
            $sync = [];
            if (isset($entry['sire'])) {
                $sync[$entry['sire']->id] = ['role' => 'sire'];
            }
            if (isset($entry['dam'])) {
                $sync[$entry['dam']->id] = ['role' => 'dam'];
            }
            if ($sync) {
                $puppy->parents()->sync($sync);
            }

            $this->command->info("✓ {$puppy->name} seeded");
        }

        $this->command->info('');
        $this->command->info('5 puppies + 4 parents seeded successfully.');
        $this->command->info('Upload photos via the admin panel — each puppy and parent is ready and waiting.');
    }
}
