<?php
header('Content-Type: application/json');

// التحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'طلب غير مسموح']);
    exit;
}

// الحصول على محتوى الطلب الخام
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// التحقق من البيانات المستلمة
if (!isset($data['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'يرجى تحديد معرف الكورس']);
    exit;
}

// جلب البيانات
$id = intval($data['id']);

// قراءة محتويات ملف courses.json
$coursesFile = '../data/courses.json';
if (!file_exists($coursesFile)) {
    http_response_code(404); // Not Found
    echo json_encode(['message' => 'ملف الكورسات غير موجود']);
    exit;
}
$courses = json_decode(file_get_contents($coursesFile), true);

// البحث عن الكورس المراد حذفه
$courseIndex = -1;
foreach ($courses as $index => $course) {
    if ($course['id'] === $id) {
        $courseIndex = $index;
        break;
    }
}

if ($courseIndex === -1) {
    http_response_code(404); // Not Found
    echo json_encode(['message' => 'الكورس غير موجود']);
    exit;
}

// حذف الصورة المرتبطة بالكورس
$targetDir = "../data/course-images/";
$imageToDelete = $targetDir . $courses[$courseIndex]['image_name'];
if (file_exists($imageToDelete)) {
    unlink($imageToDelete);
}

// إزالة الكورس من المصفوفة
array_splice($courses, $courseIndex, 1);

// حفظ التغييرات في ملف courses.json
file_put_contents($coursesFile, json_encode($courses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['message' => 'تم حذف الكورس بنجاح']);
?>