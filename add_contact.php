<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method. Use POST.']);
    exit;
}

$db_file = 'database/contacts.db';
$json_file = 'data/contacts.json';

try {
    if (!file_exists($db_file)) {
        $db = new PDO('sqlite:' . $db_file);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            phone TEXT NOT NULL,
            message TEXT NOT NULL,
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

// التحقق من وجود البيانات في الطلب
if (!isset($_POST['name']) || empty($_POST['name']) ||
    !isset($_POST['phone']) || empty($_POST['phone']) ||
    !isset($_POST['message']) || empty($_POST['message'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid request data.']);
    exit;
}

$name = htmlspecialchars($_POST['name']);
$phone = htmlspecialchars($_POST['phone']);
$message = htmlspecialchars($_POST['message']);
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
        // معالجة الخطأ
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
    $highestId = getHighestId($db, $json_file, 'contacts');
    $newId = $highestId + 1;

    $stmt = $db->prepare("INSERT INTO contacts (id, name, phone, message, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$newId, $name, $phone, $message, $created_at]);

    http_response_code(200); // OK
    echo json_encode(['message' => 'Contact message added successfully.', 'contact_id' => $newId]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error adding contact message: ' . $e->getMessage()]);
}
?>