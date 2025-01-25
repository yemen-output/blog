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

// تحديد مسارات الملفات وقاعدة البيانات بناءً على النوع
switch ($type) {
    case 'comments':
        $db_file = '../database/comments.db';
        $json_file = '../data/comments.json';
        $table = 'comments';
        break;
    case 'requests':
        $db_file = '../database/requests.db';
        $json_file = '../data/requests.json';
        $table = 'requests';
        break;
    case 'volunteers':
        $db_file = '../database/volunteers.db';
        $json_file = '../data/volunteers.json';
        $table = 'volunteers';
        break;
    case 'contacts':
        $db_file = '../database/contacts.db';
        $json_file = '../data/contacts.json';
        $table = 'contacts';
        break;
    case 'courses':
        $db_file = '../database/courses_registrations.db';
        $json_file = '../data/courses_registrations.json';
        $table = 'courses_registrations';
        break;
    default:
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid "type" parameter.']);
        exit;
}

// التأكد من أن المجلد موجود
if (!is_dir('../data')) {
    mkdir('../data', 0777, true);
}

// التحقق من وجود ملف JSON وإنشائه إذا لزم الأمر
if (!file_exists($json_file)) {
    touch($json_file);
}

// دالة لجلب البيانات من جدول
function fetchData($db_file, $query) {
    if (!file_exists($db_file)) {
        return []; // إرجاع مصفوفة فارغة في حالة عدم وجود الملف
    }
    try {
        $db = new PDO('sqlite:' . $db_file);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return []; // إرجاع مصفوفة فارغة في حالة حدوث خطأ في قاعدة البيانات
    }
}

try {
    // جلب البيانات
    $data = fetchData($db_file, "SELECT * FROM {$table}");

    // حفظ البيانات في ملف JSON
    $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $result = file_put_contents($json_file, $json_data);
    if ($result === false) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Error writing to JSON file.']);
        exit;
    }

    http_response_code(200); // OK
    echo json_encode(['message' => "Data exported to JSON successfully for type: {$type}."]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => "Error exporting data for type {$type}: " . $e->getMessage()]);
}
?>