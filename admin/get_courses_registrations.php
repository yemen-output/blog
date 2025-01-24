<?php
header('Content-Type: application/json');

// التحقق من نوع الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Invalid request method. Use POST.']));
}

$db_file = '../database/courses_registrations.db';

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

try {
    $stmt = $db->prepare("SELECT * FROM courses_registrations ORDER BY created_at DESC");
    $stmt->execute();
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($registrations);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error fetching course registrations: ' . $e->getMessage()]));
}
?>