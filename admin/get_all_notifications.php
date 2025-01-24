<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$notifications = [];

// دالة لجلب البيانات من جدول
function fetchData($db_file, $query, $type) {
    $db_file = "../" . $db_file; // إضافة "../" للمسار
    if (!file_exists($db_file)) {
        return []; // إرجاع مصفوفة فارغة في حالة عدم وجود الملف
    }

    try {
        $db = new PDO('sqlite:' . $db_file);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($row) use ($type){
            $row['type'] = $type;
            return $row;
        }, $results);
    } catch (PDOException $e) {
        return []; // إرجاع مصفوفة فارغة في حالة حدوث خطأ في قاعدة البيانات
    }
}

// جلب التعليقات
$comments = fetchData('database/comments.db', "SELECT * FROM comments ORDER BY created_at DESC", 'comment');
if (!empty($comments)) {
   $notifications = array_merge($notifications, $comments);
}

// جلب طلبات الخدمات
$requests = fetchData('database/requests.db', "SELECT * FROM requests ORDER BY created_at DESC", 'request');
if (!empty($requests)) {
   $notifications = array_merge($notifications, $requests);
}


// جلب طلبات التطوع
$volunteers = fetchData('database/volunteers.db', "SELECT * FROM volunteers ORDER BY created_at DESC", 'volunteer');
if (!empty($volunteers)) {
   $notifications = array_merge($notifications, $volunteers);
}


// جلب رسائل التواصل
$contacts = fetchData('database/contacts.db', "SELECT * FROM contacts ORDER BY created_at DESC", 'contact');
if (!empty($contacts)) {
   $notifications = array_merge($notifications, $contacts);
}


// جلب تسجيلات الدورات
$courses_registrations = fetchData('database/courses_registrations.db', "SELECT * FROM courses_registrations ORDER BY created_at DESC", 'course_registration');
if (!empty($courses_registrations)) {
   $notifications = array_merge($notifications, $courses_registrations);
}


echo json_encode($notifications);
?>