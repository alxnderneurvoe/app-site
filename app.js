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

  container.innerHTML = '';

  categories.filter(c => c.apps.length > 0).forEach(cat => {
    const categoryEl = document.createElement('div');
    categoryEl.className = 'category';

    const titleEl = document.createElement('div');
    titleEl.className = 'category-title';
    titleEl.textContent = cat.name;

    const listEl = document.createElement('ul');
    listEl.className = 'app-list';

    cat.apps.forEach(app => {
      const itemEl = document.createElement('li');
      itemEl.className = 'app-item';

      const leftEl = document.createElement('div');
      leftEl.className = 'app-left';

      const iconEl = document.createElement('span');
      iconEl.className = 'app-icon';

      if (typeof app.icon === 'string' && app.icon.trim().toLowerCase().startsWith('http')) {
        const img = document.createElement('img');
        img.src = app.icon;
        img.alt = `${app.name} logo`;
        iconEl.appendChild(img);
      } else {
        iconEl.textContent = app.icon || '📦';
      }

      const infoEl = document.createElement('span');
      infoEl.className = 'app-info';

      const nameEl = document.createElement('span');
      nameEl.className = 'app-name';
      nameEl.title = app.name;
      nameEl.textContent = app.name;

      infoEl.appendChild(nameEl);

      if (app.size) {
        const sizeEl = document.createElement('span');
        sizeEl.className = 'app-size';
        sizeEl.textContent = app.size;
        infoEl.appendChild(sizeEl);
      }

      leftEl.appendChild(iconEl);
      leftEl.appendChild(infoEl);

      const downloadLink = document.createElement('a');
      downloadLink.className = 'dl-btn';
      downloadLink.href = app.file;
      downloadLink.download = '';
      downloadLink.textContent = 'Download';

      itemEl.appendChild(leftEl);
      itemEl.appendChild(downloadLink);
      listEl.appendChild(itemEl);
    });

    categoryEl.appendChild(titleEl);
    categoryEl.appendChild(listEl);
    container.appendChild(categoryEl);
  });
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