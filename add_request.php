<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$db_file = 'database/requests.db';

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إنشاء الجدول إذا لم يكن موجودًا
    $db->exec("CREATE TABLE IF NOT EXISTS requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        phone TEXT NOT NULL,
        service_id INTEGER NOT NULL,
        details TEXT NOT NULL,
        created_at TEXT NOT NULL
    )");
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection or table creation failed: ' . $e->getMessage()]));
}

// التحقق من وجود البيانات في الطلب
if (!isset($_POST['name']) || empty($_POST['name']) ||
    !isset($_POST['phone']) || empty($_POST['phone']) ||
    !isset($_POST['service_id']) || !is_numeric($_POST['service_id']) ||
    !isset($_POST['details']) || empty($_POST['details'])) {
    die(json_encode(['error' => 'Invalid request data.']));
}

$name = $_POST['name'];
$phone = $_POST['phone'];
$service_id = intval($_POST['service_id']);
$details = $_POST['details'];
$created_at = date('Y-m-d H:i:s');

try {
    $stmt = $db->prepare("INSERT INTO requests (name, phone, service_id, details, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $phone, $service_id, $details, $created_at]);

    echo json_encode(['message' => 'Service request added successfully.', 'request_id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error adding service request: ' . $e->getMessage()]));
}
?>