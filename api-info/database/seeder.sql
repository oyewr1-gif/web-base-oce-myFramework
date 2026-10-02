USE `cms_framework`;

-- Seed Akun Admin API (Password: admin123)
INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `role`) VALUES
(1, 'admin', 'Administrator Utama', 'admin@cms.local', '$2y$10$tZz2Y7l9QjD9aY3sVlWgeOCp6U3hGzXoR4p7vH1y1pW8m9qK1e4yG', 'admin')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Seed Kategori
INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Teknologi', 'teknologi', 'Artikel dan berita seputar perkembangan dunia teknologi dan pemrograman.'),
(2, 'Tutorial', 'tutorial', 'Panduan teknis langkah demi langkah untuk developer.'),
(3, 'Pengumuman', 'pengumuman', 'Informasi dan pembaruan seputar situs web.')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Seed Artikel Sampel
INSERT INTO `posts` (`id`, `user_id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `status`, `views`) VALUES
(1, 1, 1, 'Selamat Datang di Framework CMS PHP MVC', 'selamat-datang-di-framework-cms-php-mvc', 'Framework baru yang ringan, zero-dependency, dan didesain khusus untuk performa tinggi.', '<p>Selamat datang di kerangka <strong>Framework CMS PHP MVC</strong> baru Anda!</p><p>Framework ini dibangun murni menggunakan PHP native tanpa dependensi eksternal dari Composer. Seluruh core engine dibuat secara modular dan terstruktur rapi.</p>', 'published', 15),
(2, 1, 2, 'Panduan Mengakses Standalone REST API (api-info)', 'panduan-mengakses-standalone-rest-api-api-info', 'Pelajari cara mengonsumsi endpoint data JSON melalui aplikasi terpisah api-info.', '<p>Aplikasi REST API diletakkan terpisah pada folder <code>api-info/</code> di dalam proyek ini.</p><p>Aplikasi ini dapat dipindahkan ke server produksi terisolasi dengan sangat mudah.</p>', 'published', 8)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- Seed Pengaturan Situs/API
INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'site_title', 'Modern PHP MVC CMS'),
(2, 'site_tagline', 'Framework Ringan, Cepat, dan Mandiri'),
(3, 'admin_email', 'admin@cms.local'),
(4, 'footer_text', '© 2026 Modern PHP MVC CMS. Hak Cipta Dilindungi.')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);

-- Seed Token API Default
INSERT INTO `api_tokens` (`id`, `user_id`, `token`, `name`) VALUES
(1, 1, 'test-token-cms-info-2026', 'Master Testing Token')
ON DUPLICATE KEY UPDATE `token`=VALUES(`token`);
