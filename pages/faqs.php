<?php require __DIR__ . '/../template/header.php'; ?>

<section class="page-hero" style="background-image: url('<?= $basePath; ?>/assets/images/happy_puppy.jpeg')">
  <div class="page-hero-inner">
    <span class="page-hero-eyebrow">Frequently Asked Questions</span>
    <h1>Riches Corsos FAQs</h1>
    <p>Everything families ask before bringing home a Cane Corso puppy.</p>
    <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
      <a href="<?= $basePath; ?>/index.php">Home</a><span>/</span><span>FAQs</span>
    </nav>
  </div>
</section>

<section class="faq-top">
    <div class="trust-badges">
        <div class="badge"><span>✔</span>
            <p>Health-Focused Program</p>
        </div>
        <div class="badge"><span>✔</span>
            <p>Vaccination Guidance</p>
        </div>
        <div class="badge"><span>✔</span>
            <p>Lifetime Breeder Support</p>
        </div>
    </div>

    <div class="faq-search">
        <input type="text" id="faqSearch" placeholder="Search FAQs...">
    </div>
</section>

<section class="faq-section" id="faqList">
    <article class="faq-item">
        <button class="faq-question">1. What makes Riches Corsos different?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>We focus on quality over quantity, emphasizing health, temperament, and proper structure in every litter.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">2. When can puppies go home?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>Puppies are generally ready between 8 to 10 weeks, once they are stable, socialized, and prepared for transition.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">3. Do your puppies receive vaccines?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>Yes. Puppies receive age-appropriate care and we provide health/vaccine records so your veterinarian can continue the schedule.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">4. Do you provide health documentation?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>Yes. Each puppy goes home with clear records and health-related paperwork relevant to their age and care plan.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">5. Are puppies potty trained?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>We begin foundational routines early, but full house training depends on consistency in the new home.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">6. What is included in the adoption fee?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>The fee typically includes core early care, records, and breeder guidance. Exact details are discussed during reservation.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">7. Are Cane Corsos good with families?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>With proper socialization and leadership, Cane Corsos can be excellent family companions and loyal guardians.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">8. How much exercise do they need?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>Cane Corsos are active dogs and need regular physical activity plus structured mental engagement.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">9. Do you offer delivery or pickup options?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>Yes. Depending on location, pickup and transport options can be arranged. We prioritize safe, stress-aware transitions.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">10. How do I reserve a puppy?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>Contact us through our <a href="<?= $basePath; ?>/pages/contact.php">Contact Page</a>. We’ll guide you through availability, matching, and reservation steps.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">11. What support do you provide after adoption?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>We provide ongoing guidance on adjustment, feeding, development, and general owner questions.</p>
        </div>
    </article>

    <article class="faq-item">
        <button class="faq-question">12. Can first-time owners apply?<span class="icon">+</span></button>
        <div class="faq-answer">
            <p>Yes, if expectations are realistic and owners are committed to training, structure, and responsible care.</p>
        </div>
    </article>
</section>

<section class="columns">
    <div class="column">
        <h3>Get In Touch</h3>
        <p>If you need help choosing the right puppy, we’re here to guide you.</p>
        <ul>
            <li><strong>Email:</strong> <a href="mailto:info@richescorsos.com">info@richescorsos.com</a></li>
            <li><strong>Support:</strong> Breeder guidance available before and after placement.</li>
        </ul>
    </div>

    <div class="column">
        <h3>Available Puppies</h3>
        <ul>
            <li><strong>Current Listings:</strong> Updated as puppies become available.</li>
            <li><strong>Future Litters:</strong> Ask about planned pairings and upcoming availability.</li>
            <li><strong>Waitlist:</strong> Available for families seeking specific traits.</li>
        </ul>
        <p>See current availability on our <a href="<?= $basePath; ?>/shop/shop.php">Available Puppies</a> page.</p>
    </div>
</section>

<section class="faq-cta">
    <h2>Still Have Questions?</h2>
    <p>We’d be happy to walk you through everything before you reserve a puppy.</p>
    <a href="<?= $basePath; ?>/pages/contact.php" class="cta-btn">Contact Us Today</a>
</section>

<script src="<?= $basePath; ?>/assets/js/faqs.js" defer></script>

<?php require __DIR__ . '/../template/footer.php'; ?>