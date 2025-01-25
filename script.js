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

async function makePostRequest(url, data) {
  try {
    const formData = new FormData();
    for (const key in data) {
      formData.append(key, data[key]);
    }
    const response = await fetch(url, {
      method: 'POST',
      body: formData,
    });
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Error during POST request:', error);
    throw error; // إعادة رمي الخطأ ليتم التقاطه في المكان الذي يستدعي هذه الدالة
  }
}

/*async function exportComments() {
  try {
    const data = await makePostRequest('admin/export_comments.php');
    return data;

  } catch (error) {
    console.error('An error occurred while exporting comments:', error);
  }
}

async function exportRequests() {
  try {
    const data = await makePostRequest('admin/export_requests.php');
    return data;

  } catch (error) {
    console.error('An error occurred while exporting requests:', error);

  }
}

async function exportVolunteers() {
  try {
    const data = await makePostRequest('admin/export_volunteers.php');
    return data;

  } catch (error) {
    console.error('An error occurred while exporting volunteers:', error);
  }
}

async function exportContacts() {
  try {
    const data = await makePostRequest('admin/export_contacts.php');
    return data;

  } catch (error) {
    console.error('An error occurred while exporting contacts:', error);
  }
}

async function exportCourses() {
  try {
    const data = await makePostRequest('admin/export_courses.php');
    return data;

  } catch (error) {
    console.error('An error occurred while exporting courses:', error);
  }
}
*/