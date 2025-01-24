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
  <title>تعديل محتوى - OUT PUT للعلوم التقنية</title>
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
    <h2>تعديل محتوى</h2>
    <div id="error-container" class="error-message" style="display: none;"></div>
    <form id="edit-content-form" style="display: none;">
      <input id="content-title" class="section-content" type="text" placeholder="عنوان المحتوى" required>
      <textarea id="content-description" class="section-content" rows="8" cols="40" placeholder="وصف المحتوى"></textarea>
      <input type="file" id="content-image" accept="image/*" onchange="previewImage()">
      <img id="image-preview" src="#" alt="معاينة الصورة">
      <button type="submit">تعديل المحتوى</button>
    </form>
    <div id="buttons-container" class="action-buttons" style="display: none;">
      <a href="manage-content.html"><button>إدارة المحتوى</button></a>
      <a href="create-content.html"><button>إنشاء محتوى جديد</button></a>
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
    // استخراج معرف المحتوى من رابط الصفحة
    const urlParams = new URLSearchParams(window.location.search);
    const contentId = urlParams.get('content_id');

    // وظيفة لمعاينة الصورة قبل الرفع
    function previewImage() {
      const preview = document.getElementById('image-preview');
      const file = document.getElementById('content-image').files[0];
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

    // دالة لجلب بيانات المحتوى
    async function fetchContentData() {
      if (!contentId) {
        displayError('لم يتم تحديد معرف المحتوى.');
        return;
      }

      try {
        const response = await fetch('../data/contents.json');
        if (!response.ok) {
          throw new Error('فشل في جلب بيانات المحتوى.');
        }
        const contents = await response.json();
        const content = contents.find(item => item.id === parseInt(contentId));

        if (!content) {
          displayError('المحتوى غير موجود.');
          return;
        }

        // ملء حقول النموذج ببيانات المحتوى
        document.getElementById('content-title').value = content.title;
        document.getElementById('content-description').value = content.description;
        document.getElementById('image-preview').src = `../data/content-images/${content.image_name}`;
        document.getElementById('image-preview').style.display = 'block';

        // إظهار النموذج
        document.getElementById('edit-content-form').style.display = 'flex';

      } catch (error) {
        console.error(error);
        displayError('حدث خطأ أثناء جلب بيانات المحتوى.');
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
    document.getElementById('edit-content-form').addEventListener('submit', async (event) => {
      event.preventDefault();

      const title = document.getElementById('content-title').value.trim();
      const description = document.getElementById('content-description').value.trim();
      const imageInput = document.getElementById('content-image');
      const file = imageInput.files[0];

      const formData = new FormData();
      formData.append('id', contentId);
      if (title) formData.append('title', title);
      if (description) formData.append('description', description);
      if (file) formData.append('image', file);

      try {
        const response = await fetch('edit_content.php', {
          method: 'POST',
          body: formData,
        });

        if (response.ok) {
          const data = await response.json();
          alert(`تم تعديل المحتوى بنجاح: ${data.message}`);
          window.location.href = 'manage-content.php'; // الانتقال إلى صفحة إدارة المحتوى
        } else {
          alert(`خطأ أثناء تعديل المحتوى: ${response.statusText}`);
        }
      } catch (error) {
        console.error('حدث خطأ:', error);
        alert('فشل الاتصال بالخادم.');
      }
    });

    // استدعاء دالة جلب بيانات المحتوى عند تحميل الصفحة
    fetchContentData();
  </script>
</body>
</html>