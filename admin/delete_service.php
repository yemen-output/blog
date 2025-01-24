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
    echo json_encode(['message' => 'يرجى تحديد معرف الخدمة']);
    exit;
}

// جلب البيانات
$id = intval($data['id']);

// قراءة محتويات ملف services.json
$servicesFile = '../data/services.json';
if (!file_exists($servicesFile)) {
    http_response_code(404); // Not Found
    echo json_encode(['message' => 'ملف الخدمات غير موجود']);
    exit;
}
$services = json_decode(file_get_contents($servicesFile), true);

// البحث عن الخدمة المراد حذفها
$serviceIndex = -1;
foreach ($services as $index => $service) {
    if ($service['id'] === $id) {
        $serviceIndex = $index;
        break;
    }
}

if ($serviceIndex === -1) {
    http_response_code(404); // Not Found
    echo json_encode(['message' => 'الخدمة غير موجودة']);
    exit;
}

// حذف الصورة المرتبطة بالخدمة
$targetDir = "../data/service-images/";
$imageToDelete = $targetDir . $services[$serviceIndex]['image_name'];
if (file_exists($imageToDelete)) {
    unlink($imageToDelete);
}

// إزالة الخدمة من المصفوفة
array_splice($services, $serviceIndex, 1);

// حفظ التغييرات في ملف services.json
file_put_contents($servicesFile, json_encode($services, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['message' => 'تم حذف الخدمة بنجاح']);
?>