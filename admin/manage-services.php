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
  <title>إدارة الخدمات - OUT PUT للعلوم التقنية</title>
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
    .actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 5px;
    }
    .actions button {
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 5px 10px;
      margin: 0;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
      font-size: 1em;
    }
    .actions button:hover {
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
    .new-service-button {
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
    .new-service-button:hover {
      background-color: var(--secondary-color);
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <h1>إدارة الخدمات</h1>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">العودة للرئيسية</a></li>
        <li><a href="logout.php">تسجيل الخروج</a></li>
      </ul>
    </nav>
  </header>

  <section id="manage-services">
    <h2>إدارة الخدمات</h2>
    <div id="table-container">
      <table>
        <thead>
          <tr>
            <th>عنوان الخدمة</th>
            <th>الوصف</th>
            <th>التاريخ</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody id="services-table-body">
          <!-- سيتم جلب البيانات هنا -->
        </tbody>
      </table>
    </div>

    <!-- نافذة التأكيد -->
    <div id="confirmationModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <p id="modalMessage">هل أنت متأكد من حذف هذه الخدمة؟</p>
        <div class="modal-buttons">
          <button id="confirmDelete" onclick="deleteService()">نعم</button>
          <button onclick="closeModal()">لا</button>
        </div>
      </div>
    </div>

    <button class="new-service-button" onclick="window.location.href='create-service.php'">إنشاء خدمة</button>
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
    async function fetchServices() {
      try {
        const response = await fetch('../data/services.json');
        if (!response.ok) {
          throw new Error('فشل في جلب البيانات.');
        }
        const services = await response.json();
        displayServices(services);
      } catch (error) {
        console.error(error);
        alert('حدث خطأ أثناء جلب الخدمات.');
      }
    }

    function displayServices(services) {
      const tableBody = document.getElementById('services-table-body');
      tableBody.innerHTML = '';

      services.forEach(service => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${service.title}</td>
          <td>${service.description}</td>
          <td>${service.date}</td>
          <td class="actions">
            <button onclick="editService(${service.id})">تعديل</button>
            <button onclick="openModal(${service.id})">حذف</button>
          </td>
        `;
        tableBody.appendChild(row);
      });
    }

    function editService(serviceId) {
      window.location.href = `edit-service.php?service_id=${serviceId}`;
    }

    let serviceToDeleteId = null;

    function openModal(serviceId) {
      serviceToDeleteId = serviceId;
      document.getElementById('confirmationModal').style.display = 'block';
    }

    function closeModal() {
      serviceToDeleteId = null;
      document.getElementById('confirmationModal').style.display = 'none';
    }

    async function deleteService() {
      if (serviceToDeleteId === null) return;

      try {
        const response = await fetch('delete_service.php', {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ id: serviceToDeleteId }),
        });

        if (response.ok) {
          alert('تم حذف الخدمة بنجاح.');
          fetchServices(); // إعادة تحميل البيانات بعد الحذف
        } else {
          const errorData = await response.json();
          alert(`خطأ أثناء حذف الخدمة: ${errorData.message || response.statusText}`);
        }
      } catch (error) {
        console.error('حدث خطأ:', error);
        alert('فشل الاتصال بالخادم.');
      } finally {
        closeModal();
      }
    }

    // استدعاء دالة جلب البيانات عند تحميل الصفحة
    fetchServices();
  </script>
</body>
</html>