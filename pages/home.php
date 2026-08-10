<?php
if (!empty($_SESSION['just_logged_in']) && !empty($_SESSION['user_name'])) {
    $welcomeName = htmlspecialchars($_SESSION['user_name']);
    unset($_SESSION['just_logged_in']);
    echo '
    <div class="welcome-banner" id="welcomeBanner">
        <div class="welcome-banner-inner">
            <span>👋 Welcome back, ' . $welcomeName . '! Visit your <a href="' . $basePath . '/account/dashboard.php">dashboard</a> to view your orders, wishlist, and account settings.</span>
            <button onclick="document.getElementById(\"welcomeBanner\").remove()" aria-label="Close">&times;</button>
        </div>
    </div>';
}
?>

<!-- ================== HERO ================== -->
<section class="hero-section">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="<?= $basePath; ?>/assets/videos/bg.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay-glow"></div>

    <div class="hero-content">
        <h1>Riches Corsos Puppies</h1>
        <h3 class="hero-subtitle">Power. Loyalty. Distinction.</h3>

        <div class="hero-buttons">
            <a href="<?= $basePath; ?>/shop/shop.php" class="btn-primary">View Available Puppies</a>
            <a href="<?= $basePath; ?>/pages/contact.php" class="btn-secondary">Reserve a Puppy</a>
        </div>
    </div>
</section>


<section class="stats-section">
    <div class="stats-intro">
        <h2>Our Legacy in Numbers</h2>
        <p>Every stat represents a family, a healthy puppy, and years of dedication.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-box reveal">
            <h3 class="counter" data-target="120">0</h3>
            <p>Happy Families</p>
        </div>

        <div class="stat-box reveal">
            <h3 class="counter" data-target="8">0</h3>
            <p>Years Experience</p>
        </div>

        <div class="stat-box reveal">
            <h3 class="counter" data-target="100">0</h3>
            <p>Health Guaranteed</p>
        </div>

        <div class="stat-box reveal">
            <h3 class="counter" data-target="5">0</h3>
            <p>Star Rating</p>
        </div>
    </div>
</section>



<!-- ================== AVAILABLE PUPPIES ================== -->
<section class="puppies-section">
    <h2>Our Available Puppies</h2>
    <p>Healthy, strong, and ready to become your new family member.</p>
    <div class="puppy-grid">
        <?php
        require __DIR__ . '/../admin/includes/db.php';
        $normalizeImagePath = static fn(?string $path): string => normalize_site_url($path);
        $puppyRows = [];
        try {
            $stmt = $conn->prepare("SELECT * FROM puppies WHERE LOWER(TRIM(status)) = 'available' ORDER BY created_at DESC LIMIT 3");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) $puppyRows[] = $row;
        } catch (Throwable $e) { error_log('Home puppies: ' . $e->getMessage()); }
        foreach ($puppyRows as $row):
        ?>
            <div class="puppy-card">
                <div class="puppy-img-wrapper">
                    <img src="<?= htmlspecialchars($normalizeImagePath($row['featured_image'] ?? '')); ?>" alt="<?= htmlspecialchars($row['name']); ?>" loading="lazy" decoding="async">
                    <span class="badge"><?= htmlspecialchars($row['category'] ?? 'Available'); ?></span>
                </div>
                <div class="puppy-info">
                    <h3><?= htmlspecialchars($row['name']); ?></h3>
                    <span>$<?= number_format((float)$row['price'], 2); ?></span>
                    <p><?= htmlspecialchars($row['breed']); ?> | <?= htmlspecialchars($row['age']); ?> | <?= htmlspecialchars($row['sex']); ?></p>
                    <a href="<?= $basePath; ?>/shop/product.php?id=<?= (int)$row['id']; ?>" class="view-btn">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ================== TRUST / WELLBEING ================== -->
<section class="trust-section">
    <h2>Why Our Puppies Are Special</h2>
    <p>We ensure every puppy is healthy, socialized, and ready to thrive in their new family.</p>
    <div class="trust-grid">
        <div class="trust-item">
            <img src="<?= $basePath; ?>/assets/images/happy_puppy.jpeg" alt="Playtime" loading="lazy" decoding="async">
            <h3>Happy & Socialized</h3>
            <p>Playtime, cuddles, and exploration are part of their daily routine.</p>
        </div>
        <div class="trust-item">
            <img src="<?= $basePath; ?>/assets/images/health.jpeg" alt="Care" loading="lazy" decoding="async">
            <h3>Health & Care</h3>
            <p>Regular vet check-ups, vaccinations, and proper nutrition for peak health.</p>
        </div>
        <div class="trust-item">
            <img src="<?= $basePath; ?>/assets/images/training.jpeg" alt="Training" loading="lazy" decoding="async">
            <h3>Early Training</h3>
            <p>Basic commands and social skills are introduced early for confident pups.</p>
        </div>
        <div class="trust-item">
            <img src="<?= $basePath; ?>/assets/images/family.jpeg" alt="Family" loading="lazy" decoding="async">
            <h3>Family Raised</h3>
            <p>Raised in a loving home environment for loyalty, confidence, and affection.</p>
        </div>
    </div>
</section>

<!-- ================== FROM OUR BLOG ================== -->
<section class="blog-section">
    <h2>From Our Blog</h2>
    <p>Expert tips, care guides, and stories from our Cane Corso family.</p>
    <div class="blog-grid">
        <?php
        $blogRows = [];
        try {
            $blogStmt = $conn->prepare("SELECT p.*, c.name as category_name FROM posts p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'published' ORDER BY p.created_at DESC LIMIT 3");
            $blogStmt->execute();
            $blogResult = $blogStmt->get_result();
            while ($post = $blogResult->fetch_assoc()) $blogRows[] = $post;
        } catch (Throwable $e) { error_log('Home blog: ' . $e->getMessage()); }
        foreach ($blogRows as $post):
        ?>
            <div class="blog-card">
                <div class="blog-img-wrapper">
                    <img src="<?= htmlspecialchars($normalizeImagePath($post['featured_image'] ?? '')); ?>" alt="<?= htmlspecialchars($post['title']); ?>" loading="lazy" decoding="async">
                    <span class="blog-badge"><?= htmlspecialchars($post['category_name'] ?? 'Blog'); ?></span>
                </div>
                <div class="blog-info">
                    <h3><?= htmlspecialchars($post['title']); ?></h3>
                    <p><?= htmlspecialchars(substr(strip_tags($post['content']), 0, 120)); ?>...</p>
                    <a href="<?= $basePath; ?>/blog/single-puppy.php?slug=<?= htmlspecialchars($post['slug']); ?>" class="read-more-btn">Read More</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="<?= $basePath; ?>/blog/index.php" class="view-all-btn">View All Articles</a>
</section>


<!-- ================== HEALTH ================== -->
<section class="health-section">
    <div class="health-content">
        <h2>Health & Vaccination Guarantee</h2>
        <p>
            Fully vaccinated, dewormed, and provided with health certificates for smooth transitions to your home.
            Our commitment to your new family member doesn't end at the door. Every pup is backed by a comprehensive health guarantee against genetic defects, ensuring your transition is defined by joy, not vet bills. We provide a lifetime of breeder support to help you navigate every milestone with confidence.
        </p>
        <a href="<?= $basePath; ?>/pages/contact.php">Learn More</a>
    </div>
</section>

<!-- ================== TESTIMONIALS ================== -->
<section class="testimonials-section">
    <div class="testimonials-content">
        <div class="testimonials-header">
            <span class="testimonial-pill">Customer Favorites</span>
            <h2>Rated 5 Stars by Our Families</h2>
            <p class="section-intro">
                We are proud to maintain a consistent 5-star rating on Google.
                Our commitment to quality and integrity speaks through our families.
            </p>
        </div>

        <div class="testimonial-summary">
            <div class="google-rating">
                <img src="<?= $basePath; ?>/assets/images/rating.png" alt="Google 5 Star Rating" loading="lazy" decoding="async">
                <span>5.0 Rating on Google</span>
            </div>
            <p>Trusted by hundreds of families for healthy, socialized Cane Corso puppies and a supportive adoption experience.</p>
        </div>

        <div class="testimonials-grid">

            <div class="testimonial-card">
                <div class="review-header">
                    <strong>Sarah Thompson</strong>
                    <img src="<?= $basePath; ?>/assets/images/rating.png" alt="5 star review" loading="lazy" decoding="async">
                </div>
                <div class="testimonial-text">
                    <p>
                        “Absolutely the best breeder experience we have ever had. Our puppy arrived healthy, confident, and well-socialized. Riches Corsos exceeded our expectations.”
                    </p>
                    <button type="button" class="testimonial-read-more">Read More</button>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="review-header">
                    <strong>Michael Davis</strong>
                    <img src="<?= $basePath; ?>/assets/images/rating.png" alt="5 star review" loading="lazy" decoding="async">
                </div>
                <div class="testimonial-text">
                    <p>
                        “Professional, transparent, and incredibly knowledgeable. Our Cane Corso has an amazing temperament and structure.”
                    </p>
                    <button type="button" class="testimonial-read-more">Read More</button>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="review-header">
                    <strong>Jessica Martinez</strong>
                    <img src="<?= $basePath; ?>/assets/images/rating.png" alt="5 star review" loading="lazy" decoding="async">
                </div>
                <div class="testimonial-text">
                    <p>
                        “From start to finish the process was smooth. You can truly tell these puppies are raised with love.”
                    </p>
                    <button type="button" class="testimonial-read-more">Read More</button>
                </div>
            </div>

        </div>

        <div class="testimonial-actions">
            <a href="<?= $basePath; ?>/pages/testimonials.php" class="primary-btn">View All Testimonials</a>
            <a href="<?= $basePath; ?>/pages/contact.php" class="secondary-btn">Submit a Testimonial</a>
        </div>
    </div>
</section>


<!-- ================== CTA ================== -->
<section class="cta-section">
    <div class="cta-content">
        <h2>Ready to Meet Your Corso Puppy?</h2>
        <p>Find your new best friend among our premium puppies today.</p>
        <a href="<?= $basePath; ?>/shop/shop.php">View Available Puppies</a>
    </div>
</section>


<script src="<?= $basePath; ?>/assets/js/home.js" defer></script>