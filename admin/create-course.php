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
  <title>انشاء كورس - OUT PUT للعلوم التقنية</title>
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
    <h2>انشاء كورس</h2>
    <form id="create-course-form">
      <input id="course-title" class="section-content" type="text" placeholder="عنوان الكورس" required>
      <textarea id="course-description" class="section-content" rows="8" cols="40" placeholder="وصف الكورس"></textarea>
      <input type="file" id="course-image" accept="image/*" onchange="previewImage()">
      <img id="image-preview" src="#" alt="معاينة الصورة">
      <button type="submit">انشاء الكورس</button>
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
      const file = document.getElementById('course-image').files[0];
      const reader = new FileReader();

      reader.onloadend = function() {
        preview.src = reader.result;
        preview.style.display = 'block';
      }

      if (file) {
        reader.readAsDataURL(file);
      } else {
        preview.src = "#";
        preview.style.display = 'none';
      }
    }

    // معالجة إرسال النموذج
    document.getElementById('create-course-form').addEventListener('submit', async (event) => {
      event.preventDefault();

      const title = document.getElementById('course-title').value.trim();
      const description = document.getElementById('course-description').value.trim();
      const imageInput = document.getElementById('course-image');
      const file = imageInput.files[0];

      if (!title || !description || !file) {
        alert('يرجى تعبئة جميع الحقول واختيار صورة.');
        return;
      }

      const formData = new FormData();
      formData.append('title', title);
      formData.append('description', description);
      formData.append('image', file);

      try {
        const response = await fetch('create_course.php', {
          method: 'POST',
          body: formData,
        });

        if (response.ok) {
          const data = await response.json();
          alert(`تم إنشاء الكورس بنجاح: ${data.message}`);
          // يمكنك إعادة تحميل الصفحة أو مسح محتويات النموذج بعد الإنشاء
          document.getElementById('create-course-form').reset();
          document.getElementById('image-preview').style.display = 'none';
        } else {
          alert(`خطأ أثناء إنشاء الكورس: ${response.statusText}`);
        }
      } catch (error) {
        console.error('حدث خطأ:', error);
        alert('فشل الاتصال بالخادم.');
      }
    });
  </script>
</body>
</html>