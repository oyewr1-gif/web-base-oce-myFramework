# 🎨 Dokumentasi CoreUI CSS Library

`core-ui.css` adalah sistem desain internal yang dibuat 100% dari nol tanpa bantuan library atau CDN eksternal (*zero-dependency*). File berlokasi di:
`public/assets/css/core-ui.css`

---

## 1. Variabel Desain (CSS Tokens)

Anda dapat mengubah tema warna di baris teratas file CSS:

```css
:root {
  --primary: #4f46e5;         /* Indigo Utama */
  --primary-hover: #4338ca;
  --secondary: #64748b;       /* Slate Netral */
  --success: #10b981;         /* Emerald Sukses */
  --danger: #ef4444;          /* Rose Bahaya/Hapus */
  --warning: #f59e0b;         /* Amber Peringatan */
  --info: #0284c7;            /* Sky Informasi */
  --bg-page: #f8fafc;         /* Background halaman */
  --bg-card: #ffffff;         /* Background elemen kartu */
  --border-color: #e2e8f0;    /* Garis pemisah */
  --radius-md: 0.5rem;        /* Kelengkungan sudut tombol & input */
}
```

---

## 2. Kelas Grid & Layout

### Responsive Grid
- `.grid`: Menerapkan CSS Grid bawaan.
- `.grid-2`: 2 Kolom (1 kolom pada layar mobile).
- `.grid-3`: 3 Kolom (1 kolom pada layar mobile).
- `.grid-4`: 4 Kolom (2 kolom di tablet, 1 kolom di mobile).

### Flexbox Utility
- `.d-flex`: `display: flex;`
- `.flex-column`: Arah vertikal.
- `.align-center`: Vertikal center.
- `.justify-between`: Rata kiri dan kanan.
- `.justify-center`: Rata tengah horizontal.
- `.gap-1`, `.gap-2`, `.gap-3`, `.gap-4`: Spasi antar elemen anak (0.25rem - 1.5rem).

---

## 3. Komponen Antarmuka

### Tombol (Buttons)
```html
<a href="#" class="btn btn-primary">Tombol Utama</a>
<a href="#" class="btn btn-secondary">Tombol Sekunder</a>
<a href="#" class="btn btn-success">Simpan</a>
<a href="#" class="btn btn-danger">Hapus</a>
<a href="#" class="btn btn-outline">Outline</a>
<a href="#" class="btn btn-sm btn-primary">Ukuran Kecil</a>
```

### Lencana / Status Pills (Badges)
```html
<span class="badge badge-primary">Admin</span>
<span class="badge badge-success">Published</span>
<span class="badge badge-warning">Draft</span>
<span class="badge badge-danger">Ditolak</span>
<span class="badge badge-info">Tutorial</span>
<span class="badge badge-secondary">15 Post</span>
```

### Kartu Statistik (Stat Cards)
```html
<div class="stat-card">
    <div class="stat-icon primary">📝</div>
    <div>
        <div class="stat-val">42</div>
        <div class="stat-label">Total Artikel</div>
    </div>
</div>
```

### Tabel Data (Data Tables)
```html
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Contoh Artikel</td>
                <td><span class="badge badge-success">Aktif</span></td>
                <td class="text-right">
                    <button class="btn btn-sm btn-secondary">Edit</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### Form Controls
```html
<div class="form-group">
    <label class="form-label">Nama Lengkap</label>
    <input type="text" class="form-control" placeholder="Masukkan nama...">
    <div class="form-text">Bantuan teks kecil di bawah form input.</div>
</div>

<div class="form-group">
    <label class="form-label">Kategori</label>
    <select class="form-select">
        <option>Pilihan 1</option>
    </select>
</div>

<div class="form-group">
    <label class="form-label">Deskripsi</label>
    <textarea class="form-textarea" placeholder="Tuliskan keterangan..."></textarea>
</div>
```

### Notifikasi Alert Flash
```html
<div class="alert alert-success">
    <span>Data berhasil disimpan!</span>
    <button type="button" class="alert-close">&times;</button>
</div>
```

---

## 4. Helper JavaScript Internal (`core-ui.js`)

File `public/assets/js/core-ui.js` menyediakan:
1. **Alert Dismiss**: Tombol `.alert-close` menutup alert secara otomatis dengan animasi fade-out.
2. **Auto Slug Generator**: Tambahkan atribut `id="title-input"` pada field judul dan `id="slug-input"` pada field slug.
3. **Image Preview**: Tambahkan atribut `data-preview="id_elemen_img"` pada input file `type="file"`.
4. **Mobile Sidebar Toggle**: Tombol dengan id `sidebar-toggle` otomatis membuka/menutup sidebar di layar HP.
