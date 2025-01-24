<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$db_file = 'database/courses_registrations.db';

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إنشاء الجدول إذا لم يكن موجودًا
    $db->exec("CREATE TABLE IF NOT EXISTS courses_registrations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL,
        course_id INTEGER NOT NULL,
        notes TEXT,
        created_at TEXT NOT NULL
    )");
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection or table creation failed: ' . $e->getMessage()]));
}

// التحقق من وجود البيانات في الطلب
if (
    !isset($_POST['full_name']) || empty($_POST['full_name']) ||
    !isset($_POST['email']) || empty($_POST['email']) ||
    !isset($_POST['phone']) || empty($_POST['phone']) ||
    !isset($_POST['course_id']) || !is_numeric($_POST['course_id'])
) {
    die(json_encode(['error' => 'Invalid registration data.']));
}

$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$course_id = intval($_POST['course_id']);
$notes = isset($_POST['notes']) ? $_POST['notes'] : '';
$created_at = date('Y-m-d H:i:s');

try {
    $stmt = $db->prepare("INSERT INTO courses_registrations (full_name, email, phone, course_id, notes, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $phone, $course_id, $notes, $created_at]);

    echo json_encode(['message' => 'Course registration added successfully.', 'registration_id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error adding course registration: ' . $e->getMessage()]));
}
?>