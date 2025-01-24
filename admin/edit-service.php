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
  <title>تعديل خدمة - OUT PUT للعلوم التقنية</title>
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
    <h2>تعديل خدمة</h2>
    <div id="error-container" class="error-message" style="display: none;"></div>
    <form id="edit-service-form" style="display: none;">
      <input id="service-title" class="section-content" type="text" placeholder="عنوان الخدمة" required>
      <textarea id="service-description" class="section-content" rows="8" cols="40" placeholder="وصف الخدمة"></textarea>
      <input type="file" id="service-image" accept="image/*" onchange="previewImage()">
      <img id="image-preview" src="#" alt="معاينة الصورة">
      <button type="submit">تعديل الخدمة</button>
    </form>
    <div id="buttons-container" class="action-buttons" style="display: none;">
      <a href="manage-services.html"><button>إدارة الخدمات</button></a>
      <a href="create-service.html"><button>إنشاء خدمة جديدة</button></a>
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
    // استخراج معرف الخدمة من رابط الصفحة
    const urlParams = new URLSearchParams(window.location.search);
    const serviceId = urlParams.get('service_id');

    // وظيفة لمعاينة الصورة قبل الرفع
    function previewImage() {
      const preview = document.getElementById('image-preview');
      const file = document.getElementById('service-image').files[0];
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

    // دالة لجلب بيانات الخدمة
    async function fetchServiceData() {
      if (!serviceId) {
        displayError('لم يتم تحديد معرف الخدمة.');
        return;
      }

      try {
        const response = await fetch('../data/services.json');
        if (!response.ok) {
          throw new Error('فشل في جلب بيانات الخدمة.');
        }
        const services = await response.json();
        const service = services.find(item => item.id === parseInt(serviceId));

        if (!service) {
          displayError('الخدمة غير موجودة.');
          return;
        }

        // ملء حقول النموذج ببيانات الخدمة
        document.getElementById('service-title').value = service.title;
        document.getElementById('service-description').value = service.description;
        document.getElementById('image-preview').src = `../data/service-images/${service.image_name}`;
        document.getElementById('image-preview').style.display = 'block';

        // إظهار النموذج
        document.getElementById('edit-service-form').style.display = 'flex';

      } catch (error) {
        console.error(error);
        displayError('حدث خطأ أثناء جلب بيانات الخدمة.');
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
    document.getElementById('edit-service-form').addEventListener('submit', async (event) => {
      event.preventDefault();

      const title = document.getElementById('service-title').value.trim();
      const description = document.getElementById('service-description').value.trim();
      const imageInput = document.getElementById('service-image');
      const file = imageInput.files[0];

      const formData = new FormData();
      formData.append('id', serviceId);
      if (title) formData.append('title', title);
      if (description) formData.append('description', description);
      if (file) formData.append('image', file);

      try {
        const response = await fetch('edit_service.php', {
          method: 'POST',
          body: formData,
        });

        if (response.ok) {
          const data = await response.json();
          alert(`تم تعديل الخدمة بنجاح: ${data.message}`);
          window.location.href = 'manage-services.php'; // الانتقال إلى صفحة إدارة الخدمات
        } else {
          alert(`خطأ أثناء تعديل الخدمة: ${response.statusText}`);
        }
      } catch (error) {
        console.error('حدث خطأ:', error);
        alert('فشل الاتصال بالخادم.');
      }
    });

    // استدعاء دالة جلب بيانات الخدمة عند تحميل الصفحة
    fetchServiceData();
  </script>
</body>
</html>