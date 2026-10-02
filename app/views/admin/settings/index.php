<div class="card" style="max-width: 760px;">
    <div class="card-header d-flex justify-between align-center">
        <h4 class="card-title" style="margin: 0;">Pengaturan Umum Situs Web</h4>
        <span class="badge badge-primary">⚡ Khusus Administrator</span>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/settings') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Identitas Situs -->
            <div class="form-group">
                <label for="site_title" class="form-label">Nama Situs Web (Site Title) <span style="color: var(--danger);">*</span></label>
                <input type="text" id="site_title" name="site_title" class="form-control" value="<?= e($settings['site_title'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="site_tagline" class="form-label">Slogan / Tagline</label>
                <input type="text" id="site_tagline" name="site_tagline" class="form-control" value="<?= e($settings['site_tagline'] ?? '') ?>">
                <div class="form-text">Deskripsi singkat situs web untuk pengunjung dan mesin pencari.</div>
            </div>

            <!-- Pengaturan Logo Situs -->
            <div class="form-group" style="background: var(--bg-hover, #f8fafc); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.25rem; margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <label class="form-label" style="font-weight: 600; font-size: 1rem; margin-bottom: 0.25rem;">
                    🖼️ Logo Situs Web (Brand Logo)
                </label>
                <div class="form-text" style="margin-bottom: 1rem;">
                    Logo ini akan ditampilkan di header portal publik, sidebar panel admin, dan halaman login.
                </div>

                <!-- Preview Logo Saat Ini -->
                <div style="display: flex; gap: 1.25rem; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap;">
                    <div style="background: #ffffff; border: 2px dashed var(--border-color); border-radius: 8px; padding: 12px 20px; display: inline-flex; align-items: center; justify-content: center; min-height: 68px; min-width: 160px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <img id="currentLogoPreview" src="<?= site_logo_url($settings['site_logo'] ?? null) ?>" alt="Logo Preview" style="max-height: 48px; max-width: 220px; object-fit: contain;">
                    </div>
                    <div style="flex: 1; min-width: 240px;">
                        <?php if (!empty($settings['site_logo'])): ?>
                            <div style="margin-bottom: 4px;">
                                <span class="badge badge-success">✓ Logo Kustom Aktif</span>
                            </div>
                            <div class="text-muted" style="font-size: 0.85rem; font-family: monospace; word-break: break-all; margin-bottom: 8px;">
                                <?= e($settings['site_logo']) ?>
                            </div>
                            <div>
                                <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.875rem; color: var(--danger); cursor: pointer; font-weight: 500;">
                                    <input type="checkbox" name="remove_logo" value="1" id="removeLogoCheck">
                                    <span>Hapus logo kustom & kembalikan ke logo bawaan sistem</span>
                                </label>
                            </div>
                        <?php else: ?>
                            <div style="margin-bottom: 4px;">
                                <span class="badge badge-secondary">ℹ️ Menggunakan Logo Standar (SVG Default)</span>
                            </div>
                            <div class="text-muted" style="font-size: 0.85rem;">
                                Belum ada logo kustom yang diunggah. Unggah file gambar di bawah untuk mengganti logo.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Input File Upload Logo Baru -->
                <div class="form-group" style="margin-bottom: 0.85rem;">
                    <label for="logo_file" class="form-label" style="font-weight: 500;">Unggah Berkas Logo Baru:</label>
                    <input type="file" id="logo_file" name="logo_file" class="form-control" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                    <div class="form-text">Mendukung format PNG, JPG, WebP, atau SVG (Maks. 5 MB). Disarankan logo proporsional horizontal transparan dengan tinggi 40–60px.</div>
                </div>

                <!-- Input Text URL / Path Logo (Opsional) -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="site_logo" class="form-label" style="font-weight: 500;">Atau Tentukan Path / URL Logo:</label>
                    <input type="text" id="site_logo" name="site_logo" class="form-control" value="<?= e($settings['site_logo'] ?? '') ?>" placeholder="misal: uploads/logo-kustom.png atau https://domain.com/logo.png">
                    <div class="form-text">Bisa menggunakan berkas dari Galeri Media atau URL eksternal. Biarkan kosong jika ingin memakai logo bawaan sistem.</div>
                </div>
            </div>

            <!-- Kontak & Footer -->
            <div class="form-group">
                <label for="admin_email" class="form-label">Email Administrator</label>
                <input type="email" id="admin_email" name="admin_email" class="form-control" value="<?= e($settings['admin_email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="footer_text" class="form-label">Teks Hak Cipta Footer</label>
                <input type="text" id="footer_text" name="footer_text" class="form-control" value="<?= e($settings['footer_text'] ?? '') ?>">
            </div>

            <div class="d-flex justify-end gap-2 mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan &rarr;</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('logo_file');
    const previewImg = document.getElementById('currentLogoPreview');
    const urlInput = document.getElementById('site_logo');
    const removeCheck = document.getElementById('removeLogoCheck');

    if (fileInput && previewImg) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
                if (removeCheck) {
                    removeCheck.checked = false;
                }
            }
        });
    }

    if (removeCheck && previewImg) {
        const defaultSrc = '<?= asset('images/logo.svg') ?>';
        const originalSrc = previewImg.src;
        removeCheck.addEventListener('change', function() {
            if (this.checked) {
                previewImg.src = defaultSrc;
                if (urlInput) urlInput.value = '';
                if (fileInput) fileInput.value = '';
            } else {
                previewImg.src = originalSrc;
            }
        });
    }
});
</script>
