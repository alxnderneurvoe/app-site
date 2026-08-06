<?php
$appsDir = __DIR__ . '/apps';
$files = [];

if (is_dir($appsDir)) {
    $items = scandir($appsDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === 'index.html') continue;
        $fullPath = $appsDir . '/' . $item;
        if (is_file($fullPath)) {
            $files[] = [
                'name' => $item,
                'size' => filesize($fullPath),
                'modified' => filemtime($fullPath),
            ];
        }
    }
    // Urutkan berdasarkan nama, A-Z
    usort($files, fn($a, $b) => strcasecmp($a['name'], $b['name']));
}

function formatBytes($bytes) {
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

function iconForFile($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $map = [
        'exe' => 'fa-brands fa-windows',
        'msi' => 'fa-brands fa-windows',
        'dmg' => 'fa-brands fa-apple',
        'pkg' => 'fa-brands fa-apple',
        'zip' => 'fa-solid fa-file-zipper',
        'rar' => 'fa-solid fa-file-zipper',
        '7z'  => 'fa-solid fa-file-zipper',
        'deb' => 'fa-brands fa-linux',
        'apk' => 'fa-brands fa-android',
        'iso' => 'fa-solid fa-compact-disc',
    ];
    return $map[$ext] ?? 'fa-solid fa-file-arrow-down';
}

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $files = array_values(array_filter($files, fn($f) => stripos($f['name'], $search) !== false));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apps Download Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="page-wrap">
        <div class="main-card">
            <div class="main-header">
                <h1><i class="fas fa-cloud-arrow-down"></i> Apps Download Center</h1>
                <p>Kumpulan installer buat setup device baru</p>
            </div>

            <div class="main-body">
                <form method="GET" class="search-form">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Cari aplikasi..." value="<?= htmlspecialchars($search) ?>">
                        <?php if ($search !== ''): ?>
                            <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-xmark"></i></a>
                        <?php endif; ?>
                    </div>
                </form>

                <?php if (empty($files)): ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p><?= $search !== '' ? 'Nggak ada file yang cocok dengan pencarian.' : 'Belum ada file di folder apps/' ?></p>
                    </div>
                <?php else: ?>
                    <div class="app-count"><?= count($files) ?> file tersedia</div>
                    <div class="file-list">
                        <?php foreach ($files as $file): ?>
                            <a href="apps/<?= rawurlencode($file['name']) ?>" download class="file-item">
                                <div class="file-icon">
                                    <i class="<?= iconForFile($file['name']) ?>"></i>
                                </div>
                                <div class="file-info">
                                    <div class="file-name"><?= htmlspecialchars($file['name']) ?></div>
                                    <div class="file-meta">
                                        <?= formatBytes($file['size']) ?> &middot;
                                        <?= date('d M Y', $file['modified']) ?>
                                    </div>
                                </div>
                                <div class="file-download">
                                    <i class="fas fa-download"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
