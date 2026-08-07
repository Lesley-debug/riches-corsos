<?php
require_once __DIR__ . '/../../inc/security.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$normalizeImagePath = static fn(?string $path): string => normalize_site_url($path, '');
?>



<main class="admin-content">

    <div class="admin-topbar">
        <h1>All Puppies</h1>
        <a href="create.php" class="btn-add">+ Add New</a>
    </div>

    <?php
    // Pagination
    $limit = 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    // Count total
    $totalResult = $conn->query("SELECT COUNT(*) as total FROM puppies");
    $totalRow = $totalResult->fetch_assoc();
    $totalPosts = $totalRow['total'];
    $totalPages = ceil($totalPosts / $limit);

    // Fetch paginated data
    $stmt = $conn->prepare("SELECT * FROM puppies ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <div class="table-wrapper">
        <form method="POST" action="bulk-delete.php">
            <?= csrfField(); ?>
            <button type="submit" class="btn-danger" onclick="return confirm('Delete selected posts?');">Delete Selected</button>

            <table class="admin-table">

                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Breed</th>
                        <th>Age</th>
                        <th>Sex</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?= $row['id']; ?>"></td>
                            
                            <td>
                                <?php if (!empty($row['featured_image'])): ?>
                                    <img src="<?= $normalizeImagePath($row['featured_image']); ?>" class="table-thumb">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </td>

                            <td><strong><?= htmlspecialchars($row['title']); ?></strong></td>
                            <td class="price-cell">$<?= number_format($row['price'], 2); ?></td>
                            <td><?= htmlspecialchars($row['breed']); ?></td>
                            <td><?= htmlspecialchars($row['age']); ?></td>
                            <td><?= htmlspecialchars($row['sex']); ?></td>
                            <td><span class="badge badge-<?= strtolower($row['status']); ?>"><?= htmlspecialchars($row['status']); ?></span></td>
                            <td><?= htmlspecialchars($row['category']); ?></td>
                            <td><?= date("M d, Y", strtotime($row['created_at'])); ?></td>

                            <td class="actions">
                                <a href="#" class="action-btn view-btn" onclick="openModal(<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>); return false;">View</a>
                                <a href="edit.php?id=<?= $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <form method="POST" action="delete.php" style="display:inline;" onsubmit="return confirm('Delete this puppy?');">
                                    <?= csrfField(); ?>
                                    <input type="hidden" name="id" value="<?= (int)$row['id']; ?>">
                                    <button type="submit" class="action-btn delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </tbody>
            </table>

        </form>



    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i; ?>"
                class="<?= ($i == $page) ? 'active-page' : ''; ?>">
                <?= $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <!-- Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

</main>



<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
const adminBaseUrl = <?= json_encode(BASE_URL); ?>;

function normalizeAdminImagePath(path) {
    let normalized = String(path || '').trim().replace(/\\/g, '/');

    if (!normalized || normalized.startsWith('http://') || normalized.startsWith('https://')) {
        return normalized;
    }

    normalized = normalized.replace(/^\.?\//, '');
    normalized = normalized.replace(/^\/?richescorsos\//, '');
    normalized = normalized.replace(/^\/?uploads\//, 'uploads/');

    return (adminBaseUrl || '') + '/' + normalized.replace(/^\/+/, '');
}

document.getElementById('selectAll').addEventListener('click', function() {
    let checkboxes = document.querySelectorAll('input[name="ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

function openModal(data) {
    let galleryImages = '';
    if (data.gallery) {
        let gallery = JSON.parse(data.gallery);
        gallery.forEach(img => {
            let normalizedImg = normalizeAdminImagePath(img);
            galleryImages += `<img src="${normalizedImg}" class="modal-gallery-img">`;
        });
    }

    let featuredImg = data.featured_image;
    if (featuredImg) {
        featuredImg = normalizeAdminImagePath(featuredImg);
    }

    document.getElementById('modalBody').innerHTML = `
        <h2>${data.title}</h2>
        <div class="modal-details">
            <p><strong>Price:</strong> <span class="modal-price">$${parseFloat(data.price).toFixed(2)}</span></p>
            <p><strong>Name:</strong> ${data.name}</p>
            <p><strong>Breed:</strong> ${data.breed}</p>
            <p><strong>Age:</strong> ${data.age}</p>
            <p><strong>Sex:</strong> ${data.sex}</p>
            <p><strong>Status:</strong> <span class="badge badge-${data.status.toLowerCase()}">${data.status}</span></p>
            <p><strong>Category:</strong> ${data.category}</p>
        </div>
        ${featuredImg ? `<img src="${featuredImg}" class="modal-featured-img">` : ''}
        ${galleryImages ? `<div class="modal-gallery"><h4>Gallery:</h4>${galleryImages}</div>` : ''}
        ${data.description ? `<div class="modal-description"><h4>Description:</h4><p>${data.description}</p></div>` : ''}
    `;

    document.getElementById('viewModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('viewModal').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('viewModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
