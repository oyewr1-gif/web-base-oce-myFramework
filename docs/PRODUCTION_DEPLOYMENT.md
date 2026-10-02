# 🚀 Panduan Deployment Server Produksi & Konfigurasi Database

Panduan ini menjelaskan langkah demi langkah cara mengonfigurasi dan menyelesaikan masalah koneksi database di server produksi (Hosting cPanel, VPS Linux, ataupun Server Windows/IIS).

---

## 1. Penyebab Error `Access denied for user 'your_db_username'@'localhost'`

Jika Anda melihat pesan error:
```text
Database Connection Error
SQLSTATE[HY000] [1045] Access denied for user 'your_db_username'@'localhost' (using password: YES)
```

**Penyebabnya adalah:**
Ketika proyek di-*clone* atau diunggah ke server produksi, berkas `.env` disalin dari `.env.example`. Nilai bawaan pada berkas tersebut adalah placeholder:
- `DB_USERNAME=your_db_username`
- `DB_PASSWORD=your_db_password`

Meskipun database Anda sudah dibuat, MySQL menolak koneksi karena user `your_db_username` tidak ada atau password-nya tidak cocok dengan akun MySQL di server produksi Anda.

---

## 2. Solusi Langkah Demi Langkah

### Langkah 1: Buka Berkas `.env` di Server Produksi
Buka berkas `.env` yang berada di folder root aplikasi Anda:
```bash
nano .env
# atau melalui cPanel File Manager -> Edit .env
```
*(Catatan: Di cPanel File Manager, pastikan centang opsi **"Show Hidden Files (dotfiles)"** agar file `.env` terlihat).*

### Langkah 2: Sesuaikan Kredensial Database
Ubah baris kredensial database sesuai dengan nama database, user, dan password yang telah Anda buat di server:

```env
# ========================================================
# KONEKSI DATABASE UTAMA (MySQL / MariaDB)
# ========================================================
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database_produksi_anda
DB_USERNAME=nama_user_mysql_anda
DB_PASSWORD=password_user_mysql_anda
DB_CHARSET=utf8mb4
```

> **Tips Hosting cPanel:**
> Biasanya cPanel menambahkan prefix username akun hosting pada nama database dan user, misalnya:
> - `DB_DATABASE=usercpanel_cms`
> - `DB_USERNAME=usercpanel_cmsuser`
> - `DB_PASSWORD=PasswordKuatAnda123!`

### Langkah 3: Pastikan User Memiliki Privileges (Hak Akses)
Jika Anda membuat user baru di MySQL, pastikan user tersebut telah diberikan izin penuh ke database tersebut:

**Melalui cPanel:**
1. Masuk ke menu **MySQL Databases**.
2. Di bagian **Add User To Database**, pilih User dan Database Anda.
3. Klik **Add**, lalu centang **ALL PRIVILEGES** dan klik **Make Changes**.

**Melalui CLI / phpMyAdmin SQL (VPS / Dedicated Server):**
```sql
GRANT ALL PRIVILEGES ON nama_database_produksi_anda.* TO 'nama_user_mysql_anda'@'localhost';
FLUSH PRIVILEGES;
```

### Langkah 4: Isi Skema dan Data Seeder
Jika database sudah dibuat namun tabel-tabelnya masih kosong, jalankan salah satu dari dua cara berikut:

**Cara A: Melalui Command Line (CLI)**
```bash
php database/install.php
```

**Cara B: Melalui Browser Web**
Buka URL installer melalui browser:
```text
https://domain-anda.com/database/install.php
```
*Installer akan otomatis membuat tabel `users`, `categories`, `posts`, `media`, `settings`, `api_tokens` beserta akun default admin.*

---

## 3. Akun Login Default Admin

Setelah installer selesai dijalankan:
- **URL Login Admin:** `https://domain-anda.com/auth/login`
- **Email:** `admin@cms.local` (atau username: `admin`)
- **Password:** `admin123`

> [!WARNING]
> **Penting untuk Keamanan Server Produksi:**
> 1. Segera ubah password admin melalui panel admin setelah berhasil login.
> 2. Ubah `APP_DEBUG=false` dan `APP_ENV=production` pada berkas `.env` agar pesan error sistem tidak terekspos ke publik.
> 3. Jika menggunakan folder `api-info/` terpisah, pastikan berkas `api-info/.env` juga telah disesuaikan kredensialnya.
