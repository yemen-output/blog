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
  <title>إدارة الكورسات - OUT PUT للعلوم التقنية</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="../main.css">
  <style>
    #table-container {
      overflow-x: scroll;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 10px;
      text-align: center;
      text-wrap: nowrap;
    }
    th {
      background-color: var(--primary-color);
      color: var(--white);
    }
    tr:nth-child(odd) {
      background-color: var(--white);
    }
    body.dark-mode tr:nth-child(odd) {
      background-color: var(--black);
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    body.dark-mode tr:nth-child(even) {
      background-color: var(--dark-mode-box);
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    body.dark-mode tr:hover {
      background-color: #131313;
    }
    button {
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 5px 10px;
      margin: 5px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    button:hover {
      background-color: var(--secondary-color);
    }
    .new-course-button {
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
      font-size: 1.1em;
      margin-top: 20px;
      display: block;
      margin-left: auto;
      margin-right: auto;
      width: fit-content;
    }
    .new-course-button:hover {
      background-color: var(--secondary-color);
    }
    /* نافذة التأكيد */
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
    }
    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 30%;
    }
    body.dark-mode .modal-content {
      background-color: var(--black);
      border: 1px solid #fefefe;
      color: var(--white);
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    .modal-buttons {
      text-align: center;
      margin-top: 20px;
    }
    .modal-buttons button {
      margin: 0 10px;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <h1>إدارة الكورسات</h1>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">العودة للرئيسية</a></li>
        <li><a href="logout.php">تسجيل الخروج</a></li>
      </ul>
    </nav>
  </header>

  <section id="manage-courses">
    <h2>إدارة الكورسات</h2>
    <div id="table-container">
      <table>
        <thead>
          <tr>
            <th>اسم الكورس</th>
            <th>الوصف</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody id="courses-table-body">
          <!-- سيتم جلب بيانات الكورسات هنا -->
        </tbody>
      </table>
    </div>

    <button class="new-course-button" onclick="window.location.href='create-course.php'">إنشاء كورس</button>

    <!-- نافذة التأكيد -->
    <div id="confirmationModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <p id="modalMessage">هل أنت متأكد من حذف هذا الكورس؟</p>
        <div class="modal-buttons">
          <button id="confirmDelete" onclick="deleteCourse()">نعم</button>
          <button onclick="closeModal()">لا</button>
        </div>
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
  <script>
    // جلب وعرض بيانات الكورسات
    async function fetchCourses() {
      try {
        const response = await fetch('../data/courses.json');
        if (!response.ok) {
          throw new Error('فشل في جلب بيانات الكورسات.');
        }
        const courses = await response.json();
        displayCourses(courses);
      } catch (error) {
        console.error(error);
        alert('حدث خطأ أثناء جلب بيانات الكورسات.');
      }
    }

    // عرض الكورسات في الجدول
    function displayCourses(courses) {
      const tableBody = document.getElementById('courses-table-body');
      tableBody.innerHTML = '';

      courses.forEach(course => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${course.title}</td>
          <td>${course.description}</td>
          <td>
            <button onclick="editCourse(${course.id})">تعديل</button>
            <button onclick="openModal(${course.id})">حذف</button>
          </td>
        `;
        tableBody.appendChild(row);
      });
    }

    // وظائف إدارة الكورسات
    function editCourse(courseId) {
      window.location.href = `edit-course.php?course_id=${courseId}`;
    }

    let courseToDeleteId = null;

    function openModal(courseId) {
      courseToDeleteId = courseId;
      document.getElementById('confirmationModal').style.display = 'block';
    }

    function closeModal() {
      courseToDeleteId = null;
      document.getElementById('confirmationModal').style.display = 'none';
    }

    async function deleteCourse() {
      if (courseToDeleteId === null) return;

      try {
        const response = await fetch('delete_course.php', {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ id: courseToDeleteId }),
        });

        if (response.ok) {
          alert('تم حذف الكورس بنجاح.');
          fetchCourses(); // إعادة تحميل الكورسات بعد الحذف
        } else {
          const data = await response.json();
          alert(`خطأ أثناء حذف الكورس: ${data.message || response.statusText}`);
        }
      } catch (error) {
        console.error('حدث خطأ:', error);
        alert('فشل الاتصال بالخادم.');
      } finally {
        closeModal();
      }
    }

    // استدعاء دالة جلب بيانات الكورسات عند تحميل الصفحة
    fetchCourses();
  </script>
</body>
</html>