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
    echo json_encode(['message' => 'يرجى تحديد معرف المحتوى']);
    exit;
}

// جلب البيانات
$id = intval($_POST['id']);
$title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : null;
$description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : null;
$image = isset($_FILES['image']) ? $_FILES['image'] : null;

// قراءة محتويات ملف contents.json
$contentsFile = '../data/contents.json';
if (!file_exists($contentsFile)) {
    http_response_code(404); // Not Found
    echo json_encode(['message' => 'ملف المحتويات غير موجود']);
    exit;
}
$contents = json_decode(file_get_contents($contentsFile), true);

// البحث عن المحتوى المراد تعديله
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

// معالجة رفع الصورة الجديدة (اختياري)
if ($image) {
    $targetDir = "../data/content-images/";

    // التأكد من وجود المجلد وإلا قم بإنشائه
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));

    // التحقق مما إذا كان الملف المرفوع صورة بالفعل
    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        echo json_encode(['message' => 'الملف المرفوع ليس صورة.']);
        exit;
    }

    // تحقق من حجم الملف (مثال: 5 ميغابايت)
    if ($image["size"] > 5000000) {
        echo json_encode(['message' => 'حجم الصورة كبير جدًا.']);
        exit;
    }

    // السماح فقط بأنواع معينة من الصور
    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
        echo json_encode(['message' => "عذرًا، يُسمح فقط برفع ملفات JPG و JPEG و PNG و GIF."]);
        exit;
    }

    // حذف الصورة القديمة
    $oldImage = $targetDir . $contents[$contentIndex]['image_name'];
    if (file_exists($oldImage)) {
        unlink($oldImage);
    }

    // إنشاء اسم الصورة الجديدة
    $newImageName = $id . '.' . $imageFileType;
    $targetFile = $targetDir . $newImageName;

    // رفع الصورة الجديدة
    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
        $contents[$contentIndex]['image_name'] = $newImageName;
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'حدث خطأ أثناء رفع الصورة']);
        exit;
    }
}

// تحديث العنوان والوصف (اختياري)
if ($title !== null) {
    $contents[$contentIndex]['title'] = $title;
}
if ($description !== null) {
    $contents[$contentIndex]['description'] = $description;
}

// حفظ التغييرات في ملف contents.json
file_put_contents($contentsFile, json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['message' => 'تم تعديل المحتوى بنجاح']);
?>