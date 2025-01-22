const settingsData = {
  seo: {
    siteTitle: "", // عنوان الموقع
    siteDescription: "", // وصف الموقع
    siteKeywords: "", // الكلمات المفتاحية
  },
  security: {
    enableHttps: "yes", // yes, no
    firewall: "enabled", // enabled, disabled
  },
  performance: {
    enableCaching: "yes", // yes, no
    imageCompression: "enabled", // enabled, disabled
  },
  design: {
    theme: "light", // light, dark
    primaryColor: "#007bff", // أي كود لون
    fontFamily: "Tajawal", // Tajawal, Arial, Times New Roman
  },
  social: {
    whatsapp: "", // رابط واتساب
    facebook: "", // رابط فيسبوك
    instagram: "", // رابط إنستغرام
  },
  backup: {
    backupFrequency: "daily", // daily, weekly, monthly
  },
  language: {
    defaultLanguage: "ar", // ar, en, fr
    enableTranslation: "yes", // yes, no
  },
  notifications: {
    emailNotifications: "enabled", // enabled, disabled
    pushNotifications: "enabled", // enabled, disabled
  },
  analytics: {
    googleAnalytics: "", // كود Google Analytics
    enableAnalytics: "yes", // yes, no
  },
  api: {
    apiKey: "", // مفتاح API
    enableApi: "yes", // yes, no
  },
  roles: {
    userRoles: "admin", // admin, editor, user
    rolePermissions: "", // صلاحيات الأدوار
  },
  privacy: {
    enableCookies: "yes", // yes, no
    privacyPolicy: "", // رابط سياسة الخصوصية
  },
  accessibility: {
    enableHighContrast: "yes", // yes, no
    fontSize: "small", // small, medium, large
  },
  updates: {
    autoUpdates: "enabled", // enabled, disabled
    updateFrequency: "daily", // daily, weekly, monthly
  },
  logs: {
    logLevel: "info", // info, warning, error
    logRetention: "7", // 7, 30, 90
  },
  integrations: {
    googleServices: "enabled", // enabled, disabled
    socialMediaIntegration: "enabled", // enabled, disabled
  },
  customCode: {
    customCss: "", // CSS مخصص
    customJs: "", // JavaScript مخصص
  },
  advanced: {
    debugMode: "enabled", // enabled, disabled
    maintenanceMode: "enabled", // enabled, disabled
  },
  videos: {
    youtubeLink: "", // رابط فيديو يوتيوب
  },
  content: {
    contentApproval: "enabled", // enabled, disabled
    contentArchiving: "enabled", // enabled, disabled
  },
};