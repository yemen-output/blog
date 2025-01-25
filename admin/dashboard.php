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
      position: relative;
      /* لجعل العلامة absolute بالنسبة للزر */

    }
    .admin-actions button:hover {
      background-color: var(--secondary-color);
    }
    .notification-indicator {
      position: absolute;
      top: 5px;
      left: 5px;
      width: 10px;
      height: 10px;
      background-color: red;
      border-radius: 50%;
      display: none;

    }
    .notification-indicator.active {
      display: block;
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
      <button onclick="notifications()" id="notification-button">
        الاشعارات
        <!--<span class="notification-indicator" id="notification-indicator"></span>-->
      </button>
      <button onclick="manageUsers()">إدارة المستخدمين</button>
      <button onclick="manageCoursesRegistrations()">إدارة تسجيل الدورات</button>
      <button onclick="manageVolunteers()">إدارة المتطوعين</button>
      <button onclick="manageServicesRequests()">إدارة طلبات الخدمات</button>

      <button onclick="manageContent()">إدارة المحتوى</button>
      <button onclick="viewStatistics()">عرض الإحصائيات</button>
      <button onclick="siteSettings()">إعدادات الموقع</button>
    </div>
  </section>

  <footer>
    <p>
      © 2023 OUT PUT للعلوم التقنية. جميع الحقوق محفوظة.
    </p>
  </footer>

  <button class="theme-switcher" onclick="toggleTheme()">
    <i class="fas fa-moon"></i>
  </button>

  <script src="../script.js"></script>
  <script>
    /*async function checkForNotifications() {
      try {
        const data = await fetch('check_for_notifications.php', {
          method: "POST"
        });
        console.log(data);
        return data.json();

      } catch (error) {
        console.error('Error checking for notifications:', error);
      }
    }*/

    // وظائف لوحة التحكم
    function notifications() {
      window.location.href = "notifications.php";
    }
    function manageUsers() {
      window.location.href = "manage-users.php";
    }

    function manageCoursesRegistrations() {
      window.location.href = "manage-courses-registrations.php";
    }
    function manageVolunteers() {
      window.location.href = "manage-volunteers.php";
    }
    function manageServicesRequests() {
      window.location.href = "manage-service-requests.php";

    }
    function manageContent() {
      window.location.href = "manage-content.php";
    }

    function viewStatistics() {
      window.location.href = "statistics.php";
    }

    function siteSettings() {
      window.location.href = "settings.php";
    }

    /*function updateNotificationIndicator(hasNotifications) {
      /*const notificationIndicator = document.getElementById('notification-indicator');
      if (hasNotifications === true) {
        notificationIndicator.classList.add('active');
      } else {
        notificationIndicator.classList.remove('active');
      }
    }

    checkForNotifications().then(updateNotificationIndicator);*/

  </script>
</body>
</html>