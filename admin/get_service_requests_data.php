<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method. Use POST.']);
    exit;
}

$db_file = '../database/requests.db';
$status_file = '../data/requests_status.json';
$json_file = '../data/requests.json';
$data_type = isset($_POST['data_type']) ? $_POST['data_type'] : 'all';

$requests = [];

// دالة لجلب البيانات من قاعدة البيانات
function fetchDataFromDB($db) {
    try {
        $stmt = $db->prepare("SELECT id, name, phone, service_id, created_at FROM requests ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // إرجاع مصفوفة فارغة في حالة حدوث خطأ
        return [];
    }
}

// دالة لجلب البيانات من ملف JSON
function fetchDataFromJSON($jsonFile) {
    $requests = [];
    if (file_exists($jsonFile)) {
        $json_content = file_get_contents($jsonFile);
        if ($json_content !== false) {
            $requests = json_decode($json_content, true);
            // إرجاع مصفوفة فارغة في حالة فشل التحليل
            if (!is_array($requests)) {
                $requests = [];
            }
        }
    } else {
        // إرجاع مصفوفة فارغة في حالة عدم وجود الملف
        $requests = [];
    }
    // ترتيب البيانات تنازليًا حسب تاريخ الإنشاء
    usort($requests, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    return $requests;
}

// التحقق من وجود قاعدة البيانات
if (file_exists($db_file)) {
    try {
        $db = new PDO('sqlite:' . $db_file);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // جلب البيانات من قاعدة البيانات
        if ($data_type === 'new' || $data_type === 'all') {
            $requests = array_merge($requests, fetchDataFromDB($db));
        }
    } catch (PDOException $e) {
        // تجاهل الخطأ والاستمرار في جلب البيانات من المصادر الأخرى
    }
}

// قراءة ملف الحالة
$statuses = [];
if (file_exists($status_file)) {
    $statuses_content = file_get_contents($status_file);
    if ($statuses_content !== false) {
        $statuses = json_decode($statuses_content, true);
        // استخدام مصفوفة فارغة في حالة فشل التحليل
        if (!is_array($statuses)) {
            $statuses = [];
        }
    }
}

// إضافة حالة الطلب
foreach ($requests as &$request) {
    $request_id = $request['id'];
    $request['status'] = $statuses[$request_id]['status'] ?? 'قيد المراجعة';
    if (isset($statuses[$request_id]['amount'])) {
        $request['amount'] = $statuses[$request_id]['amount'];
    }
}

// جلب البيانات من ملف JSON
if ($data_type === 'old' || $data_type === 'all') {
    $requests = array_merge($requests, fetchDataFromJSON($json_file));
}

// ترتيب البيانات تنازليًا حسب تاريخ الإنشاء (في حالة وجوده)
usort($requests, function ($a, $b) {
    $dateA = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
    $dateB = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
    return $dateB - $dateA;
});

echo json_encode($requests);
?>