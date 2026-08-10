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

$requestScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$currentProductUrl = $requestScheme . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');

$relatedStmt = $conn->prepare(
    "SELECT id, name, breed, age, sex, price, featured_image, status
     FROM puppies
     WHERE id != ? AND LOWER(TRIM(status)) IN ('available', 'reserved')
     ORDER BY RAND() LIMIT 6"
);
$relatedStmt->bind_param('i', $id);
$relatedStmt->execute();
$relatedPuppies = $relatedStmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
                <a class="share-link share-facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentProductUrl); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22 12.07C22 6.49 17.52 2 11.93 2S1.87 6.49 1.87 12.07C1.87 17.09 5.59 21.2 10.36 21.98v-7.02H7.64v-2.9h2.72V9.79c0-2.7 1.61-4.18 4.08-4.18 1.18 0 2.42.21 2.42.21v2.66h-1.37c-1.35 0-1.77.84-1.77 1.7v2.05h3.01l-.48 2.9h-2.53V21.98C18.41 21.2 22 17.09 22 12.07z" />
                    </svg>
                </a>
                <a class="share-link share-twitter" href="https://twitter.com/intent/tweet?url=<?= urlencode($currentProductUrl); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Twitter">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M23.95 4.57a10 10 0 0 1-2.83.78 4.93 4.93 0 0 0 2.17-2.72 9.86 9.86 0 0 1-3.13 1.2A4.92 4.92 0 0 0 11.78 8.3 13.96 13.96 0 0 1 1.64 3.16a4.92 4.92 0 0 0 1.52 6.57 4.9 4.9 0 0 1-2.23-.62v.06a4.93 4.93 0 0 0 3.95 4.83 4.96 4.96 0 0 1-2.22.08 4.93 4.93 0 0 0 4.6 3.42A9.88 9.88 0 0 1 0 19.54a13.94 13.94 0 0 0 7.55 2.21c9.06 0 14.01-7.5 14.01-14.01 0-.21 0-.42-.01-.64a10 10 0 0 0 2.46-2.55z" />
                    </svg>
                </a>
                <a class="share-link share-instagram" href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Open Instagram">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7.75 2h8.5A5.76 5.76 0 0 1 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2zm0 2A3.75 3.75 0 0 0 4 7.75v8.5A3.75 3.75 0 0 0 7.75 20h8.5A3.75 3.75 0 0 0 20 16.25v-8.5A3.75 3.75 0 0 0 16.25 4h-8.5zm4.25 3.2A4.8 4.8 0 1 1 7.2 12 4.8 4.8 0 0 1 12 7.2zm0 2A2.8 2.8 0 1 0 14.8 12 2.8 2.8 0 0 0 12 9.2zm5.35-2.55a1.12 1.12 0 1 1-1.12 1.12 1.12 1.12 0 0 1 1.12-1.12z" />
                    </svg>
                </a>
                <a class="share-link share-tiktok" href="https://www.tiktok.com/@canecorsopuppies60?_r=1&amp;_t=ZS-98aYUPnTKDM" target="_blank" rel="noopener noreferrer" aria-label="Open TikTok">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty(trim((string)($puppy['parent_name'] ?? ''))) || !empty(trim((string)($puppy['parent_info'] ?? ''))) || !empty(trim((string)($puppy['parent_image'] ?? '')))) : ?>
        <section class="parent-section product-parent-section" aria-labelledby="parent-section-title">
            <div class="parent-card">
                <h2 id="parent-section-title">Meet the Parents</h2>
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

<?php if (!empty($relatedPuppies)): ?>
<section class="related-section">
    <div class="related-inner">
        <div class="related-header">
            <span class="related-eyebrow">Explore More</span>
            <h2>You May Also Like</h2>
            <p>More healthy, family-raised Cane Corso puppies available now.</p>
        </div>
        <div class="related-track-wrap">
            <div class="related-track" id="relatedTrack">
                <?php foreach ($relatedPuppies as $r): ?>
                    <?php $isReserved = strtolower(trim($r['status'])) === 'reserved'; ?>
                    <article class="related-card">
                        <a href="<?= $basePath; ?>/shop/product.php?id=<?= (int)$r['id']; ?>" class="related-card-img-wrap">
                            <img src="<?= htmlspecialchars($normalizeImagePath($r['featured_image'] ?? '')); ?>" alt="<?= htmlspecialchars($r['name']); ?>" loading="lazy">
                            <?php if ($isReserved): ?>
                                <span class="related-badge reserved">Reserved</span>
                            <?php else: ?>
                                <span class="related-badge available">Available</span>
                            <?php endif; ?>
                        </a>
                        <div class="related-card-body">
                            <h3><?= htmlspecialchars($r['name']); ?></h3>
                            <p class="related-meta"><?= htmlspecialchars($r['breed']); ?> &bull; <?= htmlspecialchars($r['age']); ?> &bull; <?= htmlspecialchars($r['sex']); ?></p>
                            <div class="related-footer">
                                <span class="related-price">$<?= number_format((float)$r['price'], 2); ?></span>
                                <a href="<?= $basePath; ?>/shop/product.php?id=<?= (int)$r['id']; ?>" class="related-btn">View Details</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="related-nav">
            <button class="related-prev" id="relatedPrev" aria-label="Previous">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button class="related-next" id="relatedNext" aria-label="Next">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<script src="<?= $basePath; ?>/assets/js/product.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var track   = document.getElementById('relatedTrack');
    var prevBtn = document.getElementById('relatedPrev');
    var nextBtn = document.getElementById('relatedNext');
    if (!track || !prevBtn || !nextBtn) return;
    var cards = Array.from(track.querySelectorAll('.related-card'));
    if (!cards.length) { prevBtn.hidden = nextBtn.hidden = true; return; }
    var current = 0;
    var gap = 22;
    var perView  = function () { return window.innerWidth <= 640 ? 1 : window.innerWidth <= 1024 ? 2 : 3; };
    var maxSlide = function () { return Math.max(0, cards.length - perView()); };
    var goTo = function (n) {
        current = Math.max(0, Math.min(n, maxSlide()));
        var w = cards[0].offsetWidth + gap;
        track.style.transition = 'transform 0.45s cubic-bezier(0.4,0,0.2,1)';
        track.style.transform  = 'translateX(-' + (current * w) + 'px)';
        prevBtn.disabled = current === 0;
        nextBtn.disabled = current >= maxSlide();
    };
    prevBtn.addEventListener('click', function () { goTo(current - 1); });
    nextBtn.addEventListener('click', function () { goTo(current + 1); });
    window.addEventListener('resize', function () { goTo(current); });
    goTo(0);
});
</script>

<?php require __DIR__ . '/../template/footer.php'; ?>
