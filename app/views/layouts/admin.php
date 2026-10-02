<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin Panel') ?> &mdash; CMS</title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/logo-icon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/core-ui.css') ?>">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Backdrop untuk Mobile -->
        <div class="admin-sidebar-backdrop"></div>

        <!-- Sidebar Admin -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <a href="<?= base_url('admin/dashboard') ?>" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    <img src="<?= site_logo_url(null, true) ?>" alt="CMS Admin Logo" style="height: 32px; width: auto; max-width: 190px; object-fit: contain;">
                </a>
            </div>
            <ul class="sidebar-menu">
                <li class="menu-title">Menu Utama</li>
                <li>
                    <a href="<?= base_url('admin/dashboard') ?>" class="<?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/dashboard') !== false ? 'active' : '' ?>">
                        <span>📊 Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/posts') ?>" class="<?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/posts') !== false ? 'active' : '' ?>">
                        <span>📝 Artikel & Halaman</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/categories') ?>" class="<?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/categories') !== false ? 'active' : '' ?>">
                        <span>📁 Kategori</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/media') ?>" class="<?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/media') !== false ? 'active' : '' ?>">
                        <span>🖼️ Media & File</span>
                    </a>
                </li>

                <li class="menu-title">Sistem</li>
                <?php if (is_admin()): ?>
                    <li>
                        <a href="<?= base_url('admin/users') ?>" class="<?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/users') !== false ? 'active' : '' ?>">
                            <span>👥 Master Pengguna</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/settings') ?>" class="<?= strpos($_SERVER['REQUEST_URI'] ?? '', 'admin/settings') !== false ? 'active' : '' ?>">
                            <span>⚙️ Pengaturan Situs</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="<?= base_url('api-info') ?>" target="_blank">
                        <span>🚀 Cek REST API &nearr;</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url() ?>" target="_blank">
                        <span>🌐 Lihat Website &nearr;</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('auth/logout') ?>" style="color: #fca5a5;">
                        <span>🚪 Keluar (Logout)</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-main">
            <!-- Topbar -->
            <header class="admin-topbar">
                <div class="d-flex align-center gap-3">
                    <button type="button" id="sidebar-toggle" class="btn btn-sm btn-secondary" style="display: inline-flex;">
                        ☰ Menu
                    </button>
                    <h3 style="margin: 0; font-size: 1.15rem;"><?= e($pageTitle ?? 'Dashboard') ?></h3>
                </div>

                <div class="d-flex align-center gap-3">
                    <?php $user = current_user(); ?>
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                        <span class="badge badge-primary">⚡ Administrator</span>
                    <?php elseif (($user['role'] ?? '') === 'editor'): ?>
                        <span class="badge badge-success">✍️ Editor</span>
                    <?php else: ?>
                        <span class="badge badge-secondary"><?= e(ucfirst($user['role'] ?? 'User')) ?></span>
                    <?php endif; ?>
                    <span class="font-semibold" style="font-size: 0.9rem;"><?= e($user['name'] ?? 'User') ?></span>
                </div>
            </header>

            <!-- Admin Body -->
            <main class="admin-content">
                <!-- Flash Messages -->
                <?php foreach (Session::getFlashes() as $type => $messages): ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="alert alert-<?= $type === 'error' ? 'danger' : e($type) ?>">
                            <span><?= e($msg) ?></span>
                            <button type="button" class="alert-close">&times;</button>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <script src="<?= asset('js/core-ui.js') ?>"></script>
</body>
</html>
