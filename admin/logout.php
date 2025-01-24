<?php
session_start();

// التحقق من إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // إزالة جميع متغيرات الجلسة
    session_unset();

    // تدمير الجلسة
    session_destroy();

    // إعادة التوجيه إلى الصفحة الرئيسية
    header('Location: ../index.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الخروج - OUT PUT للعلوم التقنية</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="../main.css">
  <style>
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    input {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 1em;
    }
    button {
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 10px;
      border-radius: 8px;
      font-size: 1.1em;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    button:hover {
      background-color: var(--secondary-color);
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <h1>لوحة التحكم</h1>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">العودة للرئيسية</a></li>
      </ul>
    </nav>
  </header>
  <section>
    <h2>تسجيل الخروج</h2>
    <form id="logout-form" method="POST" action="">
      <p>
        هل أنت متأكد من أنك تريد تسجيل الخروج؟
      </p>
      <button type="submit">تأكيد</button>
    </form>
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