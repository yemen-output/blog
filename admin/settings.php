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
  <title>إعدادات الموقع - OUT PUT للعلوم التقنية </title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="../main.css">
  <style>
    .settings-tabs {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .settings-tabs button {
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .settings-tabs button:hover {
      background-color: var(--secondary-color);
    }
    .settings-tabs button.active {
      background-color: var(--secondary-color);
    }
    .settings-content {
      display: none;
    }
    .settings-content.active {
      display: block;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    label {
      font-weight: bold;
    }
    input, textarea, select {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 1em;
    }
    button[type="submit"] {
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 10px;
      border-radius: 8px;
      font-size: 1.1em;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    button[type="submit"]:hover {
      background-color: var(--secondary-color);
    }
    .video-preview {
      margin-top: 20px;
      text-align: center;
    }
    .video-preview iframe {
      width: 100%;
      max-width: 560px;
      height: 315px;
      border: none;
      border-radius: 8px;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <h1>إعدادات الموقع</h1>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">العودة للرئيسية</a></li>
        <li><a href="logout.php">تسجيل الخروج</a></li>
      </ul>
    </nav>
  </header>

  <section id="settings">
    <h2>إعدادات الموقع</h2>
    <div class="settings-tabs">
      <button onclick="openTab('seo')" class="active">SEO</button>
      <button onclick="openTab('security')">الأمان</button>
      <button onclick="openTab('performance')">الأداء</button>
      <button onclick="openTab('design')">التصميم</button>
      <button onclick="openTab('social')">التواصل</button>
      <button onclick="openTab('language')">اللغة</button>
      <button onclick="openTab('notifications')">الإشعارات</button>
      <button onclick="openTab('roles')">الصلاحيات</button>
      <button onclick="openTab('privacy')">الخصوصية</button>
      <button onclick="openTab('accessibility')">إمكانية الوصول</button>
      <button onclick="openTab('updates')">التحديثات</button>
      <button onclick="openTab('logs')">السجلات</button>
      <button onclick="openTab('integrations')">التكاملات</button>
      <button onclick="openTab('advanced')">الإعدادات المتقدمة</button>
      <button onclick="openTab('content')">إدارة المحتوى</button>
    </div>

    <!-- إعدادات SEO -->
    <div id="seo" class="settings-content active">
      <form id="seo-form">
        <label for="site-title">عنوان الموقع:</label>
        <input class="section-content" type="text" id="site-title" placeholder="عنوان الموقع" required>
        <label for="site-description">وصف الموقع:</label>
        <textarea class="section-content" id="site-description" placeholder="وصف الموقع" required></textarea>
        <label for="site-keywords">الكلمات المفتاحية:</label>
        <input class="section-content" type="text" id="site-keywords" placeholder="الكلمات المفتاحية" required>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات الأمان -->
    <div id="security" class="settings-content">
      <form id="security-form">
        <label for="enable-https">تمكين HTTPS:</label>
        <select class="section-content" id="enable-https">
          <option value="yes">نعم</option>
          <option value="no">لا</option>
        </select>
        <label for="firewall">جدار الحماية:</label>
        <select class="section-content" id="firewall">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات الأداء -->
    <div id="performance" class="settings-content">
      <form id="performance-form">
        <label for="enable-caching">تمكين التخزين المؤقت:</label>
        <select class="section-content" id="enable-caching">
          <option value="yes">نعم</option>
          <option value="no">لا</option>
        </select>
        <label for="image-compression">ضغط الصور:</label>
        <select class="section-content" id="image-compression">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات التصميم -->
    <div id="design" class="settings-content">
      <form id="design-form">
        <label for="theme">السمة:</label>
        <select class="section-content" id="theme">
          <option value="light">فاتح</option>
          <option value="dark">غامق</option>
        </select>
        <label for="primary-color">اللون الأساسي:</label>
        <input type="color" id="primary-color" value="#007bff">
        <label for="font-family">نوع الخط:</label>
        <select class="section-content" id="font-family">
          <option value="Tajawal">Tajawal</option>
          <option value="Arial">Arial</option>
          <option value="Times New Roman">Times New Roman</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات التواصل -->
    <div id="social" class="settings-content">
      <form id="social-form">
        <label for="whatsapp">واتساب:</label>
        <input class="section-content" type="url" id="whatsapp" placeholder="رابط واتساب">
        <label for="facebook">فيسبوك:</label>
        <input class="section-content" type="url" id="facebook" placeholder="رابط فيسبوك">
        <label for="instagram">إنستغرام:</label>
        <input class="section-content" type="url" id="instagram" placeholder="رابط إنستغرام">
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات اللغة -->
    <div id="language" class="settings-content">
      <form id="language-form">
        <label for="default-language">اللغة الافتراضية:</label>
        <select class="section-content" id="default-language">
          <option value="ar">العربية</option>
          <option value="en">الإنجليزية</option>
          <option value="fr">الفرنسية</option>
        </select>
        <label for="enable-translation">تمكين الترجمة:</label>
        <select class="section-content" id="enable-translation">
          <option value="yes">نعم</option>
          <option value="no">لا</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات الإشعارات -->
    <div id="notifications" class="settings-content">
      <form id="notifications-form">
        <label for="push-notifications">الإشعارات الفورية:</label>
        <select class="section-content" id="push-notifications">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات الصلاحيات -->
    <div id="roles" class="settings-content">
      <form id="roles-form">
        <label for="user-roles">أدوار المستخدمين:</label>
        <select class="section-content" id="user-roles">
          <option value="admin">مشرف</option>
          <option value="editor">محرر</option>
          <option value="user">مستخدم</option>
        </select>
        <label for="role-permissions">صلاحيات الأدوار:</label>
        <textarea class="section-content" id="role-permissions" placeholder="أدخل الصلاحيات"></textarea>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات الخصوصية -->
    <div id="privacy" class="settings-content">
      <form id="privacy-form">
        <label for="enable-cookies">تمكين ملفات تعريف الارتباط:</label>
        <select class="section-content" id="enable-cookies">
          <option value="yes">نعم</option>
          <option value="no">لا</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات إمكانية الوصول -->
    <div id="accessibility" class="settings-content">
      <form id="accessibility-form">
        <label for="enable-high-contrast">تمكين الوضع عالي التباين:</label>
        <select class="section-content" id="enable-high-contrast">
          <option value="yes">نعم</option>
          <option value="no">لا</option>
        </select>
        <label for="font-size">حجم الخط:</label>
        <select class="section-content" id="font-size">
          <option value="small">صغير</option>
          <option value="medium">متوسط</option>
          <option value="large">كبير</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات التحديثات -->
    <div id="updates" class="settings-content">
      <form id="updates-form">
        <label for="auto-updates">التحديث التلقائي:</label>
        <select class="section-content" id="auto-updates">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <label for="update-frequency">تكرار التحديث:</label>
        <select class="section-content" id="update-frequency">
          <option value="daily">يومي</option>
          <option value="weekly">أسبوعي</option>
          <option value="monthly">شهري</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات السجلات -->
    <div id="logs" class="settings-content">
      <form id="logs-form">
        <label for="log-level">مستوى السجلات:</label>
        <select class="section-content" id="log-level">
          <option value="info">معلومات</option>
          <option value="warning">تحذير</option>
          <option value="error">خطأ</option>
        </select>
        <label for="log-retention">فترة الاحتفاظ بالسجلات:</label>
        <select class="section-content" id="log-retention">
          <option value="7">أسبوع واحد</option>
          <option value="30">شهر واحد</option>
          <option value="90">ثلاثة أشهر</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إعدادات التكاملات -->
    <div id="integrations" class="settings-content">
      <form id="integrations-form">
        <label for="google-services">خدمات Google:</label>
        <select class="section-content" id="google-services">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <label for="social-media-integration">التكامل مع وسائل التواصل الاجتماعي:</label>
        <select class="section-content" id="social-media-integration">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- الإعدادات المتقدمة -->
    <div id="advanced" class="settings-content">
      <form id="advanced-form">
        <label for="debug-mode">وضع التصحيح:</label>
        <select class="section-content" id="debug-mode">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <label for="maintenance-mode">وضع الصيانة:</label>
        <select class="section-content" id="maintenance-mode">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
    </div>

    <!-- إدارة المحتوى -->
    <div id="content" class="settings-content">
      <form id="content-form">
        <label for="content-approval">تفعيل الموافقة على المحتوى:</label>
        <select class="section-content" id="content-approval">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <label for="content-archiving">تفعيل أرشفة المحتوى:</label>
        <select class="section-content" id="content-archiving">
          <option value="enabled">مفعل</option>
          <option value="disabled">معطل</option>
        </select>
        <button type="submit">حفظ التغييرات</button>
      </form>
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

  <button class="scroll-to-top" onclick="scrollToTop()">
    <i class="fas fa-arrow-up"></i>
  </button>

  <script src="../script.js"></script>
  <script>
    // فتح تبويبات الإعدادات
    function openTab(tabName) {
      const tabs = document.querySelectorAll('.settings-content');
      tabs.forEach(tab => tab.classList.remove('active'));
      document.getElementById(tabName).classList.add('active');

      const buttons = document.querySelectorAll('.settings-tabs button');
      buttons.forEach(button => button.classList.remove('active'));
      document.querySelector(`button[onclick="openTab('${tabName}')"]`).classList.add('active');
    }

    const settingsData = {
      seo: {
        siteTitle: "",
        siteDescription: "",
        siteKeywords: ""
      },
      security: {
        enableHttps: true,
        firewall: "enabled"
      },
      performance: {
        enableCaching: true,
        imageCompression: "enabled",
      },
      design: {
        theme: "light",
        // light, dark
        primaryColor: "#007bff",
        fontFamily: "Tajawal",
        // Tajawal, Arial, Times New Roman
      },
      social: {
        whatsapp: "",
        facebook: "",
        instagram: ""
      },
      language: {
        defaultLanguage: "ar",
        enableTranslation: true
      },
      notifications: {
        pushNotifications: "enabled"
      },
      roles: {
        userRoles: "admin",
        // admin, editor, user
        rolePermissions: "",
        // صلاحيات الأدوار
      },
      privacy: {
        enableCookies: true
      },
      accessibility: {
        enableHighContrast: true,
        fontSize: "small",
        // small, medium, large
      },
      updates: {
        autoUpdates: "enabled",
        updateFrequency: "weekly"
        // daily, weekly, monthly
      },
      logs: {
        logLevel: "info",
        // info, warning, error
        logRetention: "7"
        // 7, 30, 90
      },
      integrations: {
        googleServices: "enabled",
        socialMediaIntegration: "enabled"
      },
      advanced: {
        debugMode: "enabled",
        maintenanceMode: "enabled"
      },
      content: {
        contentApproval: "enabled",
        contentArchiving: "enabled"
      },
    };

    const forms = document.querySelectorAll("form");
    forms.forEach((form)=> {
      const id = form.id.split("-")[0];
      form.addEventListener("submit", (e)=> {
        e.preventDefault();
        
        if (id === "seo") {
          const siteTitle = form.getElementById("site-title");
          const siteDescription = form.getElementById("site-description");
          const siteKeywords = form.getElementById("site-keywords");
          
        }
      });
    })
  </script>
</body>
</html>