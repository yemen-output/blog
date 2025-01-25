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
  <title>إدارة المتطوعين - OUT PUT للعلوم التقنية </title>
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
      <h1>إدارة المتطوعين</h1>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">العودة للرئيسية</a></li>
        <li><a href="logout.php">تسجيل الخروج</a></li>
      </ul>
    </nav>
  </header>

  <section id="manage-volunteers">
    <h2>إدارة المتطوعين</h2>
    <div id="table-container">
      <table id="volunteers-table">
        <thead>
          <tr>
            <th>الاسم</th>
            <th>المحافظة</th>
            <th>رقم الهاتف</th>
            <th>مجال التطوع</th>
            <th>الحالة</th>
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
    // وظائف إدارة المتطوعين
    async function fetchVolunteers() {
      try {
        const formData = new FormData();
        formData.append('data_type', 'all');
        const response = await fetch('get_volunteers_data.php', {
          method: 'POST',
          body: formData
        });
        if (!response.ok) {
          throw new Error('Failed to fetch volunteer data.');
        }
        const volunteersData = await response.json();
        displayVolunteers(volunteersData);
      } catch (error) {
        console.error(error);
        alert('An error occurred while fetching volunteer data.');

      }
    }
    function displayVolunteers(volunteers) {
      const tableBody = document.querySelector('#volunteers-table tbody');
      tableBody.innerHTML = ''; // Clear any existing table rows

      volunteers.forEach(volunteer => {
        const row = document.createElement('tr');
        row.innerHTML = `
        <td>${volunteer.full_name}</td>
        <td>${volunteer.governorate}</td>
        <td>${volunteer.phone}</td>
        <td>${volunteer.volunteer_area}</td>
        <td id="volunteer-status-${volunteer.id}">${volunteer.status}</td>
        <td>
        <button onclick="acceptVolunteer(${volunteer.id})">قبول</button>
        <button onclick="rejectVolunteer(${volunteer.id})">رفض</button>
        </td>
        `;
        tableBody.appendChild(row);
      });
    }
    function acceptVolunteer(volunteerId) {
      updateVolunteerStatus(volunteerId, 'مقبول');
    }
    function rejectVolunteer(volunteerId) {
      updateVolunteerStatus(volunteerId, 'مرفوض');
    }
    async function updateVolunteerStatus(volunteerId, status) {
      try {
        const formData = new FormData();
        formData.append('volunteer_id', volunteerId);
        formData.append('status', status);
        const response = await fetch('update_volunteer_status.php', {
          method: 'POST',
          body: formData
        });
        if (!response.ok) {
          throw new Error('Failed to update volunteer status.');
        }
        const data = await response.json();
        if (data.error) {
          alert(data.error)

        } else {
          const statusCell = document.querySelector(`#volunteer-status-${volunteerId}`);
          statusCell.textContent = status;
          alert(data.message)
        }
      } catch (error) {
        console.error(error);
        alert('An error occurred while updating volunteer status.');
      }
    }
    fetchVolunteers()
  </script>
</body>
</html>