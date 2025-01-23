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

// قراءة محتويات ملف contents.json
$contentsFile = '../data/contents.json';
$contents = [];
if (file_exists($contentsFile)) {
    $contents = json_decode(file_get_contents($contentsFile), true);
}

// توليد id للمحتوى الجديد
if (empty($contents)) {
    $newId = 0; // أول محتوى يبدأ من 0
} else {
    $existingIds = array_column($contents, 'id');
    $newId = max($existingIds) + 1; // أعلى id + 1

    // التأكد من عدم تكرار الـ id
    while (in_array($newId, $existingIds)) {
        $newId++;
    }
}

// إنشاء اسم الصورة الجديدة
$newImageName = $newId . '.' . $imageFileType;
$targetFile = $targetDir . $newImageName;

// رفع الصورة
if (move_uploaded_file($image["tmp_name"], $targetFile)) {
    // إضافة المحتوى الجديد إلى مصفوفة المحتويات
    $newContent = [
        'id' => $newId,
        'title' => $title,
        'description' => $description,
        'image_name' => $newImageName
    ];
    $contents[] = $newContent;

    // حفظ التغييرات في ملف contents.json
    file_put_contents($contentsFile, json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode(['message' => 'تم إنشاء المحتوى بنجاح']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'حدث خطأ أثناء رفع الصورة']);
}
?>