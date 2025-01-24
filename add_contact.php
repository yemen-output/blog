<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$db_file = 'database/contacts.db';

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إنشاء الجدول إذا لم يكن موجودًا
    $db->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        phone TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at TEXT NOT NULL
    )");
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection or table creation failed: ' . $e->getMessage()]));
}

// التحقق من وجود البيانات في الطلب
if (!isset($_POST['name']) || empty($_POST['name']) ||
    !isset($_POST['phone']) || empty($_POST['phone']) ||
    !isset($_POST['message']) || empty($_POST['message'])) {
    die(json_encode(['error' => 'Invalid request data.']));
}

$name = $_POST['name'];
$phone = $_POST['phone'];
$message = $_POST['message'];
$created_at = date('Y-m-d H:i:s');

try {
    $stmt = $db->prepare("INSERT INTO contacts (name, phone, message, created_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $phone, $message, $created_at]);

    echo json_encode(['message' => 'Contact message added successfully.', 'contact_id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error adding contact message: ' . $e->getMessage()]));
}
?>