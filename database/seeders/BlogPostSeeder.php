<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Bringing Your Cane Corso Puppy Home: The First 30 Days',
                'slug' => 'bringing-cane-corso-puppy-home-first-30-days',
                'category' => 'Puppy Care',
                'excerpt' => 'The first month with your Cane Corso sets the foundation for everything that follows. Here is exactly what to expect and how to start on the right foot.',
                'body' => '<p>Bringing a Cane Corso puppy home is one of the most exciting moments you will experience as a dog owner. It is also one of the most important. The decisions you make in the first 30 days will shape your Corso\'s temperament, habits, and relationship with your family for the next decade.</p>

<h2>Days 1–7: The Adjustment Period</h2>
<p>Your puppy is experiencing everything for the first time. A new home, new smells, new people, and the absence of their littermates. Expect some whimpering at night, and understand this is completely normal. Keep the first week calm and structured.</p>
<p>Set up a dedicated crate space — this will be your puppy\'s safe zone. Place it in a quiet area of the house, not isolated, but away from heavy foot traffic. Line it with a familiar-smelling item from the breeder if possible. The crate is not a punishment; it is a sanctuary.</p>
<p>Feed on a strict schedule: three times per day for puppies under 12 weeks. This regulates digestion and makes potty training infinitely easier. Take your puppy outside immediately after every meal, after every nap, and every two hours in between.</p>

<h2>Days 8–14: Establishing Routine</h2>
<p>Cane Corsos are creatures of routine. By the second week, begin reinforcing the daily rhythm: wake up, outside, breakfast, play, nap, outside, training session, outside, dinner, calm time, crate for the night. Consistency here pays enormous dividends.</p>
<p>Start with the most basic commands: sit, name recognition, and "come." Keep sessions to three to five minutes maximum. Corsos are intelligent but puppies have short attention spans. End every session on a success — never frustration.</p>

<h2>Days 15–30: Socialization Window</h2>
<p>The socialization window for Cane Corsos closes around 16 weeks. Use this period deliberately. Introduce your puppy to as many positive experiences as possible: different surfaces (grass, gravel, tile, wood), different sounds, different people of varying ages and appearances, and gentle exposure to other calm, vaccinated dogs.</p>
<p>The goal is not to overwhelm — it is to build confidence. Let your puppy investigate at their own pace. Never force an interaction. Positive associations formed now become the architecture of your adult Corso\'s temperament.</p>

<h2>What to Avoid in the First Month</h2>
<ul>
<li>Allowing behaviors you would not want from a 110-pound adult dog</li>
<li>Rough play that encourages biting or jumping</li>
<li>Leaving the puppy unsupervised in areas they can get into trouble</li>
<li>Overstimulation — rest is just as important as socialization</li>
</ul>

<p>The first 30 days are demanding, but they are also extraordinary. You are building a bond with a dog that will be loyal to you for life. Invest the time, stay consistent, and enjoy every single moment.</p>',
                'published_at' => now()->subDays(14),
            ],
            [
                'title' => 'Cane Corso Health Testing: What Every Buyer Must Know',
                'slug' => 'cane-corso-health-testing-what-buyers-must-know',
                'category' => 'Health & Genetics',
                'excerpt' => 'Health testing is the single most important difference between a reputable Cane Corso breeder and everyone else. Here is what the tests are, why they matter, and what to ask.',
                'body' => '<p>When you purchase a Cane Corso puppy, you are making a decade-long commitment. The health of your future dog depends significantly on the decisions your breeder made before any puppies were born. Health testing is not optional for responsible breeders — it is the baseline.</p>

<h2>The Essential Health Tests for Cane Corsos</h2>

<h3>Hip and Elbow Evaluations (OFA)</h3>
<p>The Orthopedic Foundation for Animals (OFA) evaluates hip and elbow radiographs submitted by veterinarians. Hips are rated Excellent, Good, Fair, Borderline, or Dysplastic. Responsible breeders breed only dogs rated Fair or better — ideally Good or Excellent. Elbow results are Normal, Grade I, Grade II, or Grade III. Only Normal dogs should be bred.</p>
<p>Hip dysplasia is one of the most common inherited conditions in large breeds. Breeding two OFA-cleared dogs dramatically reduces the risk of passing it on to puppies.</p>

<h3>Cardiac Evaluation</h3>
<p>Cane Corsos can carry inherited cardiac conditions including dilated cardiomyopathy (DCM). An OFA cardiac evaluation performed by a board-certified cardiologist — not your regular vet — assesses the heart for structural abnormalities. Dogs should be evaluated annually if used in breeding programs.</p>

<h3>Eye Examination (CAER)</h3>
<p>The Canine Eye Registration Foundation (CAER) exam evaluates the eye for inherited anomalies including entropion, ectropion, and progressive retinal atrophy (PRA). Cane Corsos are particularly prone to entropion — an inward rolling of the eyelid that causes chronic irritation. CAER certification confirms the parents were clear at the time of examination.</p>

<h3>DNA Health Panel</h3>
<p>Modern DNA testing through providers such as Embark or Animal Genetics can screen for dozens of inherited conditions simultaneously. A reputable breeder will have DNA profiles on file for both sire and dam. This also confirms parentage and provides a genetic fingerprint for each dog.</p>

<h2>What to Ask a Breeder</h2>
<p>Do not be shy. Ask to see the actual test certificates — not just verbal assurances. OFA results are publicly searchable at ofa.org. Enter the dog\'s registered name and you can verify their results independently.</p>
<p>Ask specifically:</p>
<ul>
<li>What are the OFA hip and elbow scores for both parents?</li>
<li>Has the cardiac evaluation been performed by a cardiologist?</li>
<li>Are DNA profiles available?</li>
<li>Are the health test results available for review before I commit to a puppy?</li>
</ul>

<p>A breeder who is defensive about health testing, who claims it is unnecessary, or who cannot produce documentation is a breeder to walk away from. Your puppy deserves better, and so do you.</p>',
                'published_at' => now()->subDays(28),
            ],
            [
                'title' => 'Training Your Cane Corso: The Foundation Commands Every Owner Needs',
                'slug' => 'training-cane-corso-foundation-commands',
                'category' => 'Training',
                'excerpt' => 'A well-trained Cane Corso is an extraordinary companion. A poorly trained one is a significant problem. These are the foundation commands that make all the difference.',
                'body' => '<p>The Cane Corso is one of the most trainable large breeds in the world — but only if you understand what motivates them and how they learn. They are not Golden Retrievers who will work endlessly for a treat and a smile. They are intelligent, independent thinkers who respect leadership and respond best to calm, consistent direction.</p>

<h2>Start From Day One</h2>
<p>There is no "too young" when it comes to foundation training. An eight-week-old Cane Corso can learn sit, their name, and the beginning of recall. The earlier you begin, the more natural these behaviors become. Waiting until your Corso is six months old — when they are already 60 pounds with an established personality — is working against yourself.</p>

<h2>The Non-Negotiable Foundation Commands</h2>

<h3>1. Sit</h3>
<p>The simplest command and the gateway to all others. Use a high-value treat held above the nose and moved backward. The moment the bottom hits the ground, mark with "Yes!" and deliver the treat. Practice 10 repetitions, three times a day. Sitting becomes the default calm behavior — teach your Corso to sit before meals, before going through doors, and before greetings.</p>

<h3>2. Down</h3>
<p>From a sit, lure the treat to the floor between your dog\'s front paws and slightly forward. As the elbows touch the ground, mark and reward. "Down" teaches impulse control and is the foundation for the "place" command — one of the most useful behaviors a large dog can have.</p>

<h3>3. Come (Recall)</h3>
<p>A reliable recall can save your dog\'s life. Practice in a low-distraction environment first: say your dog\'s name followed by "Come," back away a few steps, and reward enthusiastically when they reach you. Never punish a dog for coming to you, even if they took their time. Coming to you must always feel like the best decision they ever made.</p>

<h3>4. Place</h3>
<p>"Place" means go to a designated mat or bed and stay there until released. For a Cane Corso, this is invaluable. Guests arriving, children playing, meals being cooked — all of these scenarios become manageable when your Corso understands and respects "place." Build duration gradually: 30 seconds, then 1 minute, then 5 minutes, then across the room.</p>

<h3>5. Leave It</h3>
<p>Cane Corsos have powerful prey drives and strong opinions about what belongs to them. "Leave it" interrupts unwanted fixations before they escalate. Start with treats in your closed fist — reward when the dog stops nosing your hand and looks away. Progress to treats on the floor covered by your foot, then uncovered, then at a distance.</p>

<h2>The Training Mindset</h2>
<p>Corsos read energy precisely. Frustration, tension, and inconsistency undermine your authority with this breed faster than almost anything else. Training sessions should be short (5–10 minutes), high-energy, and always end with success. If a session is going badly, simplify — go back to something your dog knows and succeed, then end on that note.</p>
<p>Socialization and training together produce a Cane Corso that is both safe and a genuine pleasure to live with. The work you put in during the first year will pay back every single day for the rest of their life.</p>',
                'published_at' => now()->subDays(42),
            ],
            [
                'title' => 'What to Feed Your Cane Corso: Nutrition Guide for Every Life Stage',
                'slug' => 'cane-corso-nutrition-guide-every-life-stage',
                'category' => 'Nutrition',
                'excerpt' => 'Cane Corso nutrition is not one-size-fits-all. What your puppy eats in the first year directly impacts joint development, coat quality, and long-term health. Here is everything you need to know.',
                'body' => '<p>The Cane Corso is a large, muscular breed with specific nutritional requirements that differ meaningfully from smaller dogs. What they eat — and how much — directly impacts their skeletal development, muscle mass, coat quality, energy levels, and long-term health. Getting nutrition right is not complicated, but it does require understanding.</p>

<h2>Puppy Nutrition (8 Weeks to 18 Months)</h2>
<p>Large breed puppies like Cane Corsos should NOT be fed standard puppy food designed for small breeds. The reason is critical: most puppy formulas are very high in calcium and calories to support rapid growth in smaller dogs. In large breeds, this causes bones to grow too fast, leading to developmental orthopedic diseases including HOD (hypertrophic osteodystrophy) and OCD (osteochondrosis dissecans).</p>
<p>Look for food specifically labeled "Large Breed Puppy." These formulas are calibrated for controlled growth with appropriate calcium-to-phosphorus ratios. The goal is slow, steady growth — not the fastest possible.</p>

<h3>What to Look for on the Label</h3>
<ul>
<li>Named animal protein as the first ingredient (chicken, beef, lamb — not "meat meal" or "by-products" as the primary source)</li>
<li>Calcium content between 1.0–1.8% on a dry matter basis</li>
<li>Phosphorus content between 0.8–1.6% on a dry matter basis</li>
<li>AAFCO statement for "growth" or "all life stages including large breed puppies"</li>
</ul>

<h2>Adult Nutrition (18 Months to 7 Years)</h2>
<p>Once your Corso is fully grown — typically around 18–24 months when bone density is complete — transition to an adult large breed formula. The protein requirements remain high (25–30% minimum) to maintain muscle mass, but the caloric density and calcium levels can be more moderate.</p>
<p>Feeding amounts depend entirely on your individual dog\'s weight, activity level, and metabolism. The guidelines on the back of the bag are a starting point, not gospel. Monitor body condition: you should be able to feel the ribs without pressing hard, but not see them prominently. A visible waist when viewed from above indicates healthy weight.</p>

<h2>Raw Feeding and Home-Cooked Diets</h2>
<p>Many Cane Corso owners feed raw or home-cooked diets with excellent results. The key is nutritional completeness. A raw diet consisting only of muscle meat without appropriate organ meat, raw meaty bones, and supplementation will result in serious deficiencies over time. If you choose raw, work with a veterinary nutritionist to formulate a balanced diet or use a commercially prepared raw food that meets AAFCO standards.</p>

<h2>Senior Nutrition (7 Years and Beyond)</h2>
<p>Senior Corsos benefit from reduced caloric density to prevent weight gain (metabolism slows), joint supplements (glucosamine and chondroitin), and easily digestible protein sources. Look for senior large breed formulas or work with your veterinarian to adjust your current diet.</p>

<h2>Foods to Avoid</h2>
<ul>
<li>Grapes and raisins (cause kidney failure)</li>
<li>Onions and garlic (destroy red blood cells)</li>
<li>Xylitol (found in many sugar-free products — severely toxic to dogs)</li>
<li>Macadamia nuts</li>
<li>Cooked bones (can splinter and cause intestinal perforation)</li>
<li>Corn on the cob (choking and obstruction hazard)</li>
</ul>

<p>Feed your Cane Corso well and you invest in every aspect of their health, energy, and longevity. It is one of the most impactful decisions you make as an owner, and it is also one of the most straightforward to get right.</p>',
                'published_at' => now()->subDays(56),
            ],
        ];

        foreach ($posts as $data) {
            BlogPost::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
            $this->command->info("✓ '{$data['title']}' seeded");
        }

        $this->command->info('');
        $this->command->info('4 blog posts seeded. Upload cover images via the admin panel.');
    }
}
