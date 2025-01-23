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
    echo json_encode(['message' => 'يرجى تحديد معرف المحتوى']);
    exit;
}

// جلب البيانات
$id = intval($data['id']);

// قراءة محتويات ملف contents.json
$contentsFile = '../data/contents.json';
if (!file_exists($contentsFile)) {
    http_response_code(404); // Not Found
    echo json_encode(['message' => 'ملف المحتويات غير موجود']);
    exit;
}
$contents = json_decode(file_get_contents($contentsFile), true);

// البحث عن المحتوى المراد حذفه
$contentIndex = -1;
foreach ($contents as $index => $content) {
    if ($content['id'] === $id) {
        $contentIndex = $index;
        break;
    }
}

if ($contentIndex === -1) {
    http_response_code(404); // Not Found
    echo json_encode(['message' => 'المحتوى غير موجود']);
    exit;
}

// حذف الصورة المرتبطة بالمحتوى
$targetDir = "../data/content-images/";
$imageToDelete = $targetDir . $contents[$contentIndex]['image_name'];
if (file_exists($imageToDelete)) {
    unlink($imageToDelete);
}

// إزالة المحتوى من المصفوفة
array_splice($contents, $contentIndex, 1);

// حفظ التغييرات في ملف contents.json
file_put_contents($contentsFile, json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['message' => 'تم حذف المحتوى بنجاح']);
?>