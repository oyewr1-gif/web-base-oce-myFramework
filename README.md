# 🚀 Framework CMS PHP MVC (Zero-Dependency) & Standalone REST API

Framework CMS modern, ringan, dan mandiri yang dibangun murni menggunakan **PHP Native tanpa Composer sama sekali (100% Zero-Dependency)**. Dilengkapi dengan arsitektur **Convention over Configuration**, **Custom CSS Library internal**, antarmuka Admin Panel lengkap, dan **Standalone REST API** terpisah dalam direktori `api-info/`.

---

## 📑 Daftar Isi
- [Arsitektur Sistem (Headless CMS)](#-arsitektur-sistem-headless-cms)
- [Fitur Utama](#-fitur-utama)
- [Struktur Direktori](#-struktur-direktori)
- [Dokumentasi Lengkap (Folder docs/)](#-dokumentasi-lengkap-folder-docs)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Konfigurasi & Instalasi Database](#-konfigurasi--instalasi-database)
- [Cara Menjalankan Server](#-cara-menjalankan-server)
- [Konvensi Routing & URL](#-konvensi-routing--url)
- [Panduan Pengembangan (MVC)](#-panduan-pengembangan-mvc)
- [Custom CSS Library (CoreUI)](#-custom-css-library-coreui)
- [Dokumentasi Standalone REST API (api-info)](#-dokumentasi-standalone-rest-api-api-info)

---

## 🏗️ Arsitektur Sistem (Headless CMS)

Aplikasi CMS Web mengadopsi arsitektur **Headless Client** (Decoupled Architecture). Seluruh aplikasi web utama (baik portal pengunjung publik, autentikasi login, maupun panel admin) beroperasi 100% sebagai REST API Client via `core/ApiClient.php` tanpa akses query database langsung. Satu-satunya service yang terhubung ke server MySQL adalah aplikasi REST API (`api-info/`).

```mermaid
flowchart TD
    subgraph Frontend["CMS Web Application (Zero Database Access)"]
        A["Pengunjung (Portal Berita)"] -->|HTTP Request| AC["core/ApiClient.php"]
        B["Halaman Login (/auth/login)"] -->|POST /auth/login| AC
        C["Admin Dashboard (/admin/*)"] -->|Bearer Token API| AC
    end

    subgraph BackendAPI["Standalone REST API Engine (api-info/)"]
        AC -->|JSON API Requests| R["api-info/core/ApiRouter.php"]
        R --> C1["AuthController"]
        R --> C2["DashboardController"]
        R --> C3["PostController"]
        R --> C4["CategoryController"]
        R --> C5["MediaController"]
        R --> C6["SettingController"]
    end

    subgraph Storage["Database Server"]
        BackendAPI -->|PDO Connection (api-info/core/ApiDatabase.php)| DB[("MySQL / MariaDB Database")]
    end
```

---

## ✨ Fitur Utama

1. **Zero-Dependency (Tanpa Composer)**:
   - Berjalan murni dengan PHP native standard library.
   - Dilengkapi SPL Autoloader kustom yang otomatis memuat Core, Model, dan Controller.
2. **Convention over Configuration Routing**:
   - URL `/{controller}/{method}/{param1}/{param2}` otomatis dipetakan ke class dan fungsi tanpa perlu mendaftarkan route satu per satu.
   - Mendukung subfolder controller khusus admin: `/admin/{controller}/{method}/{param1}`.
3. **Database Model PDO Bawaan**:
   - CRUD helper instan: `find($id)`, `all()`, `where()`, `firstWhere()`, `insert()`, `update()`, `delete()`, `count()`, dan `query()` untuk prepared statement aman.
4. **View Engine dengan Master Layout**:
   - Output buffering (`ob_start()`) untuk menyuntikkan konten ke master layout modular (`frontend.php`, `admin.php`, `auth.php`).
5. **Full Starter CMS**:
   - Autentikasi sesi aman (`password_hash`), proteksi Auth Guard & Admin Guard.
   - Dashboard Admin dengan ringkasan statistik.
   - CRUD Artikel & Halaman (slug otomatis, status draft/publish, thumbnail).
   - CRUD Kategori Artikel.
   - Media / File Manager (unggah gambar & dokumen, validasi ekstensi, salin URL).
   - Pengaturan Situs Web dinamis (Site Title, Tagline, Email, Footer).
   - Portal publik responsif siap pakai.
6. **Custom CSS Library Internal (100% Buatan Sendiri)**:
   - `public/assets/css/core-ui.css` tanpa ketergantungan CDN eksternal.
   - Lengkap dengan layout grid/flexbox, stat cards, data tables, forms, badges, buttons, alerts, dan sidebar responsif.
7. **Standalone REST API (`api-info/`)**:
   - Aplikasi mandiri dalam folder `api-info/` dengan konfigurasi terpisah.
   - Respon JSON terstandarisasi dengan dukungan CORS.
   - Proteksi endpoint dengan Bearer Token / API Key.

---

## 📂 Struktur Direktori

```text
myFrameWork/
├── app/                              # Aplikasi Utama (CMS Web & Admin)
│   ├── config/
│   │   ├── app.php                   # Pengaturan aplikasi & timezone
│   │   └── database.php              # Koneksi MySQL (localhost, info, Info123*)
│   ├── controllers/
│   │   ├── Home.php                  # Beranda portal publik
│   │   ├── Post.php                  # Baca artikel & kategori publik
│   │   ├── Auth.php                  # Login & Logout sesi
│   │   └── admin/                    # Controller subfolder Admin Panel
│   │       ├── Dashboard.php         # /admin/dashboard
│   │       ├── Posts.php             # /admin/posts (CRUD Artikel)
│   │       ├── Categories.php        # /admin/categories (CRUD Kategori)
│   │       ├── Media.php             # /admin/media (Upload & Galeri File)
│   │       └── Settings.php          # /admin/settings (Pengaturan Web)
│   ├── models/
│   │   ├── User.php                  # Model pengguna & autentikasi
│   │   ├── Post.php                  # Model artikel & relasi
│   │   ├── Category.php              # Model kategori
│   │   ├── Media.php                 # Model file media
│   │   └── Setting.php               # Model pengaturan website
│   └── views/
│       ├── layouts/                  # Master Layouts
│       │   ├── frontend.php
│       │   ├── admin.php
│       │   └── auth.php
│       ├── home/index.php
│       ├── post/read.php
│       ├── auth/login.php
│       └── admin/
│           ├── dashboard/index.php
│           ├── posts/index.php, create.php, edit.php
│           ├── categories/index.php
│           ├── media/index.php
│           └── settings/index.php
├── core/                             # Engine Inti Framework
│   ├── Autoloader.php                # SPL Autoloader tanpa Composer
│   ├── Router.php                    # Convention Router & Subfolder Admin
│   ├── Controller.php                # Base Controller (view, model, redirect, auth)
│   ├── Model.php                     # Base Model (PDO CRUD Helper)
│   ├── Database.php                  # Singleton PDO Connection
│   ├── Request.php                   # Input HTTP & validasi CSRF
│   ├── Response.php                  # Response status & redirect
│   ├── Session.php                   # Sesi & Flash Notifications
│   └── Helper.php                    # Helper global (base_url, asset, e, slugify)
├── public/                           # Web Root CMS
│   ├── index.php                     # Front Controller Utama
│   ├── .htaccess                     # URL Rewrite Apache
│   ├── uploads/                      # Direktori upload media
│   └── assets/
│       ├── css/core-ui.css           # Custom CSS Library internal
│       └── js/core-ui.js             # Micro JS Helper
├── database/
│   ├── schema.sql                    # Skema tabel MySQL lengkap
│   ├── seeder.sql                    # Data seeder awal
│   └── install.php                   # Skrip installer otomatis
├── api-info/                         # APLIKASI STANDALONE REST API
│   ├── config/
│   │   ├── app.php                   # Konfigurasi REST API & CORS
│   │   └── database.php              # Konfigurasi database mandiri API
│   ├── core/
│   │   ├── ApiAutoloader.php
│   │   ├── ApiDatabase.php
│   │   ├── ApiModel.php
│   │   ├── ApiController.php
│   │   ├── ApiResponse.php
│   │   └── ApiRouter.php
│   ├── controllers/
│   │   ├── AuthController.php        # POST /auth/login, GET /auth/me
│   │   ├── PostController.php        # CRUD artikel JSON
│   │   ├── CategoryController.php    # GET /categories
│   │   └── SettingController.php     # GET /settings
│   ├── models/
│   │   ├── ApiUser.php
│   │   ├── ApiPost.php
│   │   ├── ApiCategory.php
│   │   └── ApiSetting.php
│   ├── index.php                     # Entrypoint mandiri REST API
│   └── .htaccess
├── .htaccess                         # Root Apache Rewrite
└── README.md
```

---

## 💻 Persyaratan Sistem

- PHP 7.4 atau PHP 8.0, 8.1, 8.2, 8.3+
- Ekstensi PHP: `pdo`, `pdo_mysql`, `mbstring`, `fileinfo`
- Database: MySQL atau MariaDB
- Web Server: Apache (dengan `mod_rewrite`) atau PHP Built-in Server

---

## 🗄️ Konfigurasi & Instalasi Database

Sesuai permintaan Anda, konfigurasi default telah diatur sebagai berikut:
- **Host**: `localhost`
- **Port**: `3306`
- **Database**: `cms_framework`
- **Username**: `info`
- **Password**: `Info123*`

### Menjalankan Skrip Auto-Installer
Cukup jalankan satu perintah berikut di terminal:
```bash
php database/install.php
```

Skrip ini akan otomatis:
1. Membuat database `cms_framework` di MySQL.
2. Mengeksekusi tabel `users`, `categories`, `posts`, `media`, `settings`, `api_tokens`.
3. Memasukkan data awal (seeder).

### Kredensial Login Default
- **URL Login**: `http://localhost:8000/auth/login`
- **Email**: `admin@cms.local`
- **Username**: `admin`
- **Password**: `admin123`

---

## 🌐 Cara Menjalankan Server

### Opsi 1: PHP Built-in Web Server (Paling Cepat)
Jalankan dari direktori root proyek `myFrameWork`:
```bash
# Menjalankan seluruh aplikasi (Web CMS + REST API api-info/)
php -S localhost:8000
```
- **Web Utama & Admin**: Buka `http://localhost:8000/public/`
- **REST API**: Buka `http://localhost:8000/api-info/`

Atau jika ingin menjalankan web langsung dari root `public/`:
```bash
php -S localhost:8000 -t public
```

### Opsi 2: Menggunakan Apache (XAMPP / Laragon / Nginx)
Letakkan folder `myFrameWork` ke dalam direktori `htdocs` atau `www`. File `.htaccess` di root proyek akan secara otomatis meneruskan lalu lintas web ke `public/` dan request `/api-info/` ke aplikasi REST API.

---

## 🧭 Konvensi Routing & URL

Framework ini menggunakan sistem **Convention over Configuration**:

| URL Request | Controller yang Dipanggil | Method | Parameter |
| :--- | :--- | :--- | :--- |
| `/` | `app/controllers/Home.php` | `index()` | - |
| `/post/read/judul-slug` | `app/controllers/Post.php` | `read()` | `['judul-slug']` |
| `/post/category/teknologi` | `app/controllers/Post.php` | `category()` | `['teknologi']` |
| `/auth/login` | `app/controllers/Auth.php` | `login()` | - |
| `/auth/logout` | `app/controllers/Auth.php` | `logout()` | - |
| `/admin` | `app/controllers/admin/Dashboard.php` | `index()` | - |
| `/admin/posts` | `app/controllers/admin/Posts.php` | `index()` | - |
| `/admin/posts/create` | `app/controllers/admin/Posts.php` | `create()` | - |
| `/admin/posts/edit/5` | `app/controllers/admin/Posts.php` | `edit()` | `[5]` |
| `/admin/categories` | `app/controllers/admin/Categories.php`| `index()` | - |
| `/admin/media` | `app/controllers/admin/Media.php` | `index()` | - |
| `/admin/settings` | `app/controllers/admin/Settings.php` | `index()` | - |

---

## 🛠️ Panduan Pengembangan (MVC)

### 1. Membuat Model Baru
Cukup extend class `Model`. CRUD helper otomatis aktif:
```php
<?php
// app/models/Product.php
class Product extends Model
{
    protected string $table = 'products'; // Opsional, default nama class + 's'
}

// Penggunaan di controller:
$productModel = $this->model('Product');

// 1. Ambil semua data
$items = $productModel->all('price ASC');

// 2. Cari data berdasarkan ID
$item = $productModel->find(1);

// 3. Cari dengan klausa WHERE
$active = $productModel->where("status = :st", ['st' => 'active']);

// 4. Tambah data baru
$newId = $productModel->insert([
    'name'  => 'Produk A',
    'price' => 50000
]);

// 5. Update data
$productModel->update($newId, ['price' => 45000]);

// 6. Hapus data
$productModel->delete($newId);

// 7. Custom Query dengan prepared statement
$results = $productModel->query("SELECT * FROM products WHERE price > :p", ['p' => 20000]);
```

### 2. Membuat Controller Baru
Cukup extend class `Controller`:
```php
<?php
// app/controllers/Product.php
class Product extends Controller
{
    public function index(): void
    {
        $products = $this->model('Product')->all();
        $this->view('product/index', [
            'products' => $products
        ], 'layouts/frontend');
    }

    public function detail(string $id = ''): void
    {
        $item = $this->model('Product')->find((int)$id);
        if (!$item) {
            Response::notFound();
        }
        $this->view('product/detail', ['item' => $item], 'layouts/frontend');
    }
}
```

### 3. Proteksi Halaman Admin
Untuk memproteksi controller khusus user yang sudah login atau role admin:
```php
class MyProtectedController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();  // Wajib login
        // atau
        $this->requireAdmin(); // Wajib role 'admin'
    }
}
```

---

## 🎨 Custom CSS Library (CoreUI)

Library CSS internal (`public/assets/css/core-ui.css`) dibuat 100% tanpa library eksternal.

Beberapa komponen yang tersedia:
- **Grid Layout**: `.grid`, `.grid-2`, `.grid-3`, `.grid-4`
- **Flexbox**: `.d-flex`, `.align-center`, `.justify-between`, `.gap-1` s/d `.gap-4`
- **Kartu Statistik**:
  ```html
  <div class="stat-card">
      <div class="stat-icon primary">📊</div>
      <div>
          <div class="stat-val">120</div>
          <div class="stat-label">Total Data</div>
      </div>
  </div>
  ```
- **Tabel Responsif**: `.table-responsive`, `.table`, `.table-hover`
- **Tombol**: `.btn-primary`, `.btn-secondary`, `.btn-success`, `.btn-danger`, `.btn-outline`, `.btn-sm`
- **Badges**: `.badge-primary`, `.badge-success`, `.badge-warning`, `.badge-danger`, `.badge-info`
- **Alert Flash**: `.alert-success`, `.alert-danger`, `.alert-warning`, `.alert-info`

---

## 📡 Dokumentasi Standalone REST API (`api-info`)

Aplikasi REST API terletak terpisah pada folder `api-info/` dengan konfigurasi mandiri `api-info/config/database.php`.

### Base URL API
`http://localhost:8000/api-info/`

### Format Response Standar
```json
{
  "status": "success",
  "code": 200,
  "message": "Daftar artikel berhasil diambil.",
  "data": [ ... ],
  "meta": {
    "page": 1,
    "limit": 10,
    "total_items": 2,
    "total_pages": 1
  }
}
```

### Daftar Endpoint

| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :--- | :--- |
| `GET` | `/posts` | Publik | Ambil daftar artikel (`?page=1&limit=10`) |
| `GET` | `/posts/read/{id_or_slug}` | Publik | Ambil detail satu artikel |
| `POST` | `/posts/create` | **Token Auth** | Tambah artikel baru |
| `PUT` | `/posts/update/{id}` | **Token Auth** | Update data artikel |
| `DELETE` | `/posts/delete/{id}` | **Token Auth** | Hapus artikel |
| `GET` | `/categories` | Publik | Ambil daftar kategori artikel |
| `GET` | `/settings` | Publik | Ambil pengaturan umum situs |
| `POST` | `/auth/login` | Publik | Login & peroleh Bearer Token |
| `GET` | `/auth/me` | **Token Auth** | Profil pengguna pemilik token |

### Autentikasi REST API
Kirim token melalui salah satu header berikut:
```http
Authorization: Bearer <token_anda>
```
atau
```http
X-API-KEY: <token_anda>
```

#### Token Testing Bawaan:
```
test-token-cms-info-2026
```

#### Contoh Request Login via cURL:
```bash
curl -X POST http://localhost:8000/api-info/auth/login \
  -H "Content-Type: application/json" \
  -d '{"identity": "admin@cms.local", "password": "admin123"}'
```

#### Contoh Request Tambah Artikel (Protected):
```bash
curl -X POST http://localhost:8000/api-info/posts/create \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer test-token-cms-info-2026" \
  -d '{
    "title": "Artikel Baru via API",
    "content": "<p>Konten artikel yang dikirim melalui REST API terpisah.</p>",
    "category_id": 1,
    "status": "published"
  }'
```

---

## 🔒 Keamanan Bawaan
- **Prepared Statements PDO**: Mencegah serangan SQL Injection.
- **CSRF Token Protection**: Dilengkapi generator dan validasi token pada form web.
- **XSS Protection**: Fungsi escaping `e($string)` untuk membersihkan output HTML.
- **Secure Password Hashing**: Menggunakan algoritma standar industri `PASSWORD_BCRYPT`.
- **Upload Validation**: Pembatasan ekstensi file ketat dan sanitasi nama file acak.
