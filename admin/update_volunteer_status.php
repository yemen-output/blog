<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method. Use POST.']);
    exit;
}

$status_file = '../data/volunteer_status.json';

// التحقق من وجود البيانات في الطلب
if (!isset($_POST['volunteer_id']) || !is_numeric($_POST['volunteer_id']) ||
    !isset($_POST['status']) || empty($_POST['status'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid request data.']);
    exit;
}

$volunteer_id = intval($_POST['volunteer_id']);
$status = htmlspecialchars($_POST['status']); // إضافة htmlspecialchars لمنع XSS

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

// تحديث حالة المتطوع أو إضافتها إذا لم تكن موجودة
$statuses[$volunteer_id] = ['status' => $status]; // إنشاء object يحتوي على الحالة

// حفظ البيانات في ملف JSON
$result = file_put_contents($status_file, json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($result !== false) {
    http_response_code(200); // OK
    echo json_encode(['message' => 'Volunteer status updated successfully.']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error updating volunteer status.']);
}
?>