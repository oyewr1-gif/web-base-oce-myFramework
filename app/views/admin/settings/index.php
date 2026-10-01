<div class="card" style="max-width: 720px;">
    <div class="card-header">
        <h4 class="card-title">Pengaturan Umum Situs Web</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/settings') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="site_title" class="form-label">Nama Situs Web (Site Title) <span style="color: var(--danger);">*</span></label>
                <input type="text" id="site_title" name="site_title" class="form-control" value="<?= e($settings['site_title'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="site_tagline" class="form-label">Slogan / Tagline</label>
                <input type="text" id="site_tagline" name="site_tagline" class="form-control" value="<?= e($settings['site_tagline'] ?? '') ?>">
                <div class="form-text">Deskripsi singkat situs web untuk pengunjung dan mesin pencari.</div>
            </div>

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
