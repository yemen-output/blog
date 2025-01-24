<?php
header('Content-Type: application/json');

// التحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'طلب غير مسموح']);
    exit;
}

// التحقق من البيانات المستلمة
if (!isset($_POST['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'يرجى تحديد معرف الخدمة']);
    exit;
}

// جلب البيانات
$id = intval($_POST['id']);
$title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : null;
$description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : null;
$image = isset($_FILES['image']) ? $_FILES['image'] : null;

// قراءة محتويات ملف services.json
$servicesFile = '../data/services.json';
if (!file_exists($servicesFile)) {
    http_response_code(404); // Not Found
    echo json_encode(['message' => 'ملف الخدمات غير موجود']);
    exit;
}
$services = json_decode(file_get_contents($servicesFile), true);

// البحث عن الخدمة المراد تعديلها
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

// معالجة رفع الصورة الجديدة (اختياري)
if ($image) {
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

    // حذف الصورة القديمة
    $oldImage = $targetDir . $services[$serviceIndex]['image_name'];
    if (file_exists($oldImage)) {
        unlink($oldImage);
    }

    // إنشاء اسم الصورة الجديدة
    $newImageName = $id . '.' . $imageFileType;
    $targetFile = $targetDir . $newImageName;

    // رفع الصورة الجديدة
    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
        $services[$serviceIndex]['image_name'] = $newImageName;
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'حدث خطأ أثناء رفع الصورة']);
        exit;
    }
}

// تحديث العنوان والوصف (اختياري)
if ($title !== null) {
    $services[$serviceIndex]['title'] = $title;
}
if ($description !== null) {
    $services[$serviceIndex]['description'] = $description;
}

// تحديث التاريخ
$services[$serviceIndex]['date'] = date('Y-m-d');

// حفظ التغييرات في ملف services.json
file_put_contents($servicesFile, json_encode($services, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['message' => 'تم تعديل الخدمة بنجاح']);
?>