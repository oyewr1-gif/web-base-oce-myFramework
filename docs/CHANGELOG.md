# 📋 Catatan Riwayat Perubahan (Changelog)

Semua penambahan, modifikasi, dan perbaikan signifikan pada proyek **MyFrameWork** dicatat secara kronologis dalam dokumen ini.

---

## [1.0.3] - 2026-10-01
### Perbaikan Konflik Nama Class Controller vs Model (Artikel & Media Manager)

#### Diperbaiki (Fixed)
- **Konflik Nama Class `Post`**: Controller `Post` dan Model `Post` memiliki nama yang sama di global namespace, menyebabkan `$this->model('Post')` mengembalikan instance Controller dan memicu error `Call to undefined method Post::findBySlugWithRelations()`. Controller diubah menjadi `PostController`.
- **Konflik Nama Class `Media`**: Controller `Media` dan Model `Media` memicu error fatal PHP `Cannot declare class Media, because the name is already in use`. Controller diubah menjadi `MediaController`.
- **Resolusi Controller Dinamis pada `core/Router.php`**: Router kini secara cerdas mengenali class berakhiran `Controller` maupun nama langsung tanpa mengubah konvensi URL publik sama sekali (`/post/read/...` dan `/admin/media` tetap berjalan normal).
- **Pengamanan `core/Controller.php`**: Method `model()` kini selalu memuat berkas model terkait dan memverifikasi bahwa class yang diinstansiasi merupakan turunan valid dari `Model` (`is_subclass_of($className, 'Model')`).
- **Pembaruan Autoloader `core/Autoloader.php`**: Autoloader kini dapat memetakan class berakhiran `Controller` ke berkas dengan atau tanpa akhiran `Controller`.

#### Diubah (Changed)
- Standardisasi nama class seluruh controller menjadi berakhiran `Controller` (`HomeController`, `AuthController`, `PostController`, `DashboardController`, `PostsController`, `CategoriesController`, `MediaController`, `SettingsController`) untuk memastikan nol konflik dengan layer Model.

---

## [1.0.2] - 2026-10-01
### Keamanan Konfigurasi & Kredensial untuk Git/GitHub (.env & .gitignore)

#### Ditambahkan (Added)
- `core/Env.php`: Loader variabel lingkungan mandiri (*zero-dependency*) untuk membaca file `.env` tanpa Composer.
- `api-info/core/ApiEnv.php`: Loader variabel lingkungan mandiri khusus aplikasi REST API.
- Helper `env($key, $default)` pada `core/Helper.php`.
- `.env.example`: Template konfigurasi aman untuk diunggah ke repository GitHub (tanpa password/kredensial nyata).
- `api-info/.env.example`: Template konfigurasi aman khusus aplikasi REST API.
- `.gitignore`: Memblokir berkas `.env`, berkas unggahan `public/uploads/*`, log, cache, dan file editor IDE dari pelacakan Git.

#### Diubah (Changed)
- `app/config/database.php`: Kredensial database diubah menjadi pemanggilan dinamis `env('DB_USERNAME', 'root')` dan `env('DB_PASSWORD', '')`.
- `api-info/config/database.php`: Kredensial API database diubah menjadi pemanggilan dinamis `ApiEnv::get('API_DB_...')`.
- `database/install.php` & `public/index.php`: Diperbarui untuk memuat class `Env` sebelum membaca konfigurasi database.

---

## [1.0.1] - 2026-10-01
### Perbaikan Rewrite URL Apache Subfolder (XAMPP) & Installer

#### Diperbaiki (Fixed)
- **RewriteBase / di public/.htaccess**: Menghapus `RewriteBase /` yang menyebabkan Apache mengalihkan request subfolder (`/myFramework/`) ke root domain XAMPP (`localhost/dashboard`).
- **Akses Langsung ke Installer**: Memperbarui aturan pada root `.htaccess` agar berkas fisik seperti `database/install.php` dapat diakses langsung tanpa diteruskan secara keliru ke `public/`.
- **Deteksi Path Subfolder**: Memperbarui metode `parseUrl()` pada `core/Router.php` dan helper `base_url()` pada `core/Helper.php` agar mengenali request URL baik dengan maupun tanpa prefiks `/public`.

#### Ditambahkan (Added)
- `install.php`: Shortcut installer di direktori root.
- `public/install.php`: Shortcut installer di folder `public/`.

---

## [1.0.0] - 2026-10-01
### Inisialisasi Proyek Framework CMS PHP MVC & Standalone REST API

#### Ditambahkan (Added)
1. **Core Framework Engine (Zero-Dependency)**:
   - `core/Autoloader.php`: SPL Autoloader kustom untuk memuat class otomatis tanpa Composer.
   - `core/Router.php`: Dispatcher URL berbasis konvensi `/{controller}/{method}/{params}` dengan dukungan subfolder `/admin/*`.
   - `core/Controller.php`: Base Controller dengan sistem render master layout melalui output buffering, loader model, dan middleware otorisasi.
   - `core/Model.php`: Base Model dengan helper CRUD instan berbasis PDO (`find`, `all`, `where`, `firstWhere`, `insert`, `update`, `delete`, `count`, `query`).
   - `core/Database.php`: Singleton PDO connection manager dengan proteksi exception.
   - `core/Request.php`: Pengelola input HTTP (GET, POST, FILES), metode HTTP, dan generator & validasi token CSRF.
   - `core/Response.php`: HTTP response status code, header, JSON serializer, dan halaman 404 kustom.
   - `core/Session.php`: Pengelola sesi aman dan flash notifications.
   - `core/Helper.php`: Fungsi global (`base_url`, `asset`, `upload_url`, `e`, `slugify`, `csrf_field`, `current_user`, `is_admin`).

2. **Custom CSS Library Internal (CoreUI)**:
   - `public/assets/css/core-ui.css`: Library CSS internal 100% buatan sendiri tanpa pihak ketiga. Mencakup reset, design tokens, responsive grid, flexbox, stat cards, data tables, forms, badges, alerts, buttons, dan responsive collapsible sidebar.
   - `public/assets/js/core-ui.js`: Micro Vanilla JS untuk penanganan alert close, mobile sidebar toggle, auto-slug generator, dan file preview.

3. **Fitur Starter CMS Web & Admin**:
   - Master Layouts: `app/views/layouts/frontend.php`, `admin.php`, dan `auth.php`.
   - Autentikasi: Login, logout sesi aman (`password_hash`), proteksi `requireAuth()` dan `requireAdmin()`.
   - Dashboard Admin: Statistik total artikel, artikel published, kategori, media, dan user.
   - Manajemen Konten (Posts): CRUD artikel lengkap, upload gambar thumbnail, status publish/draft.
   - Manajemen Kategori: CRUD kategori artikel dengan perhitungan relasi postingan.
   - Media Manager: Pengunggah gambar/file lokal, validasi tipe mime, dan galeri salin URL.
   - Pengaturan Situs: Konfigurasi dinamis nama situs, slogan, email admin, dan footer text.
   - Portal Publik: Beranda responsif, filter kategori, dan pembaca artikel (`/post/read/{slug}`).

4. **Standalone REST API (`api-info/`)**:
   - Terpisah penuh dalam direktori `api-info/` dengan konfigurasi mandiri `api-info/config/database.php`.
   - `api-info/core/`: `ApiAutoloader`, `ApiDatabase`, `ApiModel`, `ApiController`, `ApiResponse`, dan `ApiRouter`.
   - Format standar JSON seragam dengan header CORS otomatis.
   - Proteksi endpoint mutasi via Header `Authorization: Bearer <token>` atau `X-API-KEY`.
   - Controller & Model API: `AuthController`, `PostController`, `CategoryController`, `SettingController`.

5. **Database & Installer**:
   - `database/schema.sql`: Skema MySQL untuk 6 tabel (`users`, `categories`, `posts`, `media`, `settings`, `api_tokens`).
   - `database/seeder.sql`: Data awal pengguna (admin & editor), kategori, artikel sampel, dan master test token.
   - `database/install.php`: Skrip installer CLI / Web sekali jalan untuk pembuatan database otomatis.

6. **Server Deployment**:
   - URL rewrite ganda melalui root `.htaccess`, `public/.htaccess`, dan `api-info/.htaccess`.
   - Sinkronisasi penuh ke server lokal XAMPP (`D:\Server\xampp\htdocs\myFramework`).
   - Lolos uji verifikasi sintaks PHP 8.0 tanpa error.
