<?php
session_start(); // بدء جلسة PHP

// التحقق من إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // بيانات اعتماد تسجيل الدخول (يمكنك استبدالها ببيانات من قاعدة البيانات)
    $valid_username = 'admin';
    $valid_password = '1234';

    // التحقق من اسم المستخدم وكلمة المرور
    if ($username === $valid_username && $password === $valid_password) {
        // تعيين متغيرات الجلسة
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;

        // إعادة التوجيه إلى لوحة التحكم
        header('Location: dashboard.php');
        exit;
    } else {
        // رسالة خطأ
        $error_message = 'اسم المستخدم أو كلمة المرور غير صحيحة!';
    }
}

// التحقق من تسجيل الدخول
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // إعادة التوجيه إلى لوحة التحكم إذا تم تسجيل الدخول بالفعل
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول - OUT PUT للعلوم التقنية</title>
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
    .error-message {
      color: red;
      margin-bottom: 15px;
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
    <h2>تسجيل الدخول</h2>
    <?php if (isset($error_message)): ?>
      <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form id="login-form" method="POST" action="">
      <input class="section-content" type="text" name="username" placeholder="اسم المستخدم" required>
      <input class="section-content" type="password" name="password" placeholder="كلمة المرور" required>
      <button type="submit">تسجيل الدخول</button>
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