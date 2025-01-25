<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}


$db_files = [
    '../database/comments.db',
    '../database/requests.db',
    '../database/volunteers.db',
    '../database/contacts.db',
    '../database/courses_registrations.db'
];

function check_db_has_data($db_file) {
      if (!file_exists($db_file)) {
           return false;
      }
  try {
        $db = new PDO('sqlite:' . $db_file);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->query("SELECT 1 FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
         if (empty($tables)){
            return false;

          }
        foreach ($tables as $table) {
            $stmt = $db->query("SELECT 1 FROM {$table} LIMIT 1");
             if ($stmt && $stmt->fetchColumn() !== false){
               return true;
            }
        }

        return false;

    } catch (PDOException $e) {
        return false;
    }
}

$has_notifications = false;

foreach ($db_files as $file) {
    if (check_db_has_data($file)) {
        $has_notifications = true;
        break; // إيقاف الحلقة إذا تم العثور على إشعار
    }
}

echo json_encode($has_notifications);
?>