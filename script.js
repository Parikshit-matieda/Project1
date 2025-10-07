(function () {
  const coursesListEl = document.getElementById('courses-list');
  const coursesEmptyEl = document.getElementById('courses-empty');
  const form = document.getElementById('course-form');
  const statusEl = document.getElementById('form-status');
  const headerContainer = document.querySelector('.site-header .container');

  function renderAuthLinks(user) {
    if (!headerContainer) return;
    let authEl = document.getElementById('auth-links');
    if (!authEl) {
      authEl = document.createElement('div');
      authEl.id = 'auth-links';
      authEl.style.float = 'right';
      headerContainer.appendChild(authEl);
    }
    if (user) {
      authEl.innerHTML = `
        <span style="margin-right:8px;color:#9aa4b2;">${user.email}</span>
        <button id="logout-btn" style="background:#28c997;">Logout</button>
      `;
      const logoutBtn = document.getElementById('logout-btn');
      logoutBtn.addEventListener('click', async () => {
        try {
          await fetch('php/logout.php', { method: 'POST' });
          location.href = 'login.html';
        } catch (_) {
          location.href = 'login.html';
        }
      });
    } else {
      authEl.innerHTML = `
        <a href="login.html" style="margin-right:8px;">Login</a>
        <a href="signup.html">Sign Up</a>
      `;
    }
  }

  async function fetchMe() {
    try {
      const res = await fetch('php/me.php');
      const out = await res.json();
      renderAuthLinks(out.user || null);
      return out.user || null;
    } catch (_) {
      renderAuthLinks(null);
      return null;
    }
  }

  async function fetchCourses() {
    try {
      const res = await fetch('php/courses.php');
      if (!res.ok) throw new Error('Failed to load courses');
      const data = await res.json();
      renderCourses(Array.isArray(data) ? data : []);
    } catch (err) {
      console.error(err);
      coursesListEl.innerHTML = '';
      coursesEmptyEl.style.display = 'block';
      coursesEmptyEl.textContent = 'Error loading courses. Check PHP/MySQL setup.';
    }
  }

  function renderCourses(courses) {
    coursesListEl.innerHTML = '';
    if (!courses.length) {
      coursesEmptyEl.style.display = 'block';
      return;
    }
    coursesEmptyEl.style.display = 'none';
    for (const course of courses) {
      const li = document.createElement('li');
      li.className = 'course-item';
      li.innerHTML = `
        <div class="course-title">${escapeHtml(course.title)}</div>
        ${course.description ? `<div class="course-desc">${escapeHtml(course.description)}</div>` : ''}
        <div class="course-meta">Created: ${course.created_at ? new Date(course.created_at).toLocaleString() : '—'}</div>
      `;
      coursesListEl.appendChild(li);
    }
  }

  function escapeHtml(str) {
    if (typeof str !== 'string') return '';
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    statusEl.textContent = 'Saving...';
    const title = document.getElementById('title').value.trim();
    const description = document.getElementById('description').value.trim();
    if (!title) {
      statusEl.textContent = 'Title is required';
      return;
    }
    try {
      const res = await fetch('php/add_course.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title, description })
      });
      if (!res.ok) throw new Error('Request failed');
      const out = await res.json();
      if (out && out.success) {
        statusEl.textContent = 'Saved!';
        form.reset();
        await fetchCourses();
      } else {
        statusEl.textContent = out && out.error ? out.error : 'Error saving course';
      }
    } catch (err) {
      console.error(err);
      statusEl.textContent = 'Network or server error';
    }
  });

  fetchMe();
  fetchCourses();
})();


