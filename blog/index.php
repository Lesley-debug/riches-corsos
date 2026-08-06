<?php
require_once '../admin/includes/db.php';
include '../template/header.php';

$normalizeImagePath = static fn(?string $path): string => normalize_site_url($path);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$tag = $_GET['tag'] ?? '';

$where = "WHERE posts.status='published'";
$params = [];
$types = '';

if ($search) {
    $searchTerm = "%$search%";
    $where .= " AND (posts.title LIKE ? OR posts.content LIKE ?)";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

if ($category) {
    $where .= " AND categories.slug = ?";
    $params[] = $category;
    $types .= "s";
}

if ($tag) {
    $where .= " AND tags.slug = ?";
    $params[] = $tag;
    $types .= "s";
}

// Get total count for pagination
$countQuery = "
SELECT COUNT(DISTINCT posts.id) as total
FROM posts
LEFT JOIN categories ON posts.category_id = categories.id
LEFT JOIN post_tags ON posts.id = post_tags.post_id
LEFT JOIN tags ON post_tags.tag_id = tags.id
$where
";

if ($types) {
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $totalResult = $countStmt->get_result();
} else {
    $totalResult = $conn->query($countQuery);
}

$totalPosts = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $limit);

// Get posts for current page
$query = "
SELECT DISTINCT posts.*, categories.name AS category_name
FROM posts
LEFT JOIN categories ON posts.category_id = categories.id
LEFT JOIN post_tags ON posts.id = post_tags.post_id
LEFT JOIN tags ON post_tags.tag_id = tags.id
$where
ORDER BY posts.created_at DESC
LIMIT $limit OFFSET $offset
";

if ($types) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

?>

<section class="page-hero" style="background-image: url('<?= $basePath; ?>/assets/images/happy_puppy.jpeg')">
  <div class="page-hero-inner">
    <span class="page-hero-eyebrow">Our Blog</span>
    <h1>Insights & Stories</h1>
    <p>Expert guidance, breed knowledge, and behind-the-scenes at Riches Corsos.</p>
    <form method="GET" class="hero-search">
      <input type="text" name="search" placeholder="Search articles..." value="<?= htmlspecialchars($search); ?>">
      <button type="submit">Search</button>
    </form>
    <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
      <a href="../index.php">Home</a><span>/</span><span>Blog</span>
    </nav>
  </div>
</section>

<div class="blog-container">

    <!-- Main posts column -->
    <div class="blog-main">
        <?php if ($search): ?>
            <div class="active-filter">
                Showing results for:
                <strong>"<?= htmlspecialchars($search); ?>"</strong>
                <a href="index.php" class="clear-filter">✕ Clear</a>
            </div>
        <?php endif; ?>
        <?php while ($post = $result->fetch_assoc()): ?>
            <div class="blog-post-card">
                <?php if (!empty($post['featured_image'])): ?>
                    <div class="post-image">
                        <a href="single-puppy.php?slug=<?= $post['slug']; ?>">
                            <img src="<?= htmlspecialchars($normalizeImagePath($post['featured_image'])); ?>"
                                alt="<?= htmlspecialchars($post['title']); ?>" loading="lazy">
                        </a>
                    </div>
                <?php endif; ?>

                <div class="post-content">
                    <span class="post-category">
                        <?= htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                    </span>

                    <h2>
                        <a href="single-puppy.php?slug=<?= $post['slug']; ?>">
                            <?= htmlspecialchars($post['title']); ?>
                        </a>
                    </h2>

                    <p class="post-meta">
                        <?= date('F d, Y', strtotime($post['created_at'])); ?>
                    </p>

                    <p class="post-excerpt">
                        <?= htmlspecialchars($post['excerpt']); ?>
                    </p>

                    <a href="single-puppy.php?slug=<?= $post['slug']; ?>" class="read-more-btn">
                        Continue Reading →
                    </a>
                </div>
            </div>
        <?php endwhile; ?>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?><?php if ($search) echo '&search=' . urlencode($search); ?><?php if ($category) echo '&category=' . urlencode($category); ?><?php if ($tag) echo '&tag=' . urlencode($tag); ?>">Previous</a>
            <?php endif; ?>

            <span><?= $page ?> of <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?php if ($search) echo '&search=' . urlencode($search); ?><?php if ($category) echo '&category=' . urlencode($category); ?><?php if ($tag) echo '&tag=' . urlencode($tag); ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="blog-sidebar">

        <!-- Search -->
        <div class="sidebar-widget">
            <h3>Search</h3>
            <form method="GET" class="sidebar-search-form">
                <input type="text" name="search" placeholder="Search posts..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Categories -->
        <div class="sidebar-widget">
            <h3>Categories</h3>
            <ul class="sidebar-list">
                <?php
                $catRes = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                while ($c = $catRes->fetch_assoc()):
                ?>
                    <li>
                        <a class="<?= $category === $c['slug'] ? 'active' : '' ?>"
                            href="?category=<?= $c['slug']; ?>">
                            <?= htmlspecialchars($c['name']); ?>
                        </a>
                    </li>

                <?php endwhile; ?>
            </ul>
        </div>

        <!-- Tags -->
        <div class="sidebar-widget">
            <h3>Tags</h3>
            <ul class="sidebar-list">
                <?php
                $tagRes = $conn->query("SELECT * FROM tags ORDER BY name ASC");
                while ($t = $tagRes->fetch_assoc()):
                ?>
                    <li>
                        <a class="<?= $tag === $t['slug'] ? 'active' : '' ?>"
                            href="?tag=<?= $t['slug']; ?>">
                            <?= htmlspecialchars($t['name']); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- Latest Posts -->
        <div class="sidebar-widget">
            <h3>Latest Posts</h3>
            <ul class="latest-posts-list">
                <?php
                $latest = $conn->query("SELECT slug, title FROM posts WHERE status='published' ORDER BY created_at DESC LIMIT 5");
                while ($l = $latest->fetch_assoc()):
                ?>
                    <li><a href="single-puppy.php?slug=<?= $l['slug']; ?>"><?= htmlspecialchars($l['title']); ?></a></li>
                <?php endwhile; ?>
            </ul>
        </div>

    </div>

</div>
<?php include '../template/footer.php'; ?>
