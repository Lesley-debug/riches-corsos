<?php
require_once '../admin/includes/db.php';

// fetch slug and post data
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    die("Post not found.");
}

$slug = $_GET['slug'];

$stmt = $conn->prepare("
SELECT posts.*, categories.name AS category_name
FROM posts
LEFT JOIN categories ON posts.category_id = categories.id
WHERE posts.slug=? AND posts.status='published'
LIMIT 1
");

$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Post not found.");
}

$post = $result->fetch_assoc();

// meta variables for header
$pageTitle       = $post['title'] . ' | Riches Corsos';
$metaDescription = $post['excerpt'];
$metaImage       = '';
if (!empty($post['featured_image'])) {
    $metaImagePath = trim((string)$post['featured_image']);
    if (preg_match('#^https?://#', $metaImagePath)) {
        $metaImage = $metaImagePath;
    } else {
        $metaImagePath = preg_replace('#^/richescorsos/#', '/', $metaImagePath);
        $metaImage = 'https://richescorsos.com/' . ltrim($metaImagePath, '/');
    }
}
$canonicalUrl    = 'https://richescorsos.com/blog/single-puppy.php?slug=' . urlencode($post['slug']);
$ogType         = 'article';

// increase view count
$updateViews = $conn->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
$updateViews->bind_param("i", $post['id']);
$updateViews->execute();

// reading time calculation (200 wpm)
$wordCount   = str_word_count(strip_tags($post['content']));
$readingTime = ceil($wordCount / 200);

// neighbouring posts
$prevStmt = $conn->prepare("
SELECT slug, title FROM posts
WHERE created_at < ? AND status='published'
ORDER BY created_at DESC LIMIT 1
");
$prevStmt->bind_param("s", $post['created_at']);
$prevStmt->execute();
$prevPost = $prevStmt->get_result()->fetch_assoc();

$nextStmt = $conn->prepare("
SELECT slug, title FROM posts
WHERE created_at > ? AND status='published'
ORDER BY created_at ASC LIMIT 1
");
$nextStmt->bind_param("s", $post['created_at']);
$nextStmt->execute();
$nextPost = $nextStmt->get_result()->fetch_assoc();

// related posts
$relatedStmt = $conn->prepare("
SELECT slug, title, featured_image
FROM posts
WHERE category_id = ? 
AND id != ?
AND status='published'
ORDER BY created_at DESC
LIMIT 3
");
$relatedStmt->bind_param("ii", $post['category_id'], $post['id']);
$relatedStmt->execute();
$related = $relatedStmt->get_result();

// tags
$tagStmt = $conn->prepare("
SELECT tags.name, tags.slug
FROM tags
INNER JOIN post_tags ON tags.id = post_tags.tag_id
WHERE post_tags.post_id = ?
");
$tagStmt->bind_param("i", $post['id']);
$tagStmt->execute();
$tagResult = $tagStmt->get_result();

$normalizeImagePath = static fn(?string $path): string => normalize_site_url($path);

$shareUrl = 'https://richescorsos.com/blog/single-puppy.php?slug=' . urlencode($post['slug']);
?>

<?php include '../template/header.php'; ?>

<section class="page-hero page-hero--post" style="background-image: url('<?= !empty($post['featured_image']) ? htmlspecialchars($normalizeImagePath($post['featured_image'])) : $basePath . '/assets/images/happy_puppy.webp'; ?>')">
  <div class="page-hero-inner">
    <span class="page-hero-eyebrow"><?= htmlspecialchars($post['category_name'] ?? 'Blog'); ?></span>
    <h1><?= htmlspecialchars($post['title']); ?></h1>
    <p class="post-hero-meta">
      <?= date('F d, Y', strtotime($post['created_at'])); ?> &nbsp;&middot;&nbsp;
      <?= $readingTime; ?> min read &nbsp;&middot;&nbsp;
      <?= (int)$post['views'] + 1; ?> views
    </p>
    <nav class="page-hero-breadcrumb" aria-label="Breadcrumb">
      <a href="../index.php">Home</a><span>/</span>
      <a href="index.php">Blog</a><span>/</span>
      <span><?= htmlspecialchars(mb_strimwidth($post['title'], 0, 40, '…')); ?></span>
    </nav>
  </div>
</section>

<section class="single-post-content">
  <div class="single-post-main">

    <?php if (!empty($post['featured_image'])): ?>
      <div class="featured-image">
        <img src="<?= htmlspecialchars($normalizeImagePath($post['featured_image'])); ?>"
             alt="<?= htmlspecialchars($post['title']); ?>">
      </div>
    <?php endif; ?>

    <div class="post-body">
      <?= nl2br($post['content']); ?>
    </div>

    <?php if ($tagResult->num_rows > 0): ?>
      <div class="post-tags">
        <strong>Tags:</strong>
        <?php while ($tag = $tagResult->fetch_assoc()): ?>
          <a href="index.php?tag=<?= $tag['slug']; ?>"><?= htmlspecialchars($tag['name']); ?></a>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>

    <div class="share-buttons">
      <strong>Share:</strong>
      <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl); ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
        Facebook
      </a>
      <a target="_blank" href="https://twitter.com/intent/tweet?url=<?= urlencode($shareUrl); ?>&text=<?= urlencode($post['title']); ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
        Twitter
      </a>
      <a target="_blank" href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' ' . $shareUrl); ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
        WhatsApp
      </a>
    </div>

    <nav class="post-navigation">
      <?php if ($prevPost): ?>
        <a href="single-puppy.php?slug=<?= $prevPost['slug']; ?>">← <?= htmlspecialchars(mb_strimwidth($prevPost['title'], 0, 55, '…')); ?></a>
      <?php else: ?><span></span><?php endif; ?>
      <?php if ($nextPost): ?>
        <a href="single-puppy.php?slug=<?= $nextPost['slug']; ?>"><?= htmlspecialchars(mb_strimwidth($nextPost['title'], 0, 55, '…')); ?> →</a>
      <?php endif; ?>
    </nav>

  </div>

  <aside class="single-post-sidebar">

    <?php
    $latestSide = $conn->query("SELECT slug, title, featured_image FROM posts WHERE status='published' ORDER BY created_at DESC LIMIT 5");
    ?>
    <div class="sidebar-card">
      <h4>Latest Articles</h4>
      <ul class="sidebar-latest-list">
        <?php while ($l = $latestSide->fetch_assoc()): ?>
          <li>
            <a href="single-puppy.php?slug=<?= $l['slug']; ?>">
              <?php if (!empty($l['featured_image'])): ?>
                <img src="<?= htmlspecialchars($normalizeImagePath($l['featured_image'])); ?>" alt="">
              <?php endif; ?>
              <?= htmlspecialchars($l['title']); ?>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>

    <div class="sidebar-card sidebar-cta">
      <h4>Ready for a Puppy?</h4>
      <p>Browse our available Cane Corso puppies and reserve yours today.</p>
      <a href="../shop/shop.php">View Available Puppies</a>
    </div>

  </aside>
</section>

<?php if ($related->num_rows > 0): ?>
<section class="related-posts">
  <h3>Related Articles</h3>
  <div class="related-grid">
    <?php while ($r = $related->fetch_assoc()): ?>
      <a href="single-puppy.php?slug=<?= $r['slug']; ?>" class="related-card">
        <img src="<?= htmlspecialchars($normalizeImagePath($r['featured_image'])); ?>" alt="<?= htmlspecialchars($r['title']); ?>">
        <span><?= htmlspecialchars($r['title']); ?></span>
      </a>
    <?php endwhile; ?>
  </div>
</section>
<?php endif; ?>

<?php include '../template/footer.php'; ?>
