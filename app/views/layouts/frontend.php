<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Modern PHP MVC CMS') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/logo-icon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/core-ui.css') ?>">
</head>
<body>
    <!-- Header Publik -->
    <header class="site-header">
        <div class="container">
            <nav class="site-nav">
                <a href="<?= base_url() ?>" class="site-brand" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    <img src="<?= site_logo_url($siteLogo ?? null) ?>" alt="<?= e($siteTitle ?? 'MyFrameWork CMS') ?>" style="height: 38px; width: auto; max-width: 220px; object-fit: contain;" class="site-logo">
                </a>
                <ul class="site-links">
                    <li><a href="<?= base_url() ?>">Beranda</a></li>
                    <?php if (is_logged_in()): ?>
                        <li><a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-primary">Dashboard Admin</a></li>
                    <?php else: ?>
                        <li><a href="<?= base_url('auth/login') ?>" class="btn btn-sm btn-outline">Masuk</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Notifikasi Flash -->
    <div class="container mt-3">
        <?php foreach (Session::getFlashes() as $type => $messages): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : e($type) ?>">
                    <span><?= e($msg) ?></span>
                    <button type="button" class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <!-- Konten Utama Halaman -->
    <main class="container mt-3" style="min-height: 60vh;">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container text-center">
            <p><?= e($siteTitle ?? 'Modern PHP MVC CMS') ?> &mdash; Framework Mandiri Berbasis PHP MVC Native</p>
            <small class="text-muted"><?= e($footerText ?? '© 2026 Hak Cipta Dilindungi.') ?></small>
        </div>
    </footer>

    <script src="<?= asset('js/core-ui.js') ?>"></script>
</body>
</html>
