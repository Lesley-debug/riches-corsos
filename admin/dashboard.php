<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

// --- Stats Queries ---
$total = $conn->query("SELECT COUNT(*) as count FROM puppies")->fetch_assoc()['count'];
$available = $conn->query("SELECT COUNT(*) as count FROM puppies WHERE status='Available'")->fetch_assoc()['count'];
$sold = $conn->query("SELECT COUNT(*) as count FROM puppies WHERE status='Sold'")->fetch_assoc()['count'];
$reserved = $conn->query("SELECT COUNT(*) as count FROM puppies WHERE status='Reserved'")->fetch_assoc()['count'];
$draft = $conn->query("SELECT COUNT(*) as count FROM puppies WHERE status='Draft'")->fetch_assoc()['count'];

$totalPosts = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
$publishedPosts = $conn->query("SELECT COUNT(*) as count FROM posts WHERE status='published'")->fetch_assoc()['count'];
$totalCategories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$totalTags = $conn->query("SELECT COUNT(*) as count FROM tags")->fetch_assoc()['count'];

// Total Value
$totalValue = $conn->query("SELECT SUM(price) as total FROM puppies WHERE status='Available'")->fetch_assoc()['total'] ?? 0;

// Recent Puppies
$recent = $conn->query("SELECT * FROM puppies ORDER BY id DESC LIMIT 5");
?>

<main class="admin-content">

    <div class="admin-topbar">
        <h1>Dashboard</h1>
    </div>

    <!-- Stats Cards -->
    <div class="dashboard-cards">

        <div class="card">
            <h3>Total Puppies</h3>
            <p><?= $total ?></p>
        </div>

        <div class="card">
            <h3>Available</h3>
            <p><?= $available ?></p>
        </div>

        <div class="card">
            <h3>Reserved</h3>
            <p><?= $reserved ?></p>
        </div>

        <div class="card">
            <h3>Sold</h3>
            <p><?= $sold ?></p>
        </div>

        <div class="card">
            <h3>Draft</h3>
            <p><?= $draft ?></p>
        </div>

        <div class="card highlight">
            <h3>Available Value</h3>
            <p>$<?= number_format($totalValue, 2) ?></p>
        </div>

        <div class="card">
            <h3>Total Posts</h3>
            <p><?= $totalPosts ?></p>
        </div>

        <div class="card">
            <h3>Published Posts</h3>
            <p><?= $publishedPosts ?></p>
        </div>

        <div class="card">
            <h3>Total Categories</h3>
            <p><?= $totalCategories ?></p>
        </div>

        <div class="card">
            <h3>Total Tags</h3>
            <p><?= $totalTags ?></p>
        </div>

    </div>

    <!-- Recent Posts -->
    <div class="recent-section">
        <h2>Recent Puppies</h2>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recent->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td>
                            <span class="badge <?= strtolower($row['status']) ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td class="table-action">
                            <a href="shop/edit.php?id=<?= $row['id'] ?>">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
