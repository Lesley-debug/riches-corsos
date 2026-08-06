<?php
require __DIR__ . '/../template/header.php';
require __DIR__ . '/../admin/includes/db.php';

$normalizeImagePath = static fn(?string $path): string => normalize_site_url($path);
?>

<div class="shop-page">
    <div class="shop-shell">
        <section class="page-hero" style="background-image: url('<?= $basePath; ?>/assets/images/happy_puppy.jpeg')">
            <div class="page-hero-inner">
                <span class="page-hero-eyebrow">Available Puppies</span>
                <h1>Find Your Perfect Cane Corso</h1>
                <p>Healthy, confident, and family-raised — browse our available puppies and reserve yours today.</p>
                <div class="page-hero-actions">
                    <a href="#available-puppies" class="btn-hero-primary">Browse Puppies</a>
                    <a href="<?= $basePath; ?>/pages/contact.php" class="btn-hero-ghost">Reserve a Puppy</a>
                </div>
                <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?= $basePath; ?>/index.php">Home</a><span>/</span><span>Shop</span>
                </nav>
            </div>
        </section>

        <?php
        $featuredStmt = $conn->prepare("\n            SELECT * FROM puppies\n            WHERE LOWER(TRIM(category)) = 'featured' AND LOWER(TRIM(status)) = 'available'\n            ORDER BY created_at DESC\n            LIMIT 1\n        ");
        $featuredStmt->execute();
        $featured = $featuredStmt->get_result()->fetch_assoc();
        ?>

        <section class="shop-highlight">
            <div class="highlight-grid">
                <article class="highlight-card">
                    <strong>Health Certified</strong>
                    <p>Every puppy is vaccinated, vet-checked, and ready for your home.</p>
                </article>
                <article class="highlight-card">
                    <strong>Family Raised</strong>
                    <p>Raised in a loving environment for confident, social puppies.</p>
                </article>
                <article class="highlight-card">
                    <strong>Trusted Delivery</strong>
                    <p>Secure transport options available for safe arrival.</p>
                </article>
            </div>
        </section>

        <?php if ($featured): ?>
            <section class="featured-hero">
                <div>
                    <img src="<?= htmlspecialchars($normalizeImagePath($featured['featured_image'] ?? '')); ?>" alt="<?= htmlspecialchars($featured['name']); ?>">
                </div>
                <div class="featured-content">
                    <h2><?= htmlspecialchars($featured['title'] ?: $featured['name']); ?></h2>
                    <p><?= htmlspecialchars(substr($featured['description'] ?? '', 0, 220)); ?>...</p>
                    <div class="featured-price">$<?= number_format((float)$featured['price'], 2); ?></div>
                    <div class="action-row">
                        <a href="<?= $basePath; ?>/shop/product.php?id=<?= (int)$featured['id']; ?>" class="btn-primary">View Details</a>
                        <a href="<?= $basePath; ?>/add-to-cart.php?id=<?= (int)$featured['id']; ?>" class="btn-outline">Reserve Now</a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section id="available-puppies" class="section-block">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Available Puppies</h2>
                    <p class="section-subtitle">Browse current Cane Corso puppies ready for adoption, each raised for health and temperament.</p>
                </div>
            </div>
            <div class="puppy-grid">
                <?php
                $availableStmt = $conn->prepare("SELECT * FROM puppies WHERE LOWER(TRIM(status)) IN ('available', 'reserved') ORDER BY created_at DESC");
                $availableStmt->execute();
                $availableResult = $availableStmt->get_result();
                ?>

                <?php while ($row = $availableResult->fetch_assoc()): ?>
                    <?php $isReserved = strtolower(trim($row['status'])) === 'reserved'; ?>
                    <article class="puppy-card<?= $isReserved ? ' reserved' : ''; ?>">
                        <?php if ($isReserved): ?>
                            <span class="reserved-badge">Reserved</span>
                        <?php endif; ?>
                        <img src="<?= htmlspecialchars($normalizeImagePath($row['featured_image'] ?? '')); ?>" alt="<?= htmlspecialchars($row['name']); ?>">
                        <div class="puppy-card-content">
                            <h3><?= htmlspecialchars($row['name']); ?></h3>
                            <p class="puppy-meta">
                                <?= htmlspecialchars($row['breed']); ?> •
                                <?= htmlspecialchars($row['age']); ?> •
                                <?= htmlspecialchars($row['sex']); ?>
                            </p>
                            <p class="puppy-price">$<?= number_format((float)$row['price'], 2); ?></p>
                            
                                <a href="<?= $basePath; ?>/shop/product.php?id=<?= (int)$row['id']; ?>" class="btn-primary">View</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="section-block section-block--spaced">
            <div class="section-header">
                <h2 class="section-title">Recently Adopted</h2>
            </div>
            <div class="puppy-grid">
                <?php
                $soldStmt = $conn->prepare("\n                    SELECT * FROM puppies\n                    WHERE LOWER(TRIM(status)) = 'sold'\n                    ORDER BY created_at DESC\n                    LIMIT 6\n                ");
                $soldStmt->execute();
                $soldResult = $soldStmt->get_result();
                ?>

                <?php while ($row = $soldResult->fetch_assoc()): ?>
                    <article class="puppy-card sold">
                        <span class="sold-badge">Adopted</span>
                        <img src="<?= htmlspecialchars($normalizeImagePath($row['featured_image'] ?? '')); ?>" alt="<?= htmlspecialchars($row['name']); ?>">
                        <div class="puppy-card-content">
                            <h3><?= htmlspecialchars($row['name']); ?></h3>
                            <p class="puppy-meta"><?= htmlspecialchars($row['breed']); ?></p>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
    </div>

    <section class="trust-strip">
        <div class="shop-shell">
            <h2 class="section-title" style="color: #fff; margin-bottom: 22px;">Why Choose Us</h2>
            <div class="trust-grid">
                <div class="trust-item">
                    <h4>Health Guarantee</h4>
                    <p>All puppies come vaccinated and vet checked.</p>
                </div>
                <div class="trust-item">
                    <h4>Registration Papers</h4>
                    <p>Proper documentation and certification included.</p>
                </div>
                <div class="trust-item">
                    <h4>Safe Delivery</h4>
                    <p>We ensure safe nationwide transport.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>