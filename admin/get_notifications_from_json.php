<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$notifications = [];
$json_files = [
    '../data/comments.json' => 'comment',
    '../data/requests.json' => 'request',
    '../data/volunteers.json' => 'volunteer',
    '../data/contacts.json' => 'contact',
    '../data/course_registrations.json' => 'course_registration'
];


foreach ($json_files as $file => $type) {
      if (!file_exists($file)) {
            continue;
    }
    $content = file_get_contents($file);
    if ($content === false) {
       continue;
    }
    $data = json_decode($content, true);
      if ($data !== null) {
         $notifications = array_merge($notifications, array_map(function($row) use ($type){
            $row['type'] = $type;
            return $row;
        }, $data));

      }

}
// ترتيب الإشعارات حسب التاريخ
usort($notifications, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

echo json_encode($notifications);
?>