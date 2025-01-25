<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$db_file = '../database/comments.db';
$json_file = '../data/comments.json';

// التحقق من وجود post_id في الطلب
if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
    die(json_encode(['error' => 'Invalid post ID.']));
}

$post_id = intval($_POST['post_id']);
$data_type = $_POST['data_type'] ?? 'all';


$comments = [];

// دالة لجلب البيانات من قاعدة البيانات
function fetchDataFromDB($db, $post_id) {
    try {
        $stmt = $db->prepare("SELECT id, comment, created_at FROM comments WHERE post_id = ? ORDER BY created_at DESC");
        $stmt->execute([$post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// دالة لجلب البيانات من ملف JSON
function fetchDataFromJSON($jsonFile, $post_id) {
    $comments = [];
    if (file_exists($jsonFile)) {
        $json_content = file_get_contents($jsonFile);
        if ($json_content !== false) {
            $json_data = json_decode($json_content, true);
             if (is_array($json_data)) {
                foreach ($json_data as $item) {
                    if (isset($item['post_id']) && $item['post_id'] == $post_id) {
                        $comments[] = $item;
                    }
                }
            }
        }
    }
    usort($comments, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    return $comments;
}

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  if ($data_type === 'new' || $data_type === 'all') {
        $comments = array_merge($comments, fetchDataFromDB($db, $post_id));
    }
     if ($data_type === 'old' || $data_type === 'all') {
        $comments = array_merge($comments, fetchDataFromJSON($json_file, $post_id));
    }
        usort($comments, function ($a, $b) {
           return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

    echo json_encode($comments);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}
?>