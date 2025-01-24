<?php
header('Content-Type: application/json');

// التحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'طلب غير مسموح']);
    exit;
}

// التحقق من البيانات المستلمة
if (!isset($_POST['title'], $_POST['description'], $_FILES['image'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'يرجى تعبئة جميع الحقول']);
    exit;
}

// جلب البيانات
$title = htmlspecialchars($_POST['title']);
$description = htmlspecialchars($_POST['description']);
$image = $_FILES['image'];

// معالجة رفع الصورة
$targetDir = "../data/service-images/";

// التأكد من وجود المجلد وإلا قم بإنشائه
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));

// التحقق من الصورة
$check = getimagesize($image["tmp_name"]);
if ($check === false) {
    echo json_encode(['message' => 'الملف المرفوع ليس صورة.']);
    exit;
}

if ($image["size"] > 5000000) {
    echo json_encode(['message' => 'حجم الصورة كبير جدًا.']);
    exit;
}

if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
    echo json_encode(['message' => "عذرًا، يُسمح فقط برفع ملفات JPG و JPEG و PNG و GIF."]);
    exit;
}

// قراءة محتويات ملف services.json
$servicesFile = '../data/services.json';
$services = [];
if (file_exists($servicesFile)) {
    $services = json_decode(file_get_contents($servicesFile), true);
}

// توليد id للخدمة الجديدة
if (empty($services)) {
    $newId = 0;
} else {
    $existingIds = array_column($services, 'id');
    $newId = max($existingIds) + 1;

    while (in_array($newId, $existingIds)) {
        $newId++;
    }
}

// إنشاء اسم الصورة الجديدة
$newImageName = $newId . '.' . $imageFileType;
$targetFile = $targetDir . $newImageName;

// رفع الصورة
if (move_uploaded_file($image["tmp_name"], $targetFile)) {
    // إضافة الخدمة الجديدة إلى مصفوفة الخدمات مع تاريخ الإنشاء
    $newService = [
        'id' => $newId,
        'title' => $title,
        'description' => $description,
        'image_name' => $newImageName,
        'date' => date('Y-m-d') // استخدام صيغة YYYY-MM-DD
    ];
    $services[] = $newService;

    // حفظ التغييرات في ملف services.json
    file_put_contents($servicesFile, json_encode($services, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode(['message' => 'تم إنشاء الخدمة بنجاح']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'حدث خطأ أثناء رفع الصورة']);
}
?>