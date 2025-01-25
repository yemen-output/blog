<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method. Use POST.']);
    exit;
}

$db_file = 'database/volunteers.db';
$json_file = 'data/volunteers.json';

try {
    if (!file_exists($db_file)) {
        $db = new PDO('sqlite:' . $db_file);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
if (!isset($_POST['full_name']) || empty($_POST['full_name']) ||
    !isset($_POST['governorate']) || empty($_POST['governorate']) ||
    !isset($_POST['specialization']) || empty($_POST['specialization']) ||
    !isset($_POST['academic_year']) || empty($_POST['academic_year']) ||
    !isset($_POST['volunteer_area']) || empty($_POST['volunteer_area']) ||
    !isset($_POST['contribution']) || empty($_POST['contribution']) ||
    !isset($_POST['expectation']) || empty($_POST['expectation']) ||
    !isset($_POST['phone']) || empty($_POST['phone']) ||
    !isset($_POST['can_train']) || empty($_POST['can_train'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid request data.']);
    exit;
}

$full_name = htmlspecialchars($_POST['full_name']);
$governorate = htmlspecialchars($_POST['governorate']);
$specialization = htmlspecialchars($_POST['specialization']);
$academic_year = htmlspecialchars($_POST['academic_year']);
$volunteer_area = htmlspecialchars($_POST['volunteer_area']);
$contribution = htmlspecialchars($_POST['contribution']);
$expectation = htmlspecialchars($_POST['expectation']);
$phone = htmlspecialchars($_POST['phone']);
$can_train = htmlspecialchars($_POST['can_train']);
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
    $highestId = getHighestId($db, $json_file, 'volunteers');
    $newId = $highestId + 1;

    $stmt = $db->prepare("INSERT INTO volunteers (id, full_name, governorate, specialization, academic_year, volunteer_area, contribution, expectation, phone, can_train, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$newId, $full_name, $governorate, $specialization, $academic_year, $volunteer_area, $contribution, $expectation, $phone, $can_train, $created_at]);

    http_response_code(200); // OK
    echo json_encode(['message' => 'Volunteer request added successfully.', 'volunteer_id' => $newId]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error adding volunteer request: ' . $e->getMessage()]);
}
?>