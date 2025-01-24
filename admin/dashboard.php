<?php
session_start();

// التحقق من تسجيل الدخول
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // تم تسجيل الدخول بنجاح
    http_response_code(200); // OK
} else {
    // تسجيل الدخول غير صحيح
    http_response_code(302); // Found (Redirection)
    header('Location: ../index.html'); // إعادة التوجيه إلى الصفحة الرئيسية
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة تحكم المطور - OUT PUT للعلوم التقنية </title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="../main.css">
  <style>
    .admin-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .admin-actions button {
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 15px 30px;
      border-radius: 8px;
      font-size: 1.1em;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .admin-actions button:hover {
      background-color: var(--secondary-color);
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <h1>لوحة تحكم المطور</h1>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">العودة للرئيسية</a></li>
        <li><a href="logout.php">تسجيل الخروج</a></li>
      </ul>
    </nav>
  </header>

  <section id="dashboard">
    <h2>إدارة الموقع</h2>
    <div class="admin-actions">
      <button onclick="notifications()">الاشعارات</button>
      <button onclick="manageUsers()">إدارة المستخدمين</button>
      <button onclick="manageCourses()">إدارة الدورات</button>
      <button onclick="manageVolunteers()">إدارة المتطوعين</button>
      <button onclick="manageContent()">إدارة المحتوى</button>
      <button onclick="manageServices()">إدارة الخدمات</button>
      <button onclick="viewStatistics()">عرض الإحصائيات</button>
      <button onclick="siteSettings()">إعدادات الموقع</button>
    </div>
  </section>

  <footer>
    <p>© 2023 OUT PUT للعلوم التقنية. جميع الحقوق محفوظة.</p>
  </footer>

  <button class="theme-switcher" onclick="toggleTheme()">
    <i class="fas fa-moon"></i>
  </button>
  
  <script src="../script.js"></script>
  <script>
    // وظائف لوحة التحكم
    function notifications() {
      alert('سيتم فتح صفحة الاشعارات .');
      window.location.href = "notifications.php";
    }
    function manageUsers() {
      alert('سيتم فتح صفحة إدارة المستخدمين.');
      window.location.href = "manage-users.php";
    }

    function manageCourses() {
      alert('سيتم فتح صفحة إدارة الدورات.');
      window.location.href = "manage-courses.php";
    }

    function manageVolunteers() {
      alert('سيتم فتح صفحة إدارة المتطوعين.');
      window.location.href = "manage-volunteers.php";
    }
    
    function manageServices() {
      alert('سيتم فتح صفحة إدارة الخدمات.');

      window.location.href = "manage-services.php";
    }

    function manageContent() {
      alert('سيتم فتح صفحة إدارة المحتوى.');
      window.location.href = "manage-content.php";
    }

    function viewStatistics() {
      alert('سيتم فتح صفحة الإحصائيات.');
      window.location.href = "statistics.php";
    }

    function siteSettings() {
      alert('سيتم فتح صفحة إعدادات الموقع.');
      window.location.href = "settings.php";
    }
  </script>
</body>
</html>