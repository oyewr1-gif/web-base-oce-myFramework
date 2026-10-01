# 📚 Dokumentasi Proyek MyFrameWork CMS & REST API

Folder `docs/` ini berfungsi sebagai pusat dokumentasi resmi, panduan teknis, dan catatan riwayat perubahan (*changelog*) untuk seluruh pengembangan framework ini.

---

## 📑 Struktur Dokumentasi

| Dokumen | Deskripsi |
| :--- | :--- |
| [CHANGELOG.md](CHANGELOG.md) | **Catatan Riwayat Perubahan**: Catatan setiap penambahan fitur, pembaruan, dan perbaikan bug. |
| [FRAMEWORK_CMS_PLAN.md](FRAMEWORK_CMS_PLAN.md) | **Framework CMS Plan**: Rencana awal spesifikasi arsitektur hasil wawancara interaktif `/grill-me`. |
| [ARCHITECTURE.md](ARCHITECTURE.md) | **Arsitektur Sistem**: Desain teknis core engine MVC, SPL autoloader, router convention, dan model database. |
| [API_DOCUMENTATION.md](API_DOCUMENTATION.md) | **Dokumentasi REST API**: Panduan endpoint lengkap aplikasi mandiri `api-info/` beserta format autentikasi token. |
| [CSS_LIBRARY.md](CSS_LIBRARY.md) | **Panduan CoreUI CSS**: Referensi kelas utilitas dan komponen antarmuka internal 100% buatan sendiri. |

---

## 📝 Kebijakan Dokumentasi Perubahan

Setiap kali Anda atau pengembang lain melakukan penambahan, perubahan, atau refactoring pada proyek ini, **WAJIB** mencatat perubahan tersebut ke dalam [CHANGELOG.md](CHANGELOG.md) dengan mengikuti format:

```markdown
## [Versi / Tanggal] - Judul Perubahan Singkat
### Ditambahkan (Added)
- Deskripsi file / fitur / komponen baru.

### Diubah (Changed)
- Deskripsi penyesuaian fungsi, logika, atau desain yang telah ada.

### Diperbaiki (Fixed)
- Deskripsi bug atau perbaikan kesalahan kode.
```
