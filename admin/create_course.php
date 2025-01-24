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
$targetDir = "../data/course-images/";

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

// قراءة محتويات ملف courses.json
$coursesFile = '../data/courses.json';
$courses = [];
if (file_exists($coursesFile)) {
    $courses = json_decode(file_get_contents($coursesFile), true);
}

// توليد id للكورس الجديد
if (empty($courses)) {
    $newId = 0;
} else {
    $existingIds = array_column($courses, 'id');
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
    // إضافة الكورس الجديد إلى مصفوفة الكورسات مع تاريخ الإنشاء
    $newCourse = [
        'id' => $newId,
        'title' => $title,
        'description' => $description,
        'image_name' => $newImageName,
        'date' => date('Y-m-d H:i:s')
    ];
    $courses[] = $newCourse;

    // حفظ التغييرات في ملف courses.json
    file_put_contents($coursesFile, json_encode($courses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode(['message' => 'تم إنشاء الكورس بنجاح']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'حدث خطأ أثناء رفع الصورة']);
}
?>