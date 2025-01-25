<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$status_file = '../data/courses_registrations_status.json';

// التحقق من وجود البيانات في الطلب
if (!isset($_POST['registration_id']) || !is_numeric($_POST['registration_id']) ||
    !isset($_POST['status']) || empty($_POST['status'])) {
    die(json_encode(['error' => 'Invalid request data.']));
}

$registration_id = intval($_POST['registration_id']);
$status = $_POST['status'];

$amount = isset($_POST['amount']) ? $_POST['amount'] : null;


// قراءة ملف الحالة
$statuses = [];
if (file_exists($status_file)) {
    $statuses_content = file_get_contents($status_file);
      if ($statuses_content !== false) {
        $statuses = json_decode($statuses_content, true) ?: [];
      }

}
// تحديث حالة التسجيل أو إضافته إذا لم يكن موجودًا
$statuses[$registration_id]['status'] = $status;
if($amount !== null){
       $statuses[$registration_id]['amount'] = $amount;

}
// حفظ البيانات في ملف JSON
$result = file_put_contents($status_file, json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($result !== false) {
    echo json_encode(['message' => 'Course registration status updated successfully.']);
} else {
    echo json_encode(['error' => 'Error updating course registration status.']);
}
?>