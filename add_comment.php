<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method. Use POST.']);
    exit;
}

$db_file = 'database/comments.db';
$json_file = 'data/comments.json';

try {
    if (!file_exists($db_file)) {
        $db = new PDO('sqlite:' . $db_file);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            post_id INTEGER NOT NULL,
            comment TEXT NOT NULL,
            created_at TEXT NOT NULL
        )");
    } else {
        $db = new PDO('sqlite:' . $db_file);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database connection or table creation failed: ' . $e->getMessage()]);
    exit;
}

// التحقق من وجود post_id و comment في الطلب
if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || !isset($_POST['comment']) || empty($_POST['comment'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid post ID or comment.']);
    exit;
}

$post_id = intval($_POST['post_id']);
$comment = htmlspecialchars($_POST['comment']); // إضافة htmlspecialchars لتجنب XSS
$created_at = date('Y-m-d H:i:s');

// دالة للحصول على أعلى رقم ID
function getHighestId($db, $jsonFile, $tableName) {
    $highestId = 0;

    // من قاعدة البيانات
    try {
        $stmt = $db->query("SELECT MAX(id) FROM " . $tableName);
        $db_max_id = $stmt->fetchColumn();
        if ($db_max_id !== false) {
            $highestId = max($highestId, (int)$db_max_id);
        }
    } catch (PDOException $e) {
        //  معالجة الخطأ
    }

    // من ملف JSON
    if (file_exists($jsonFile)) {
        $json_content = file_get_contents($jsonFile);
        $json_data = json_decode($json_content, true);
        if (is_array($json_data)) {
            foreach ($json_data as $item) {
                if (isset($item['id']) && is_numeric($item['id'])) {
                    $highestId = max($highestId, (int)$item['id']);
                }
            }
        }
    }

    return $highestId;
}

try {
    $highestId = getHighestId($db, $json_file, 'comments');
    $newId = $highestId + 1;

    $stmt = $db->prepare("INSERT INTO comments (id, post_id, comment, created_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$newId, $post_id, $comment, $created_at]);

    http_response_code(200); // OK
    echo json_encode(['message' => 'Comment added successfully.', 'comment_id' => $newId]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error adding comment: ' . $e->getMessage()]);
}
?>