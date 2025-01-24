<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$db_file = 'database/volunteers.db';

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إنشاء الجدول إذا لم يكن موجودًا
    $db->exec("CREATE TABLE IF NOT EXISTS volunteers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        governorate TEXT NOT NULL,
        specialization TEXT NOT NULL,
        academic_year TEXT NOT NULL,
        volunteer_area TEXT NOT NULL,
         contribution TEXT NOT NULL,
        expectation TEXT NOT NULL,
        phone TEXT NOT NULL,
        can_train TEXT NOT NULL,
        created_at TEXT NOT NULL
    )");
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection or table creation failed: ' . $e->getMessage()]));
}

// التحقق من وجود البيانات في الطلب
if (
    !isset($_POST['full_name']) || empty($_POST['full_name']) ||
    !isset($_POST['governorate']) || empty($_POST['governorate']) ||
    !isset($_POST['specialization']) || empty($_POST['specialization']) ||
    !isset($_POST['academic_year']) || empty($_POST['academic_year']) ||
    !isset($_POST['volunteer_area']) || empty($_POST['volunteer_area']) ||
     !isset($_POST['contribution']) || empty($_POST['contribution']) ||
     !isset($_POST['expectation']) || empty($_POST['expectation']) ||
    !isset($_POST['phone']) || empty($_POST['phone']) ||
    !isset($_POST['can_train']) || empty($_POST['can_train'])
) {
    die(json_encode(['error' => 'Invalid request data.']));
}

$full_name = $_POST['full_name'];
$governorate = $_POST['governorate'];
$specialization = $_POST['specialization'];
$academic_year = $_POST['academic_year'];
$volunteer_area = $_POST['volunteer_area'];
$contribution = $_POST['contribution'];
$expectation = $_POST['expectation'];
$phone = $_POST['phone'];
$can_train = $_POST['can_train'];
$created_at = date('Y-m-d H:i:s');

try {
    $stmt = $db->prepare("INSERT INTO volunteers (full_name, governorate, specialization, academic_year, volunteer_area, contribution, expectation, phone, can_train, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $governorate, $specialization, $academic_year, $volunteer_area, $contribution, $expectation, $phone, $can_train, $created_at]);

    echo json_encode(['message' => 'Volunteer request added successfully.', 'volunteer_id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error adding volunteer request: ' . $e->getMessage()]));
}
?>