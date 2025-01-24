<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$db_file = '../database/comments.db';

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
    die(json_encode(['error' => 'Invalid post ID.']));
}

$post_id = intval($_POST['post_id']);

try {
    $stmt = $db->prepare("SELECT id, comment, created_at FROM comments WHERE post_id = ? ORDER BY created_at DESC");
    $stmt->execute([$post_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($comments);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error fetching comments: ' . $e->getMessage()]));
}
?>