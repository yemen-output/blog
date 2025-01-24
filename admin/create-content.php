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
  <title>انشاء محتوى - OUT PUT للعلوم التقنية</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="../main.css">
  <style>
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    input, textarea {
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
    #image-preview {
      max-width: 200px;
      max-height: 200px;
      margin-bottom: 10px;
      display: none; /* إخفاء الصورة افتراضيًا */
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
    <h2>انشاء محتوى</h2>
    <form id="create-content-form">
      <input id="content-title" class="section-content" type="text" placeholder="عنوان المحتوى" required>
      <textarea id="content-description" class="section-content" rows="8" cols="40" placeholder="وصف المحتوى"></textarea>
      <input type="file" id="content-image" accept="image/*" onchange="previewImage()">
      <img id="image-preview" src="#" alt="معاينة الصورة"> <!-- عنصر معاينة الصورة -->
      <button type="submit">انشاء المحتوى</button>
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
  <script>
    // وظيفة لمعاينة الصورة قبل الرفع
    function previewImage() {
      const preview = document.getElementById('image-preview');
      const file = document.getElementById('content-image').files[0];
      const reader = new FileReader();

      reader.onloadend = function() {
        preview.src = reader.result;
        preview.style.display = 'block'; // عرض الصورة بعد التحميل
      }

      if (file) {
        reader.readAsDataURL(file);
      } else {
        preview.src = "#";
        preview.style.display = 'none'; // إخفاء الصورة إذا لم يتم اختيار ملف
      }
    }

    // معالجة إرسال النموذج
    document.getElementById('create-content-form').addEventListener('submit', async (event) => {
      event.preventDefault(); // منع إعادة تحميل الصفحة

      // الحصول على القيم من النموذج
      const title = document.getElementById('content-title').value.trim();
      const description = document.getElementById('content-description').value.trim();
      const imageInput = document.getElementById('content-image');
      const file = imageInput.files[0];

      // تحقق من وجود بيانات
      if (!title || !description || !file) {
        alert('يرجى تعبئة جميع الحقول واختيار صورة.');
        return;
      }

      // إنشاء FormData لإرسال البيانات
      const formData = new FormData();
      formData.append('title', title);
      formData.append('description', description);
      formData.append('image', file);

      try {
        // إرسال الطلب إلى API
        const response = await fetch('create_content.php', { // استخدام مسار PHP المحدث
          method: 'POST',
          body: formData,
        });

        if (response.ok) {
          const data = await response.json();
          alert(`تم إنشاء المحتوى بنجاح: ${data.message}`);
        } else {
          alert(`خطأ أثناء رفع المحتوى: ${response.statusText}`);
        }
      } catch (error) {
        console.error('حدث خطأ:', error);
        alert('فشل الاتصال بالخادم. تأكد من أن مسار الملف PHP صحيح.');
      }
    });
  </script>
</body>
</html>