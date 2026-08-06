<?php
require_once __DIR__ . '/../../../inc/security.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}
requireValidCSRF();

$action = $_POST['bulk_action'] ?? '';

if ($action === 'all') {
    // Delete all posts and their tags
    $conn->query('DELETE FROM post_tags');
    $conn->query('DELETE FROM posts');

} elseif ($action === 'selected') {
    $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $stmt = $conn->prepare("DELETE FROM post_tags WHERE post_id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM posts WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
    }

} else {
    // Legacy single delete
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare('DELETE FROM post_tags WHERE post_id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $stmt = $conn->prepare('DELETE FROM posts WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }
}

header('Location: index.php');
exit();
