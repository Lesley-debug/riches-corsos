<?php
$pageTitle = 'Search Results - Riches Corsos';
$query = trim($_GET['q'] ?? '');
if ($query) {
    $pageTitle = 'Search: ' . htmlspecialchars($query) . ' - Riches Corsos';
}

require __DIR__ . '/template/header.php';
require __DIR__ . '/admin/includes/db.php';

$normalizeImagePath = static fn(?string $path): string => normalize_site_url($path);
?>

<div class="shop-page">
    <div class="shop-shell">
        <div class="section-header" style="margin-top: 32px;">
            <h1 class="section-title">Search Results for "<?= htmlspecialchars($query); ?>"</h1>
        </div>

        <?php if ($query): ?>
            <?php
            $searchTerm = '%' . $query . '%';
            $stmt = $conn->prepare("
                SELECT * FROM puppies 
                WHERE (name LIKE ? OR breed LIKE ? OR description LIKE ? OR title LIKE ?)
                AND status = 'Available'
                ORDER BY created_at DESC
            ");
            $stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <?php if ($result->num_rows > 0): ?>
                <p style="margin-bottom:24px;color:var(--text-secondary)">Found <?= $result->num_rows; ?> result(s)</p>
                <div class="puppy-grid">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <article class="puppy-card">
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
            <?php else: ?>
                <div class="empty-cart" style="margin-top:32px">
                    <h2>No puppies found</h2>
                    <p>No puppies matched your search. Try a different term.</p>
                    <a href="<?= $basePath; ?>/shop/shop.php" class="btn-shop-now">Browse All Puppies</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-cart" style="margin-top:32px">
                <h2>Enter a search term</h2>
                <p>Use the search bar above to find puppies.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/template/footer.php'; ?>
