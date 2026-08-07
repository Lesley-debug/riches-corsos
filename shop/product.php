<?php
require_once __DIR__ . '/../inc/paths.php';
require __DIR__ . '/../admin/includes/db.php';

$normalizeImagePath = static fn(?string $path): string => normalize_site_url($path);

if (!isset($_GET['id'])) {
    header('Location: ' . site_url('/shop/shop.php'));
    exit();
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare('SELECT * FROM puppies WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$puppy = $result->fetch_assoc();

if (!$puppy) {
    header('Location: ' . site_url('/shop/shop.php'));
    exit();
}

$thumbnails = json_decode($puppy['thumbnails'] ?? '[]', true);
if (!is_array($thumbnails)) {
    $thumbnails = [];
}

array_unshift($thumbnails, $puppy['featured_image']);
$thumbnails = array_values(array_unique(array_filter($thumbnails)));

require __DIR__ . '/../template/header.php';
?>

<div class="container">
    <div class="product-wrapper">
        <div class="product-gallery">
            <div class="main-image">
                <img id="mainImage" src="<?= htmlspecialchars($normalizeImagePath($puppy['featured_image'] ?? '')); ?>" alt="<?= htmlspecialchars($puppy['name']); ?>">
            </div>

            <div class="thumbnails">
                <?php foreach ($thumbnails as $index => $thumb): ?>
                    <img src="<?= htmlspecialchars($normalizeImagePath($thumb)); ?>" class="thumb <?= $index === 0 ? 'active' : ''; ?>" alt="Thumbnail">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="product-info">
            <?php if (strtolower((string)$puppy['status']) === 'available'): ?>
                <span class="availability available">Available</span>
            <?php else: ?>
                <span class="availability sold">Adopted</span>
            <?php endif; ?>

            <h1><?= htmlspecialchars(($puppy['title'] ?: $puppy['name']) ?? 'Puppy'); ?></h1>

            <div class="price-box">
                <span class="price">$<?= number_format((float)$puppy['price'], 2); ?></span>
                <span class="deposit-note">Secure with a deposit</span>
            </div>

            <div class="highlights">
                <div><strong>Breed:</strong> <?= htmlspecialchars($puppy['breed']); ?></div>
                <div><strong>Age:</strong> <?= htmlspecialchars($puppy['age']); ?></div>
                <div><strong>Sex:</strong> <?= htmlspecialchars($puppy['sex']); ?></div>
            </div>

            <ul class="details">
                <li><strong>Vaccination:</strong> <?= htmlspecialchars($puppy['vaccination_status']); ?></li>
                <li><strong>Potty Trained:</strong> <?= htmlspecialchars($puppy['potty_trained']); ?></li>
                <li><strong>Registration Papers:</strong> <?= htmlspecialchars($puppy['registration_papers']); ?></li>
                <li><strong>Health Certificate:</strong> <?= htmlspecialchars($puppy['health_certificate']); ?></li>
            </ul>

            <div class="trust-badges">
                <span>✔ Vet Checked</span>
                <span>✔ Health Guarantee</span>
                <span>✔ Safe Delivery</span>
            </div>

            <?php if (strtolower((string)$puppy['status']) === 'available'): ?>
                <div class="product-actions">
                    <a href="<?= $basePath; ?>/add-to-cart.php?id=<?= (int)$puppy['id']; ?>" class="btn-primary">Reserve This Puppy</a>
                    <button class="btn-wishlist" data-id="<?= (int)$puppy['id']; ?>" data-name="<?= htmlspecialchars($puppy['name']); ?>" data-price="<?= htmlspecialchars($puppy['price']); ?>" data-image="<?= htmlspecialchars($puppy['featured_image']); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 21s-7-4.35-10-9a6 6 0 0 1 10-6 6 6 0 0 1 10 6c-3 4.65-10 9-10 9z" />
                        </svg>
                        Add to Wishlist
                    </button>
                </div>
            <?php else: ?>
                <div class="sold-message">This puppy has already been adopted.</div>
            <?php endif; ?>

            <div class="puppy-share">
                <span>Share:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?= $basePath; ?>/assets/icons/facebook.png" alt="Facebook" class="social-icon">
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?= $basePath; ?>/assets/icons/twitter.png" alt="Twitter" class="social-icon">
                </a>
                <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">
                    <img src="<?= $basePath; ?>/assets/icons/instagram.png" alt="Instagram" class="social-icon">
                </a>
                <a href="https://www.tiktok.com/" target="_blank" rel="noopener noreferrer">
                    <img src="<?= $basePath; ?>/assets/icons/tik-tok.png" alt="TikTok" class="social-icon">
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty(trim((string)($puppy['parent_name'] ?? ''))) || !empty(trim((string)($puppy['parent_info'] ?? ''))) || !empty(trim((string)($puppy['parent_image'] ?? '')))) : ?>
        <section class="parent-section">
            <div class="parent-card">
                <h2>Meet the Parents</h2>
                <?php $hasParentImage = !empty($puppy['parent_image']); ?>
                <div class="parent-layout">
                    <?php if ($hasParentImage): ?>
                        <div class="parent-image-wrap">
                            <img src="<?= htmlspecialchars($normalizeImagePath($puppy['parent_image'])); ?>" alt="Parent">
                        </div>
                    <?php endif; ?>
                    <div class="parent-details<?= $hasParentImage ? '' : ' parent-details-full'; ?>">
                        <?php if (!empty(trim((string)($puppy['parent_name'] ?? '')))): ?>
                            <div class="parent-meta-row">
                                <div>
                                    <h3>Parent Name</h3>
                                    <p><?= htmlspecialchars($puppy['parent_name']); ?></p>
                                </div>
                                <?php if (!empty(trim((string)($puppy['parent_breed'] ?? '')))): ?>
                                    <div>
                                        <h3>Parent Breed</h3>
                                        <p><?= htmlspecialchars($puppy['parent_breed']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty(trim((string)($puppy['parent_info'] ?? '')))): ?>
                            <div class="parent-info-block">
                                <h3>About the Parents</h3>
                                <p><?= nl2br(htmlspecialchars($puppy['parent_info'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="product-tabs">
        <div class="tab-nav">
            <button class="tab-btn active" data-tab="description">Description</button>
            <button class="tab-btn" data-tab="health">Health Guarantee</button>
            <button class="tab-btn" data-tab="shipping">Shipping & Delivery</button>
        </div>

        <div class="tab-content active" id="description">
            <p><?= nl2br(htmlspecialchars($puppy['description'] ?? '')); ?></p>
        </div>

        <div class="tab-content" id="health">
            <p>
                All puppies are vaccinated, dewormed, and come with official health documentation.
                We provide breeder support and a structured health guarantee.
            </p>
        </div>

        <div class="tab-content" id="shipping">
            <p>
                Safe nationwide delivery is available through licensed transport services.
                Pickup options can also be arranged.
            </p>
        </div>
    </div>
</div>

<script src="<?= $basePath; ?>/assets/js/product.js" defer></script>

<?php require __DIR__ . '/../template/footer.php'; ?>
