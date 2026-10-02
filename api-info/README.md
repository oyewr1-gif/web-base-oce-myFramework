# 🚀 Standalone REST API (api-info) - Panduan Server Terisolasi

Folder `api-info/` ini adalah aplikasi REST API yang **100% mandiri (*self-contained*)**. Aplikasi ini dapat dioperasikan secara lokal bersama CMS web utama, maupun **diekstrak dan dipindahkan ke server produksi terisolasi** (misalnya pada subdomain `api.domain.com` atau server API khusus).

---

## 📦 Struktur Berkas Mandiri

```text
api-info/
├── config/
│   ├── app.php            # Konfigurasi aplikasi & CORS
│   └── database.php       # Konfigurasi koneksi MySQL terpisah
├── controllers/           # Endpoint handlers (Auth, Post, Category, Setting)
├── core/                  # Engine API mandiri (Autoloader, Router, PDO, Response, Env)
├── database/              # Skema tabel & Installer database khusus API
│   ├── schema.sql
│   ├── seeder.sql
│   └── install.php
├── models/                # Model database API
├── .env.example           # Template konfigurasi aman
├── .env                   # Variabel lingkungan lokal
├── .htaccess              # Apache URL rewrite
├── index.php              # Front-controller tunggal
└── README.md              # Panduan ini
```

---

## 🛠️ Langkah Memindahkan ke Server Produksi Terisolasi

Jika Anda ingin memindahkan API ini ke server terpisah:

### 1. Salin Folder `api-info/`
Salin seluruh isi folder `api-info/` ke direktori webroot di server produksi Anda, misalnya:
- Linux / Nginx / Apache: `/var/www/api/`
- Windows / XAMPP: `C:\xampp\htdocs\api\`

### 2. Konfigurasi Variabel Lingkungan (.env)
Salin `.env.example` menjadi `.env` di dalam folder tersebut:
```bash
cp .env.example .env
```
Sesuaikan kredensial database dan origin CORS di berkas `.env`:
```env
API_NAME="Production CMS REST API"
API_VERSION="1.0.0"
API_CORS_ORIGIN="https://www.domainanda.com"

API_DB_DRIVER=mysql
API_DB_HOST=127.0.0.1
API_DB_PORT=3306
API_DB_DATABASE=db_api_produksi
API_DB_USERNAME=user_api
API_DB_PASSWORD=password_rahasia_anda
API_DB_CHARSET=utf8mb4
```

### 3. Inisialisasi Database
Jalankan skrip installer mandiri:
```bash
php database/install.php
```
Skrip ini akan otomatis membuat tabel-tabel yang dibutuhkan dan token API pengujian.

### 4. Menjalankan Server
- **PHP Built-in Server**:
  ```bash
  php -S 0.0.0.0:8080
  ```
- **Apache VirtualHost**:
  Arahkan `DocumentRoot` langsung ke folder `api-info`.
- **Nginx**:
  Gunakan konfigurasi standard PHP-FPM:
  ```nginx
  server {
      listen 80;
      server_name api.domainanda.com;
      root /var/www/api;
      index index.php;

      location / {
          try_files $uri $uri/ /index.php?$query_string;
      }

      location ~ \.php$ {
          include fastcgi_params;
          fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
          fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
      }
  }
  ```

### 5. Hubungkan Front-End CMS
Di server front-end CMS utama, cukup buka `.env` dan arahkan:
```env
API_BASE_URL=https://api.domainanda.com
```
Front-end CMS Anda akan langsung berkomunikasi dengan server REST API yang terisolasi tersebut!
