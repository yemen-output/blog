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
    echo json_encode(['message' => 'يرجى تحديد معرف الكورس']);
    exit;
}

// جلب البيانات
$id = intval($_POST['id']);
$title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : null;
$description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : null;
$image = isset($_FILES['image']) ? $_FILES['image'] : null;

// قراءة محتويات ملف courses.json
$coursesFile = '../data/courses.json';
if (!file_exists($coursesFile)) {
    http_response_code(404); // Not Found
    echo json_encode(['message' => 'ملف الكورسات غير موجود']);
    exit;
}
$courses = json_decode(file_get_contents($coursesFile), true);

// البحث عن الكورس المراد تعديله
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

// معالجة رفع الصورة الجديدة (اختياري)
if ($image) {
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

    // حذف الصورة القديمة
    $oldImage = $targetDir . $courses[$courseIndex]['image_name'];
    if (file_exists($oldImage)) {
        unlink($oldImage);
    }

    // إنشاء اسم الصورة الجديدة
    $newImageName = $id . '.' . $imageFileType;
    $targetFile = $targetDir . $newImageName;

    // رفع الصورة الجديدة
    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
        $courses[$courseIndex]['image_name'] = $newImageName;
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'حدث خطأ أثناء رفع الصورة']);
        exit;
    }
}

// تحديث العنوان والوصف (اختياري)
if ($title !== null) {
    $courses[$courseIndex]['title'] = $title;
}
if ($description !== null) {
    $courses[$courseIndex]['description'] = $description;
}

// حفظ التغييرات في ملف courses.json
file_put_contents($coursesFile, json_encode($courses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['message' => 'تم تعديل الكورس بنجاح']);
?>