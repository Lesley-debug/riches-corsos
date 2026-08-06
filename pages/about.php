<?php require __DIR__ . '/../template/header.php'; ?>

<section class="page-hero" style="background-image: url('<?= $basePath; ?>/assets/images/happy_puppy.jpeg')">
  <div class="page-hero-inner">
    <span class="page-hero-eyebrow">Our Story</span>
    <h1>Who We Are</h1>
    <p>Dedicated breeders combining structure, temperament, and health-first standards to raise premium Cane Corso puppies for responsible families.</p>
    <div class="page-hero-actions">
      <a href="<?= $basePath; ?>/shop/shop.php" class="btn-hero-primary">View Available Puppies</a>
      <a href="<?= $basePath; ?>/pages/contact.php" class="btn-hero-ghost">Contact Us</a>
    </div>
    <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
      <a href="<?= $basePath; ?>/index.php">Home</a><span>/</span><span>About Us</span>
    </nav>
  </div>
</section>

<section class="about-story section-wrap reveal-up">
    <div class="story-grid">
        <div class="story-media">
            <img src="<?= $basePath; ?>/assets/images/about.jpeg" alt="Cane Corso puppies at Riches Corsos" loading="lazy" decoding="async">
        </div>

        <div class="story-content">
            <p class="kicker">Our Story</p>
            <h2>Built From Passion, Guided by Responsibility</h2>
            <p>
                Riches Corsos started with one clear vision: to raise Cane Corsos that are physically sound,
                mentally stable, and prepared for family life. We are not a volume kennel. We keep our program
                intentional so each puppy receives direct care, social exposure, and confident early development.
            </p>
            <p>
                From birth through placement, our puppies are raised with consistency and structure. We prioritize
                veterinary oversight, clean lineage decisions, and transparency with every family that works with us.
                Our mission is not only to place puppies, but to build lasting breeder-family relationships.
            </p>
        </div>
    </div>
</section>

<section class="brand-values section-wrap reveal-up">
    <div class="section-head">
        <h2>Our Mission, Vision & Achievements</h2>
    </div>

    <div class="value-grid">
        <article class="value-card">
            <h3>Our Mission</h3>
            <p>
                To breed healthy, well-adjusted Cane Corso puppies through ethical practices,
                proper socialization, and long-term client support.
            </p>
        </article>

        <article class="value-card">
            <h3>Our Vision</h3>
            <p>
                To be recognized as a trusted Cane Corso breeder where quality, integrity,
                and responsible ownership remain the highest standards.
            </p>
        </article>

        <article class="value-card">
            <h3>Our Achievements</h3>
            <ul>
                <li>120+ successful family placements</li>
                <li>Health-first and ethics-led breeding program</li>
                <li>Ongoing client guidance after placement</li>
                <li>Strong reputation for temperament and structure</li>
            </ul>
        </article>
    </div>
</section>

<section class="special-section section-wrap">
    <div class="section-head reveal-up">
        <h2>What Makes Us Special</h2>
    </div>

    <div class="special-grid">
        <article class="special-card reveal-up">
            <img src="<?= $basePath; ?>/assets/images/first-aid-kit-1.png" alt="Health assurance icon" loading="lazy" decoding="async">
            <h3>Health You Can Trust</h3>
            <p>
                Puppies leave with age-appropriate care records and health documentation,
                so you can move forward with confidence.
            </p>
        </article>

        <article class="special-card reveal-up">
            <img src="<?= $basePath; ?>/assets/images/potty-pad.png" alt="Early training icon" loading="lazy" decoding="async">
            <h3>Early Training Foundation</h3>
            <p>
                We start structure and routine early, helping each puppy transition smoothly
                into their new home.
            </p>
        </article>

        <article class="special-card reveal-up">
            <img src="<?= $basePath; ?>/assets/images/dogs.png" alt="Support icon" loading="lazy" decoding="async">
            <h3>Breeder Support</h3>
            <p>
                You get direct guidance from us on feeding, adjustment, and development,
                long after your puppy goes home.
            </p>
        </article>

        <article class="special-card reveal-up">
            <img src="<?= $basePath; ?>/assets/images/white-terrier.png" alt="Quality lineage icon" loading="lazy" decoding="async">
            <h3>Quality Lineage Focus</h3>
            <p>
                Our breeding choices prioritize temperament, structure, and long-term soundness,
                not shortcuts.
            </p>
        </article>
    </div>
</section>

<section class="breed-section section-wrap reveal-up">
    <div class="section-head">
        <h2>About the Cane Corso</h2>
    </div>

    <div class="breed-grid">
        <article class="breed-card">
            <h3>Overview</h3>
            <p>
                Cane Corsos are powerful, intelligent guardians known for loyalty and composure.
                They thrive with structure, consistent leadership, and family integration.
            </p>
        </article>

        <article class="breed-card">
            <h3>Temperament</h3>
            <p>
                Confident and stable when properly socialized, they are naturally protective and deeply bonded
                to their owners while remaining calm in daily life.
            </p>
        </article>

        <article class="breed-card">
            <h3>Appearance</h3>
            <p>
                Athletic frame, broad head, and commanding presence. Correct structure supports both working ability
                and long-term mobility.
            </p>
        </article>
    </div>
</section>

<section class="about-cta section-wrap reveal-up">
    <h2>Ready to Meet Your Cane Corso Companion?</h2>
    <p>Explore our available puppies or contact us to discuss the right match for your family.</p>
    <div class="hero-actions">
        <a href="<?= $basePath; ?>/shop/shop.php" class="btn">Browse Puppies</a>
        <a href="<?= $basePath; ?>/pages/contact.php" class="btn-secondary">Speak With Us</a>
    </div>
</section>

<script src="<?= $basePath; ?>/assets/js/about.js" defer></script>

<?php require __DIR__ . '/../template/footer.php'; ?>