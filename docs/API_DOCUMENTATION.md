# 📡 Dokumentasi Standalone REST API (`api-info`)

Aplikasi REST API diletakkan pada folder mandiri `api-info/` dalam proyek, memiliki konfigurasi database tersendiri, dan menghasilkan output JSON terstandarisasi dengan dukungan CORS.

---

## 🔑 Informasi Dasar

- **Base URL Lokal**: `http://localhost/myFramework/api-info/` (atau `http://localhost:8000/api-info/`)
- **Format Konten**: `application/json; charset=UTF-8`
- **Header CORS**: Diizinkan secara default (`*`) untuk integrasi SPA (React, Vue, Flutter, Next.js).
- **Master Testing Token Bawaan**:
  ```
  test-token-cms-info-2026
  ```

---

## 🔒 Autentikasi API

Endpoint yang membutuhkan proteksi token ditandai dengan **[Auth Required]**.
Kirim token melalui salah satu format header berikut:

```http
Authorization: Bearer test-token-cms-info-2026
```
atau
```http
X-API-KEY: test-token-cms-info-2026
```

---

## 📑 Daftar Endpoint

### 1. Autentikasi Pengguna

#### `POST /auth/login`
Memverifikasi kredensial pengguna dan mengembalikan Bearer Token baru.

- **Request Body (JSON)**:
  ```json
  {
    "identity": "admin@cms.local",
    "password": "admin123"
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "status": "success",
    "code": 200,
    "message": "Login berhasil. Gunakan access_token pada header Authorization.",
    "data": {
      "token_type": "Bearer",
      "access_token": "a1b2c3d4e5f6...",
      "user": {
        "id": 1,
        "username": "admin",
        "name": "Administrator Utama",
        "email": "admin@cms.local",
        "role": "admin"
      }
    }
  }
  ```

#### `GET /auth/me` [Auth Required]
Mengambil informasi pengguna dari token yang dikirim.

---

### 2. Manajemen Artikel (Posts)

#### `GET /posts`
Mengambil daftar artikel dengan status `published`.

- **Query Parameters**:
  - `page` (integer, default: 1): Nomor halaman.
  - `limit` (integer, default: 10): Jumlah item per halaman.
- **Response (200 OK)**:
  ```json
  {
    "status": "success",
    "code": 200,
    "message": "Daftar artikel berhasil diambil.",
    "data": [
      {
        "id": 1,
        "title": "Selamat Datang di Framework CMS PHP MVC",
        "slug": "selamat-datang-di-framework-cms-php-mvc",
        "excerpt": "Framework baru yang ringan, zero-dependency...",
        "thumbnail": null,
        "status": "published",
        "views": 15,
        "created_at": "2026-10-01 16:00:00",
        "author_name": "Administrator Utama",
        "category_name": "Teknologi"
      }
    ],
    "meta": {
      "page": 1,
      "limit": 10,
      "total_items": 2,
      "total_pages": 1
    }
  }
  ```

#### `GET /posts/read/{id_atau_slug}`
Mengambil satu detail artikel berdasarkan ID atau URL slug.
- Contoh: `/posts/read/1` atau `/posts/read/selamat-datang-di-framework-cms-php-mvc`

#### `POST /posts/create` [Auth Required]
Membuat artikel baru ke dalam sistem.

- **Request Body (JSON)**:
  ```json
  {
    "title": "Inovasi Baru di Era Digital",
    "content": "<p>Isi lengkap dari artikel baru...</p>",
    "category_id": 1,
    "status": "published",
    "excerpt": "Ringkasan artikel opsional"
  }
  ```
- **Response (201 Created)**:
  ```json
  {
    "status": "success",
    "code": 201,
    "message": "Artikel berhasil dibuat.",
    "data": {
      "id": 3,
      "title": "Inovasi Baru di Era Digital",
      "slug": "inovasi-baru-di-era-digital-1727773200",
      ...
    }
  }
  ```

#### `PUT /posts/update/{id}` [Auth Required]
Memperbarui artikel yang sudah ada.

#### `DELETE /posts/delete/{id}` [Auth Required]
Menghapus artikel dari database.

---

### 3. Kategori & Pengaturan

#### `GET /categories`
Mengambil semua kategori yang tersedia beserta jumlah total artikel terkait.

#### `GET /settings`
Mengambil informasi konfigurasi publik situs web (`site_title`, `site_tagline`, `footer_text`).
