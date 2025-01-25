<?php
session_start();

// التحقق من تسجيل الدخول
if (!(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true)) {
    // تسجيل الدخول غير صحيح
    http_response_code(302); // Found (Redirection)
    header('Location: ../index.html'); // إعادة التوجيه إلى الصفحة الرئيسية
    exit;
}

// دالة لجلب الإحصائيات من قاعدة البيانات أو JSON
function fetchStatistics($db_file, $status_file, $json_file, $table_name)
{
    $db_file = "../" . $db_file; // إضافة "../" للمسار
    $status_file = "../" . $status_file; // إضافة "../" للمسار
    $json_file = "../" . $json_file; // إضافة "../" للمسار

    $total = 0;
    $accepted = 0;
    $rejected = 0;
    $pending = 0;
    $total_amount = 0;

    // جلب البيانات من قاعدة البيانات
    if (file_exists($db_file)) {
        try {
            $db = new PDO('sqlite:' . $db_file);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // حساب إجمالي السجلات في قاعدة البيانات
            if ($table_name) {
                $stmt = $db->query("SELECT COUNT(*) FROM " . $table_name);
                $total += $stmt->fetchColumn();
            }

        } catch (PDOException $e) {
            // معالجة الخطأ بشكل مناسب، مثل تسجيله في ملف
            error_log("Database error: " . $e->getMessage());
        }
    }

    // قراءة ملف الحالة
    $statuses = [];
    if (file_exists($status_file)) {
        $statuses_content = file_get_contents($status_file);
        if ($statuses_content !== false) {
            $statuses = json_decode($statuses_content, true);
            if (!is_array($statuses)) {
                $statuses = [];
            }
        }
        // حساب الإحصائيات من ملف الحالة
        foreach ($statuses as $id => $statusData) {
            $status = isset($statusData['status']) ? $statusData['status'] : 'قيد المراجعة';
            if ($status == 'مقبول') {
                $accepted++;
            } elseif ($status == 'مرفوض') {
                $rejected++;
            } else {
                $pending++;
            }

            // جمع المبالغ من ملف الحالة
            if (isset($statusData['amount']) && is_numeric($statusData['amount'])) {
                $total_amount += floatval($statusData['amount']);
            }
        }
    }
    // جلب البيانات من ملف JSON (مع تحديث الحالة)
    $json_data = [];
    if (file_exists($json_file)) {
        $json_content = file_get_contents($json_file);
        if ($json_content !== false) {
            $json_data = json_decode($json_content, true);
            if (!is_array($json_data)) {
                $json_data = [];
            }
        }

        // تحديث الحالة من ملف statuses
        foreach ($json_data as &$item) {
            $id = $item['id'];
            $item['status'] = isset($statuses[$id]['status']) ? $statuses[$id]['status'] : 'قيد المراجعة';
        }
        // حساب إجمالي السجلات من json
        $total += count($json_data);

    }

    // حساب قيد المراجعة بشكل صحيح
    $pending = $total - ($accepted + $rejected);
    // في حال عدم وجود بيانات في قاعدة البيانات ووجودها في ملف json
    if ($total == 0 && !empty($json_data)) $total = count($json_data);

    return [
        'total' => $total,
        'accepted' => $accepted,
        'rejected' => $rejected,
        'pending' => $pending,
        'total_amount' => $total_amount
    ];
}

// جلب إحصائيات تسجيل الدورات
$coursesStats = fetchStatistics('database/courses_registrations.db', 'data/courses_registrations_status.json', 'data/courses_registrations.json', 'courses_registrations');

// جلب إحصائيات طلبات الخدمات
$requestsStats = fetchStatistics('database/requests.db', 'data/requests_status.json', 'data/requests.json', 'requests');

// جلب إحصائيات المتطوعين
$volunteersStats = fetchStatistics('database/volunteers.db', 'data/volunteer_status.json', 'data/volunteers.json', 'volunteers');

// دمج المبالغ
$total_amount = $coursesStats['total_amount'] + $requestsStats['total_amount'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إحصائيات الموقع - OUT PUT للعلوم التقنية </title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="../main.css">
  <style>
    .statistics {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .statistic-item {
      background-color: var(--white);
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 20px;
      text-wrap: nowrap;
      text-align: center;
      flex-grow: 1;
      flex-basis: 0;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .statistic-item:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .statistic-item h3 {
      color: var(--primary-color);
      font-size: 1.5em;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <h1>إحصائيات الموقع</h1>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">العودة للرئيسية</a></li>
        <li><a href="logout.php">تسجيل الخروج</a></li>
      </ul>
    </nav>
  </header>

  <section id="statistics">
    <h2>إحصائيات الموقع</h2>
    <div class="statistics">
      <div class="statistic-item section-content">
        <h3>عدد تسجيل الدورات</h3>
        <p>
          إجمالي: <?php echo $coursesStats['total']; ?>
        </p>
        <p>
          مقبول: <?php echo $coursesStats['accepted']; ?>
        </p>
        <p>
          مرفوض: <?php echo $coursesStats['rejected']; ?>
        </p>
        <p>
          قيد المراجعة: <?php echo $coursesStats['pending']; ?>
        </p>
      </div>
      <div class="statistic-item section-content">
        <h3>عدد طلب الخدمات</h3>
        <p>
          إجمالي: <?php echo $requestsStats['total']; ?>
        </p>
        <p>
          مقبول: <?php echo $requestsStats['accepted']; ?>
        </p>
        <p>
          مرفوض: <?php echo $requestsStats['rejected']; ?>
        </p>
        <p>
          قيد المراجعة: <?php echo $requestsStats['pending']; ?>
        </p>
      </div>
      <div class="statistic-item section-content">
        <h3>عدد المتطوعين</h3>
        <p>
          إجمالي: <?php echo $volunteersStats['total']; ?>
        </p>
        <p>
          مقبول: <?php echo $volunteersStats['accepted']; ?>
        </p>
        <p>
          مرفوض: <?php echo $volunteersStats['rejected']; ?>
        </p>
        <p>
          قيد المراجعة: <?php echo $volunteersStats['pending']; ?>
        </p>
      </div>
      <div class="statistic-item section-content">
        <h3>المبلغ المجموع من تسجيل الدورات</h3>
        <p>
          <?php echo $coursesStats['total_amount']; ?>
        </p>
      </div>
      <div class="statistic-item section-content">
        <h3>المبلغ المجموع من الخدمات</h3>
        <p>
          <?php echo $requestsStats['total_amount']; ?>
        </p>
      </div>
      <div class="statistic-item section-content">
        <h3>إجمالي المبلغ المجموع</h3>
        <p>
          <?php echo $total_amount; ?>
        </p>
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
</body>
</html>