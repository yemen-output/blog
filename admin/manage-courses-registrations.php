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
  <title>إدارة تسجيل الدورات - OUT PUT للعلوم التقنية </title>
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
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <h1>إدارة تسجيل الدورات</h1>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">العودة للرئيسية</a></li>
        <li><a href="logout.php">تسجيل الخروج</a></li>
      </ul>
    </nav>
  </header>

  <section id="manage-courses-registrations">
    <h2>إدارة تسجيل الدورات</h2>
    <div id="table-container">
      <table id="courses-registrations-table">
        <thead>
          <tr>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>رقم الهاتف</th>
            <th>الدورة</th>
            <th>الحالة</th>
            <th>المبلغ</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
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
    async function fetchCoursesRegistrations() {
      try {
        const formData = new FormData();
        formData.append('data_type', 'all');
        const response = await fetch('get_courses_registrations_data.php', {
          method: 'POST',
          body: formData
        });
        if (!response.ok) {
          throw new Error('Failed to fetch courses registrations data.');
        }
        const coursesRegistrationsData = await response.json();
        displayCoursesRegistrations(coursesRegistrationsData);
      } catch (error) {
        console.error(error);
        alert('An error occurred while fetching courses registrations data.');

      }
    }
    function displayCoursesRegistrations(coursesRegistrations) {
      const tableBody = document.querySelector('#courses-registrations-table tbody');
      tableBody.innerHTML = '';

      coursesRegistrations.forEach(registration => {
        const row = document.createElement('tr');
        row.innerHTML = `
        <td>${registration.full_name}</td>
        <td>${registration.email}</td>
        <td>${registration.phone}</td>
        <td>${registration.course_id}</td>
        <td id="courses-registrations-status-${registration.id}">${registration.status || 'قيد المراجعة'}</td>
        <td id="courses-registrations-amount-${registration.id}">${registration.amount || 'لم يتم تحديده'}</td>
        <td>
        <button onclick="acceptCourseRegistration(${registration.id})">قبول</button>
        <button onclick="rejectCourseRegistration(${registration.id})">رفض</button>
        <button onclick="updateCourseRegistrationAmount(${registration.id})">تحديد المبلغ</button>

        </td>
        `;
        tableBody.appendChild(row);
      });

    }
    async function acceptCourseRegistration(registrationId) {
      const amount = prompt("أدخل المبلغ الذي سجّل به الدورة:");
      if (amount === null) return
      if (amount.trim() === '' || isNaN(amount) || parseFloat(amount) < 0) {
        alert("الرجاء إدخال مبلغ صحيح.");
        return;
      }
      updateCourseRegistrationStatus(registrationId, 'مقبول', amount);
    }
    async function updateCourseRegistrationAmount(registrationId) {
      const amount = prompt("أدخل المبلغ الذي سجّل به الدورة:");
      if (amount === null) return
      if (amount.trim() === '' || isNaN(amount) || parseFloat(amount) < 0) {
        alert("الرجاء إدخال مبلغ صحيح.");
        return;
      }

      updateCourseRegistrationStatus(registrationId, null, amount);
    }


    function rejectCourseRegistration(registrationId) {
      updateCourseRegistrationStatus(registrationId, 'مرفوض');
    }

    async function updateCourseRegistrationStatus(registrationId, status, amount = null) {
      try {
        const formData = new FormData();
        formData.append('registration_id', registrationId);
        formData.append('status', status);
        if (amount !== null) {
          formData.append('amount', amount);
        }
        const response = await fetch('update_course_registration_status.php', {
          method: 'POST',
          body: formData
        });
        if (!response.ok) {
          throw new Error('Failed to update course registration status.');
        }
        const data = await response.json();
        if (data.error) {
          alert(data.error)
        } else {
          const statusCell = document.querySelector(`#courses-registrations-status-${registrationId}`);
          const amountCell = document.querySelector(`#courses-registrations-amount-${registrationId}`);

          if (status) {
            statusCell.textContent = status;
          }
          if (amount) {
            amountCell.textContent = amount;
          }

          alert(data.message)

        }
      } catch (error) {
        console.error(error);
        alert('An error occurred while updating course registration status.');

      }
    }
    fetchCoursesRegistrations()
  </script>
</body>
</html>