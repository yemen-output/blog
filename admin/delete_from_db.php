<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method. Use POST.']);
    exit;
}

// تأكد من أن البيانات المطلوبة موجودة في الطلب
if (!isset($_POST['type'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing "type" parameter.']);
    exit;
}

$type = $_POST['type'];

// تحديد مسار قاعدة البيانات بناءً على النوع
switch ($type) {
    case 'comments':
        $db_file = '../database/comments.db';
        $table = 'comments';
        break;
    case 'requests':
        $db_file = '../database/requests.db';
        $table = 'requests';
        break;
    case 'volunteers':
        $db_file = '../database/volunteers.db';
        $table = 'volunteers';
        break;
    case 'contacts':
        $db_file = '../database/contacts.db';
        $table = 'contacts';
        break;
    case 'courses':
        $db_file = '../database/courses_registrations.db';
        $table = 'courses_registrations';
        break;
    default:
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid "type" parameter.']);
        exit;
}

try {
    // حذف البيانات من قاعدة البيانات
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("DELETE FROM {$table}");

    http_response_code(200); // OK
    echo json_encode(['message' => "Data deleted from database successfully for type: {$type}."]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => "Error deleting data for type {$type}: " . $e->getMessage()]);
}
?>