let allCategories = [];

async function loadApps() {
    try {
        const res = await fetch('apps.json?t=' + Date.now(), { cache: 'no-store' });
        const data = await res.json();
        allCategories = data.categories || [];
        render(allCategories);
    } catch (err) {
        console.error('[apps-site] gagal load apps.json:', err);
        document.getElementById('categories').innerHTML =
            '<div class="empty">Gagal memuat daftar aplikasi. Cek apps.json.</div>';
    }
}

function render(categories) {
    const container = document.getElementById('categories');
    const emptyMsg = document.getElementById('empty-msg');

    const totalApps = categories.reduce((sum, c) => sum + c.apps.length, 0);
    emptyMsg.style.display = totalApps === 0 ? 'block' : 'none';

    container.innerHTML = categories.filter(c => c.apps.length > 0).map(cat => `
    <div class="category">
      <div class="category-title">${cat.name}</div>
      <ul class="app-list">
        ${cat.apps.map(app => `
          <li class="app-item">
            <span class="app-bullet">-</span>
            <span class="app-icon">${app.icon || '📦'}</span>
            <span class="app-name" title="${app.name}">${app.name}${app.version ? ` v${app.version}` : ''}</span>
            <a class="dl-btn" href="${app.file}" download>Download</a>
          </li>
        `).join('')}
      </ul>
    </div>
  `).join('');
}

document.getElementById('search').addEventListener('input', (e) => {
    const q = e.target.value.trim().toLowerCase();
    if (!q) { render(allCategories); return; }

    const filtered = allCategories.map(cat => ({
        name: cat.name,
        apps: cat.apps.filter(app => app.name.toLowerCase().includes(q))
    }));
    render(filtered);
});

loadApps();