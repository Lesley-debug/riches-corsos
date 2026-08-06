<?php
require_once __DIR__ . '/../../inc/security.php'; // handles error display + session hardening safely
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$ordersResult = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");

require_once __DIR__ . '/../includes/header.php';
?>

<main class="admin-content">
    <div class="admin-topbar">
        <h1>Orders Management</h1>
        <a href="../dashboard.php" class="btn-add">← Back to Dashboard</a>
    </div>

    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ordersResult && $ordersResult->num_rows > 0): ?>
                    <?php while ($order = $ordersResult->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($order['id']); ?></td>
                            <td><?= htmlspecialchars($order['customer_name']); ?></td>
                            <td><?= htmlspecialchars($order['customer_email']); ?></td>
                            <td>$<?= number_format($order['total'], 2); ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($order['status']); ?>">
                                    <?= ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="POST" action="update-status.php" style="display:inline;">
                                    <?= csrfField(); ?>
                                    <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>



<?php require_once __DIR__ . '/../includes/footer.php'; ?>
