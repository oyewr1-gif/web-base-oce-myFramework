<div class="card">
    <div class="card-header">
        <h4 class="card-title">Tulis Artikel Baru</h4>
        <a href="<?= base_url('admin/posts') ?>" class="btn btn-sm btn-outline">&larr; Kembali</a>
    </div>

    <div class="card-body">
        <form action="<?= base_url('admin/posts/create') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="grid grid-2 mb-3">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="title-input" class="form-label">Judul Artikel <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="title-input" name="title" class="form-control" placeholder="Masukkan judul artikel yang menarik..." required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="slug-input" class="form-label">URL Slug (Otomatis)</label>
                    <input type="text" id="slug-input" name="slug" class="form-control" placeholder="url-slug-otomatis">
                    <div class="form-text">Biarkan otomatis terisi dari judul atau sesuaikan sendiri.</div>
                </div>
            </div>

            <div class="grid grid-2 mb-3">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="status" class="form-label">Status Publikasi</label>
                    <select name="status" id="status" class="form-select">
                        <option value="published">Diterbitkan (Published)</option>
                        <option value="draft">Konsep (Draft)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="excerpt" class="form-label">Ringkasan Singkat (Excerpt)</label>
                <textarea name="excerpt" id="excerpt" class="form-textarea" style="min-height: 70px;" placeholder="Ringkasan singkat artikel untuk preview..."></textarea>
            </div>

            <div class="form-group">
                <label for="content" class="form-label">Isi Konten Artikel <span style="color: var(--danger);">*</span></label>
                <textarea name="content" id="content" class="form-textarea" style="min-height: 220px;" placeholder="Tulis konten lengkap artikel di sini (mendukung tag HTML standar seperti <p>, <h3>, <strong>, <em>, <ul>, <li>)..." required></textarea>
            </div>

            <div class="form-group">
                <label for="thumbnail" class="form-label">Gambar Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*" data-preview="thumb-preview">
                <div class="form-text">Format yang didukung: JPG, PNG, GIF, WebP.</div>
                <div class="mt-2">
                    <img id="thumb-preview" src="" alt="Preview" style="display: none; max-width: 200px; height: 120px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                </div>
            </div>

            <div class="d-flex justify-end gap-2 mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <a href="<?= base_url('admin/posts') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Artikel &rarr;</button>
            </div>
        </form>
    </div>
</div>
