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
  <title>تعديل كورس - OUT PUT للعلوم التقنية</title>
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
      display: none;
    }
    .error-message {
      color: red;
      font-size: 1.2em;
      margin-bottom: 20px;
    }
    .action-buttons {
      display: flex;
      gap: 10px;
      margin-top: 20px;
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
    <h2>تعديل كورس</h2>
    <div id="error-container" class="error-message" style="display: none;"></div>
    <form id="edit-course-form" style="display: none;">
      <input id="course-title" class="section-content" type="text" placeholder="عنوان الكورس" required>
      <textarea id="course-description" class="section-content" rows="8" cols="40" placeholder="وصف الكورس"></textarea>
      <input type="file" id="course-image" accept="image/*" onchange="previewImage()">
      <img id="image-preview" src="#" alt="معاينة الصورة">
      <button type="submit">تعديل الكورس</button>
    </form>
    <div id="buttons-container" class="action-buttons" style="display: none;">
      <a href="manage-courses.html"><button>إدارة الكورسات</button></a>
      <a href="create-course.html"><button>إنشاء كورس جديد</button></a>
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
    // استخراج معرف الكورس من رابط الصفحة
    const urlParams = new URLSearchParams(window.location.search);
    const courseId = urlParams.get('course_id');

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

    // دالة لجلب بيانات الكورس
    async function fetchCourseData() {
      if (!courseId) {
        displayError('لم يتم تحديد معرف الكورس.');
        return;
      }

      try {
        const response = await fetch('../data/courses.json');
        if (!response.ok) {
          throw new Error('فشل في جلب بيانات الكورس.');
        }
        const courses = await response.json();
        const course = courses.find(item => item.id === parseInt(courseId));

        if (!course) {
          displayError('الكورس غير موجود.');
          return;
        }

        // ملء حقول النموذج ببيانات الكورس
        document.getElementById('course-title').value = course.title;
        document.getElementById('course-description').value = course.description;
        document.getElementById('image-preview').src = `../data/course-images/${course.image_name}`;
        document.getElementById('image-preview').style.display = 'block';

        // إظهار النموذج
        document.getElementById('edit-course-form').style.display = 'flex';

      } catch (error) {
        console.error(error);
        displayError('حدث خطأ أثناء جلب بيانات الكورس.');
      }
    }

    // دالة لعرض رسالة الخطأ
    function displayError(message) {
      const errorContainer = document.getElementById('error-container');
      errorContainer.textContent = message;
      errorContainer.style.display = 'block';
      document.getElementById('buttons-container').style.display = 'flex';
    }

    // معالجة إرسال النموذج
    document.getElementById('edit-course-form').addEventListener('submit', async (event) => {
      event.preventDefault();

      const title = document.getElementById('course-title').value.trim();
      const description = document.getElementById('course-description').value.trim();
      const imageInput = document.getElementById('course-image');
      const file = imageInput.files[0];

      const formData = new FormData();
      formData.append('id', courseId);
      if (title) formData.append('title', title);
      if (description) formData.append('description', description);
      if (file) formData.append('image', file);

      try {
        const response = await fetch('edit_course.php', {
          method: 'POST',
          body: formData,
        });

        if (response.ok) {
          const data = await response.json();
          alert(`تم تعديل الكورس بنجاح: ${data.message}`);
          window.location.href = 'manage-courses.php'; // الانتقال إلى صفحة إدارة الكورسات
        } else {
          alert(`خطأ أثناء تعديل الكورس: ${response.statusText}`);
        }
      } catch (error) {
        console.error('حدث خطأ:', error);
        alert('فشل الاتصال بالخادم.');
      }
    });

    // استدعاء دالة جلب بيانات الكورس عند تحميل الصفحة
    fetchCourseData();
  </script>
</body>
</html>