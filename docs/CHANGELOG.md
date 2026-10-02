# 📋 Catatan Riwayat Perubahan (Changelog)

Semua penambahan, modifikasi, dan perbaikan signifikan pada proyek **MyFrameWork** dicatat secara kronologis dalam dokumen ini.

---

## [1.3.1] - 2026-10-02
### Fitur Penggantian Logo pada Pengaturan Situs Web (Khusus Administrator)

#### Ditambahkan (Added)
- **Modul Penggantian Logo di Pengaturan Situs (`/admin/settings`)**:
  - Dukungan unggah berkas logo baru secara langsung via input file (`.png`, `.jpg`, `.jpeg`, `.svg`, `.webp`).
  - Unggahan berkas logo diteruskan secara headless ke endpoint API Media (`/media/upload`) via `ApiClient::uploadMedia()`, dan path file otomatis disimpan ke pengaturan `site_logo`.
  - Dukungan input URL / path kustom untuk fleksibilitas referensi logo eksternal atau galeri media yang sudah ada.
  - Fitur **Pratinjau Langsung (Live Preview)**: Pengguna dapat langsung melihat preview logo sebelum formulir disimpan menggunakan JavaScript FileReader.
  - Opsi reset/hapus logo kustom (`remove_logo`) untuk mengembalikan logo ke SVG standar sistem kapan saja.
  - Indikator status logo aktif: Menampilkan status apakah situs sedang menggunakan "Logo Kustom Aktif" atau "Logo Standar (SVG Default)".
- **Fungsi Helper Global**:
  - `site_logo_url(?string $customLogo = null, bool $forDark = false): string`: Helper cerdas untuk menyelesaikan URL absolut logo brand, mendukung path relatif `uploads/`, path aset lokal, URL CDN eksternal, dan fallback otomatis ke SVG default sistem.
  - `get_site_setting(string $key, $default = '', bool $fresh = false)`: Helper untuk mengambil konfigurasi situs secara efisien dengan in-memory static cache.

#### Diperbarui (Updated)
- **Pengendali Admin Settings (`app/controllers/admin/Settings.php`)**:
  - Menambahkan penanganan key `site_logo` pada daftar allowed keys.
  - Memproses unggahan berkas logo (`logo_file`) melalui REST API.
  - Memproses aksi pembersihan/reset logo kembali ke bawaan.
- **Tampilan Antarmuka (Views & Layouts)**:
  - `app/views/admin/settings/index.php`: Antarmuka formulir diperluas dengan kartu pengelolaan logo, badge hak akses Administrator, dan script live preview.
  - `app/views/layouts/frontend.php`: Menggunakan `site_logo_url($siteLogo ?? null)` sehingga perubahan logo di admin langsung tercermin di header portal publik.
  - `app/views/layouts/admin.php`: Menggunakan `site_logo_url(null, true)` agar sidebar admin juga menggunakan logo kustom jika diatur.
  - `app/views/auth/login.php`: Menggunakan `site_logo_url()` untuk menampilkan logo kustom pada kartu login.
  - `app/controllers/Post.php`: Mengirimkan variabel `siteLogo` ke view publik (read artikel & filter kategori).
- **Proteksi Otorisasi (RBAC)**:
  - Modul pengaturan situs dan perubahan logo tetap diproteksi ketat hanya untuk user dengan peran Administrator (`requireAdmin()`).
  - Editor artikel diblokir dari mengakses atau mengubah pengaturan (HTTP 403 Forbidden).

---

## [1.3.0] - 2026-10-02
### Integrasi Logo Resmi Brand & Master Pengguna dengan Peran Editor (RBAC)

#### Ditambahkan (Added)
- **Aset Logo Vektor SVG**:
  - `public/assets/images/logo.svg`: Logo utama berwarna modern dengan perpaduan gradien indigo-cyan dan aksen rose-orange untuk tema terang.
  - `public/assets/images/logo-light.svg`: Varian logo terang khusus untuk latar belakang gelap (sidebar admin panel).
  - `public/assets/images/logo-icon.svg`: Favicon dan icon mark kompak mandiri (*standalone vector*).
- **Master Pengguna (Users Management)**:
  - `api-info/controllers/UserController.php`: Endpoint REST API (`/users`) untuk operasi CRUD pengguna (Daftar, Detail, Tambah, Edit, Hapus).
  - `app/controllers/admin/Users.php`: Controller admin panel untuk manajemen pengguna dengan proteksi otorisasi khusus Administrator.
  - `app/views/admin/users/index.php`, `create.php`, `edit.php`: Tampilan antarmuka master pengguna dengan tabel, badge level peran, form tambah & edit, dan petunjuk hak akses.
  - `core/ApiClient.php`: Method baru `getUsers()`, `getUser()`, `createUser()`, `updateUser()`, `deleteUser()`.

#### Diubah & Ditingkatkan (Changed & Enhanced)
- **Role-Based Access Control (RBAC) & Peran Editor**:
  - **Administrator (`admin`)**: Memiliki akses penuh ke seluruh modul sistem (Master Users, Pengaturan Situs, Artikel, Kategori, Media).
  - **Editor Artikel (`editor`)**: Khusus memproduksi dan mengurasi konten (Artikel, Kategori, Media). Menu Master Users dan Pengaturan Situs otomatis disembunyikan dari sidebar, dan akses rute URL langsung dicegah dengan proteksi 403 Forbidden.
  - Proteksi akun mandiri: Administrator dicegah menghapus akun miliknya sendiri saat sedang aktif login.
- **Tampilan Antarmuka (Layouts)**:
  - `app/views/layouts/frontend.php`: Menyematkan logo resmi brand dan favicon pada header navigasi portal publik.
  - `app/views/layouts/admin.php`: Menyematkan logo terang pada sidebar brand, favicon, menu "Master Pengguna" khusus admin, dan badge peran dinamis (`⚡ Administrator` atau `✍️ Editor`) pada topbar profil.
  - `app/views/auth/login.php`: Menampilkan logo brand dan petunjuk kredensial akun bawaan untuk Administrator dan Editor.
  - `api-info/controllers/SettingController.php`: Menambahkan dukungan pengaturan `site_logo` dan proteksi `requireAdmin()`.

---

## [1.2.1] - 2026-10-02
### Perbaikan Otentikasi Unggah Media & Auto-Healing Sesi REST API

#### Diperbaiki (Fixed)
- **Token Transmission Multi-Channel**: Pada saat mengunggah file media (`multipart/form-data`), header HTTP terkadang dapat dipangkas oleh server Apache/FastCGI. `core/ApiClient.php` kini mengirimkan token otentikasi melalui 3 saluran sekaligus: Header `Authorization: Bearer <token>`, Header `X-API-KEY: <token>`, dan parameter query URL `?api_token=<token>`, serta di dalam body payload form data.
- **Auto-Healing Sesi Pengguna**: Jika browser pengguna masih memiliki sesi login lama (sebelum migrasi REST API di mana `Session::get('api_token')` belum tercipta), `core/Controller.php` dan `core/ApiClient.php` secara otomatis mendeteksi dan menginisialisasi token default tanpa memaksa pengguna logout atau memicu pesan error penolakan akses.
- **Ekstraksi Token API**: `api-info/core/ApiController.php` kini memeriksa parameter `api_token` baik dari Header, URL Query, maupun Form POST body.

---

## [1.2.0] - 2026-10-02
### Transformasi Menjadi 100% Full Headless REST API Client (Zero Direct Database Access)

#### Arsitektur Baru (Architecture Shift)
- **Decoupled Architecture**: Seluruh aplikasi CMS Web (`app/controllers/`) kini beroperasi 100% sebagai REST API Client (*Headless CMS*) tanpa query langsung ke database PDO. 
- Satu-satunya service yang terhubung ke MySQL adalah REST API (`api-info/`).
- Aplikasi web berkomunikasi ke `api-info` via HTTP REST API Client menggunakan token otentikasi Bearer.

#### Ditambahkan (Added)
- `api-info/controllers/DashboardController.php`: Endpoint agregasi statistik sistem (`/dashboard/stats`) untuk menghitung total artikel, draft, kategori, media, dan pengguna.
- `api-info/controllers/MediaController.php`: Endpoint REST API (`/media`) untuk galeri file media dan upload file `multipart/form-data`.
- `api-info/models/ApiMedia.php`: Model mandiri untuk tabel `media` di layer API.
- `core/ApiClient.php`: Peningkatan menyeluruh untuk mendukung seluruh operasi admin: `login()`, `logout()`, `getDashboardStats()`, `getAdminPosts()`, `createPost()`, `updatePost()`, `deletePost()`, `createCategory()`, `deleteCategory()`, `getMedia()`, `uploadMedia()`, `deleteMedia()`, `updateSettings()`, dan error reporting presisi.

#### Diubah (Changed)
- `app/controllers/Auth.php`: Login dan Logout diubah 100% memanggil endpoint API `POST /auth/login` dan `POST /auth/logout`. Token sesi disimpan di `Session::set('api_token')`.
- `app/controllers/admin/Dashboard.php`: Diubah menggunakan `ApiClient::getDashboardStats()` tanpa layer model internal.
- `app/controllers/admin/Posts.php`: Seluruh operasi CRUD artikel dan upload thumbnail diubah memanggil REST API.
- `app/controllers/admin/Categories.php`: CRUD kategori diubah memanggil REST API.
- `app/controllers/admin/Media.php`: Pengelolaan media dan upload diubah memanggil REST API.
- `app/controllers/admin/Settings.php`: Konfigurasi situs diubah memanggil REST API.
- `api-info/.htaccess` & `api-info/core/ApiController.php`: Penanganan header `Authorization: Bearer` untuk kompatibilitas penuh Apache/FastCGI/XAMPP.

---

## [1.1.1] - 2026-10-02
### Peningkatan Deteksi & Penanganan Error Kredensial Database (.env Placeholder Detection)

#### Diperbaiki & Ditingkatkan (Fixed & Enhanced)
- `core/Database.php`: Menambahkan deteksi cerdas jika berkas `.env` di server produksi masih menggunakan nilai template bawaan (`your_db_username` / `your_db_password`). Jika terdeteksi, sistem menampilkan panduan visual interaktif langkah demi langkah untuk mengonfigurasi `.env` alih-alih hanya pesan error fatal MySQL.
- `api-info/core/ApiDatabase.php`: Memberikan respons JSON error informatif jika kredensial `.env` pada REST API masih menggunakan template bawaan.
- `database/install.php` & `api-info/database/install.php`: Menambahkan guard validasi sebelum inisialisasi koneksi PDO untuk mencegah eksekusi installer jika kredensial `.env` belum diisi dengan akun database server nyata.
- `docs/PRODUCTION_DEPLOYMENT.md`: Panduan khusus langkah konfigurasi database server produksi dan checklist migrasi berkas `.env`.

---

## [1.1.0] - 2026-10-02
### Pemisahan Akses Front-End ke REST API & Portabilitas Server API Terisolasi

#### Ditambahkan (Added)
- `core/ApiClient.php`: HTTP Client mandiri (*zero-dependency*) untuk portal front-end mengonsumsi seluruh data artikel, kategori, dan pengaturan dari REST API (`api-info`).
- `api-info/database/`: Paket mandiri skema (`schema.sql`), data seeder (`seeder.sql`), dan installer otomatis (`install.php`) khusus aplikasi REST API.
- `api-info/README.md`: Panduan teknis lengkap cara mengekstrak dan menjalankan aplikasi `api-info/` di server produksi terisolasi (Nginx/Apache/Docker) dengan subdomain terpisah (contoh: `api.domain.com`).
- Konfigurasi `API_BASE_URL` pada `.env` dan `.env.example` untuk memudahkan perpindahan endpoint server API.

#### Diubah (Changed)
- `app/controllers/Home.php` & `app/controllers/Post.php`: Seluruh akses langsung ke layer Model database dihilangkan dan diganti 100% menggunakan `ApiClient` ke REST API.
- `app/views/home/index.php`: Menghilangkan tombol "Buka Admin Panel" dan link "Cek REST API" pada hero section portal pengunjung.
- `app/views/layouts/frontend.php`: Menghapus link teknis REST API dari navigasi publik agar tampilan portal lebih bersih.
- `app/views/admin/dashboard/index.php` & `app/views/layouts/admin.php`: Menambahkan tombol dan menu "Cek REST API" ke Admin Dashboard dan sidebar sistem.
- `api-info/models/ApiPost.php`: Menambahkan filter kategori dinamis pada method `allPublished()` dan penghitungan views otomatis pada `findDetail()`.
- `api-info/controllers/PostController.php`: Mendukung query parameter `?category=slug_or_id`.
- `api-info/controllers/CategoryController.php`: Mendukung pencarian detail kategori berdasarkan slug URL.

#### Diperbaiki (Fixed)
- `api-info/core/ApiModel.php`: Menambahkan method `execute()` untuk menjalankan query pembaruan non-SELECT (UPDATE/DELETE).

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
