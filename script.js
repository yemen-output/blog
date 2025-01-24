const themeIcon = document.querySelector('.theme-switcher i');

const isDarkMode = JSON.parse(localStorage.getItem("is-dark-mode"));
if (isDarkMode === true) {
  document.body.classList.add("dark-mode");
  themeIcon.classList.remove('fa-moon');
  themeIcon.classList.add('fa-sun');
}

// تبديل السمة (فاتح/غامق)
function toggleTheme() {
  document.body.classList.toggle('dark-mode');
  if (document.body.classList.contains('dark-mode')) {
    themeIcon.classList.remove('fa-moon');
    themeIcon.classList.add('fa-sun');
    localStorage.setItem("is-dark-mode", JSON.stringify(true));
  } else {
    themeIcon.classList.remove('fa-sun');
    themeIcon.classList.add('fa-moon');
    localStorage.setItem("is-dark-mode", JSON.stringify(false));
  }
}

// إضافة تأثيرات عند التمرير
const sections = document.querySelectorAll('section');

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('animate__animated', 'animate__fadeInUp');
    }
  });
}, {
  threshold: 0.1
});

sections.forEach(section => {
  observer.observe(section);
});

// إضافة زر للعودة للأعلى
const scrollToTopBtn = document.createElement('button');
scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
scrollToTopBtn.classList.add('scroll-to-top');
scrollToTopBtn.style.display = "none";
document.body.appendChild(scrollToTopBtn);

scrollToTopBtn.addEventListener('click', () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
});

window.addEventListener('scroll', () => {
  if (window.scrollY > 300) {
    scrollToTopBtn.style.display = 'block';
  } else {
    scrollToTopBtn.style.display = 'none';
  }
});

async function checkLogin() {
  try {
    const response = await fetch('admin/check-login.php');
    const data = await response.json();

    if (!response.ok || !data.logged_in) {
      window.location.href = 'admin/login.php';
    }
  } catch (error) {
    console.error('حدث خطأ:', error);
    alert('فشل في التحقق من حالة تسجيل الدخول.');
  }
}