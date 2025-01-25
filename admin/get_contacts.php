<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$db_file = '../database/contacts.db';
$json_file = '../data/contacts.json';
$data_type = $_POST['data_type'] ?? 'all';


$contacts = [];
// دالة لجلب البيانات من قاعدة البيانات
function fetchDataFromDB($db) {
      try {
          $stmt = $db->prepare("SELECT * FROM contacts ORDER BY created_at DESC");
          $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
             return [];

        }

}
// دالة لجلب البيانات من ملف JSON
function fetchDataFromJSON($jsonFile) {
    $contacts = [];
     if (file_exists($jsonFile)) {
         $json_content = file_get_contents($jsonFile);
        if ($json_content !== false) {
            $contacts = json_decode($json_content, true) ?: [];
        }
    }
    usort($contacts, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    return $contacts;

}
try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   if ($data_type === 'new' || $data_type === 'all') {
        $contacts = array_merge($contacts, fetchDataFromDB($db));
    }
     if ($data_type === 'old' || $data_type === 'all') {
        $contacts = array_merge($contacts, fetchDataFromJSON($json_file));
    }
     usort($contacts, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    echo json_encode($contacts);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}
?>