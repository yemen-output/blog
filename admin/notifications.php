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
  <title>الإشعارات - OUT PUT للعلوم التقنية</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="../main.css">
  <style>
    .notification-item {
      background-color: #f9f9f9;
      border: 1px solid #ddd;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 8px;
    }
    body.dark-mode .notification-item {
      background-color: #343434;
      color: var(--dark-mode-text)
    }
    .notification-item.error {
      background-color: #ffe6e6;
      border: 1px solid #ffcccc;
    }
    body.dark-mode .notification-item.error {
      background-color: #402929;
      border: 1px solid #7e4949;
      color: var(--dark-mode-text)
    }
    .notification-item h3 {
      margin-bottom: 10px;
    }
    button {
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 10px;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 10px
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

  <section id="notifications">
    <h2>الإشعارات</h2>
    <div id="notifications-container">
      <!-- سيتم إضافة الإشعارات هنا باستخدام JavaScript -->
    </div>
    <!--<button id="load-old-notifications">فتح الإشعارات القديمة</button>-->
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
    async function exportData(type) {
      try {
        // استدعاء ملف الحفظ
        const exportResponse = await fetch('export_to_json.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'type=' + type,
        });

        if (!exportResponse.ok) {
          throw new Error('Error exporting data to JSON.');
        }

        const exportData = await exportResponse.json();
        console.log(exportData.message);

        // استدعاء ملف الحذف بعد اكتمال عملية الحفظ
        const deleteResponse = await fetch('delete_from_db.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'type=' + type,
        });

        if (!deleteResponse.ok) {
          throw new Error('Error deleting data from database.');
        }

        const deleteData = await deleteResponse.json();
        console.log(deleteData.message);

      } catch (error) {
        console.error(error);
      }
    }

    // جلب جميع الإشعارات
    async function fetchNotifications(data_type = 'new') {
      try {
        const formData = new FormData();
        formData.append('data_type', data_type);
        const response = await fetch('get_notifications_from_db.php', {
          method: 'POST',
          body: formData
        });
        if (!response.ok) {
          throw new Error('فشل في جلب الإشعارات.');
        }
        const notifications = await response.json();
        displayNotifications(notifications);
        /*await exportData('comments');
        await exportData('requests');
        await exportData('volunteers');
        await exportData('contacts');
        await exportData('courses');*/
      } catch (error) {
        console.error(error);
        alert('حدث خطأ أثناء جلب الإشعارات.');
      }
    }
    // جلب جميع الإشعارات القديمة
    async function fetchOldNotifications() {
      try {
        const formData = new FormData();
        const response = await fetch('get_notifications_from_json.php', {
          method: 'POST',
          body: formData
        });
        if (!response.ok) {
          throw new Error('فشل في جلب الإشعارات القديمة.');
        }
        const oldNotifications = await response.json();
        displayNotifications(oldNotifications, true);

      } catch (error) {
        console.error(error);
        alert('حدث خطأ أثناء جلب الإشعارات القديمة.');

      }

    }
    // عرض الإشعارات
    function displayNotifications(notifications, append = false) {
      const notificationsContainer = document.getElementById('notifications-container');
      if (!append) {
        notificationsContainer.innerHTML = '';
      }
      if (notifications.length == 0) {
        notificationsContainer.innerHTML += '<p>لا توجد إشعارات</p>';
      }
      notifications.forEach(notification => {
        const notificationItem = document.createElement('div');
        notificationItem.classList.add('notification-item')
        if (notification.error) {} else {
          let content = '';
          if (notification.type === 'comment') {
            content = `<h3>تعليق جديد</h3><p><strong>التعليق:</strong> ${notification.comment}</p>
            <p><strong>معرّف المنشور:</strong> ${notification.post_id}</p>
            <p><strong>تاريخ الإنشاء:</strong> ${notification.created_at}</p>
            `;
          } else if (notification.type === 'request') {
            content = `<h3>طلب خدمة جديد</h3>
            <p><strong>الاسم:</strong> ${notification.name}</p>
            <p><strong>رقم الهاتف:</strong> ${notification.phone}</p>
            <p><strong>معرّف الخدمة:</strong> ${notification.service_id}</p>
            <p><strong>التفاصيل:</strong> ${notification.details}</p>
            <p><strong>تاريخ الإنشاء:</strong> ${notification.created_at}</p>
            `;

          } else if (notification.type === 'volunteer') {
            content = `<h3>طلب تطوع جديد</h3>
            <p><strong>الاسم:</strong> ${notification.full_name}</p>
            <p><strong>المحافظة:</strong> ${notification.governorate}</p>
            <p><strong>التخصص:</strong> ${notification.specialization}</p>
            <p><strong>السنة الدراسية:</strong> ${notification.academic_year}</p>
            <p><strong>مجال التطوع:</strong> ${notification.volunteer_area}</p>
            <p><strong>ماذا يقدم للفريق:</strong> ${notification.contribution}</p>
            <p><strong>ماذا يريد من الفريق:</strong> ${notification.expectation}</p>
            <p><strong>رقم الهاتف:</strong> ${notification.phone}</p>
            <p><strong>هل يستطيع التدريب:</strong> ${notification.can_train}</p>
            <p><strong>تاريخ الإنشاء:</strong> ${notification.created_at}</p>
            `;
          } else if (notification.type === 'contact') {
            content = `<h3>رسالة تواصل جديدة</h3>
            <p><strong>الاسم:</strong> ${notification.name}</p>
            <p><strong>رقم الهاتف:</strong> ${notification.phone}</p>
            <p><strong>الرسالة:</strong> ${notification.message}</p>
            <p><strong>تاريخ الإنشاء:</strong> ${notification.created_at}</p>
            `;
          } else if (notification.type === 'course_registration') {
            content = `<h3>تسجيل دورة جديد</h3>
            <p><strong>الاسم:</strong> ${notification.full_name}</p>
            <p><strong>البريد الإلكتروني:</strong> ${notification.email}</p>
            <p><strong>رقم الهاتف:</strong> ${notification.phone}</p>
            <p><strong>معرّف الدورة:</strong> ${notification.course_id}</p>
            <p><strong>الملاحظات:</strong> ${notification.notes}</p>
            <p><strong>تاريخ الإنشاء:</strong> ${notification.created_at}</p>
            `;
          }
          notificationItem.innerHTML = content;
        }
        notificationsContainer.appendChild(notificationItem);
      });
    }

    /*const loadOldButton = document.getElementById('load-old-notifications');
    loadOldButton.addEventListener('click', async () => {
      await fetchOldNotifications();
    });*/
    // استدعاء دالة جلب الإشعارات عند تحميل الصفحة
    fetchNotifications();
  </script>
</body>
</html>