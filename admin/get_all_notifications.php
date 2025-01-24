<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}


$notifications = [];

// دالة لجلب البيانات من جدول
function fetchData($db_file, $query, $type) {
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
       return  ['error' => 'Database error: ' . $e->getMessage(), 'type'=>$type];
    }
}

// جلب التعليقات
$comments = fetchData('../database/comments.db', "SELECT * FROM comments ORDER BY created_at DESC", 'comment');
if (isset($comments['error'])) {
     $notifications[] = $comments;
}else{
   $notifications = array_merge($notifications, $comments);
}

// جلب طلبات الخدمات
$requests = fetchData('../database/requests.db', "SELECT * FROM requests ORDER BY created_at DESC", 'request');
if (isset($requests['error'])) {
     $notifications[] = $requests;
}else{
   $notifications = array_merge($notifications, $requests);
}

// جلب طلبات التطوع
$volunteers = fetchData('../database/volunteers.db', "SELECT * FROM volunteers ORDER BY created_at DESC", 'volunteer');
if (isset($volunteers['error'])) {
     $notifications[] = $volunteers;
}else{
   $notifications = array_merge($notifications, $volunteers);
}


// جلب رسائل التواصل
$contacts = fetchData('../database/contacts.db', "SELECT * FROM contacts ORDER BY created_at DESC", 'contact');
if (isset($contacts['error'])) {
     $notifications[] = $contacts;
}else{
   $notifications = array_merge($notifications, $contacts);
}


// جلب تسجيلات الدورات
$courses_registrations = fetchData('../database/courses_registrations.db', "SELECT * FROM courses_registrations ORDER BY created_at DESC", 'course_registration');
if (isset($courses_registrations['error'])) {
     $notifications[] = $courses_registrations;
}else{
   $notifications = array_merge($notifications, $courses_registrations);
}


echo json_encode($notifications);
?>