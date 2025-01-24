<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$db_file = 'database/comments.db';

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إنشاء الجدول إذا لم يكن موجودًا
    $db->exec("CREATE TABLE IF NOT EXISTS comments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        post_id INTEGER NOT NULL,
        comment TEXT NOT NULL,
        created_at TEXT NOT NULL
    )");
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection or table creation failed: ' . $e->getMessage()]));
}

// تحقق من وجود post_id و comment في الطلب
if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || !isset($_POST['comment']) || empty($_POST['comment'])) {
    die(json_encode(['error' => 'Invalid post ID or comment.']));
}

$post_id = intval($_POST['post_id']);
$comment = $_POST['comment'];
$created_at = date('Y-m-d H:i:s'); // الحصول على التاريخ والوقت الحاليين

try {
    $stmt = $db->prepare("INSERT INTO comments (post_id, comment, created_at) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $comment, $created_at]);

    echo json_encode(['message' => 'Comment added successfully.', 'comment_id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error adding comment: ' . $e->getMessage()]));
}
?>