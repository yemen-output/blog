<?php
session_start();

// التحقق من تسجيل الدخول
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // تم تسجيل الدخول بنجاح
    http_response_code(200); // OK
    exit; // لا تفعل شيئًا آخر
} else {
    // تسجيل الدخول غير صحيح
    http_response_code(302); // Found (Redirection)
    header('Location: ../index.html'); // إعادة التوجيه إلى الصفحة الرئيسية
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إحصائيات الموقع - OUT PUT للعلوم التقنية </title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="../main.css">
  <style>
    .statistics {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .statistic-item {
      background-color: var(--white);
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 20px;
      text-wrap: nowrap;
      text-align: center;
      flex-grow: 1;
      flex-basis: 0;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .statistic-item:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .statistic-item h3 {
      color: var(--primary-color);
      font-size: 1.5em;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <h1>إحصائيات الموقع</h1>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">العودة للرئيسية</a></li>
        <li><a href="logout.php">تسجيل الخروج</a></li>
      </ul>
    </nav>
  </header>

  <section id="statistics">
    <h2>إحصائيات الموقع</h2>
    <div class="statistics">
      <div class="statistic-item section-content">
        <h3>عدد الزوار</h3>
        <p>
          1245
          <!-- fetch <php> -->
        </p>
      </div>
      <div class="statistic-item section-content">
        <h3>عدد تسجيل الدورات</h3>
        <p>
          576
          <!-- fetch <php> -->
        </p>
      </div>
      <div class="statistic-item section-content">
        <h3>عدد المتطوعين</h3>
        <p>
          55
          <!-- fetch <php> -->
        </p>
      </div>
      <div class="statistic-item section-content">
        <h3>عدد طلب الخدمات</h3>
        <p>
          193
          <!-- fetch <php> -->
        </p>
      </div>
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
</body>
</html>