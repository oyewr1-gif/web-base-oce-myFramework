<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Artikel</h4>
        <div class="d-flex gap-2">
            <a href="<?= base_url('post/read/' . $post['slug']) ?>" target="_blank" class="btn btn-sm btn-outline">Lihat di Web &nearr;</a>
            <a href="<?= base_url('admin/posts') ?>" class="btn btn-sm btn-secondary">&larr; Kembali</a>
        </div>
    </div>

    <div class="card-body">
        <form action="<?= base_url('admin/posts/edit/' . $post['id']) ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="grid grid-2 mb-3">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="title-input" class="form-label">Judul Artikel <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="title-input" name="title" class="form-control" value="<?= e($post['title']) ?>" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="slug-input" class="form-label">URL Slug</label>
                    <input type="text" id="slug-input" name="slug" class="form-control" value="<?= e($post['slug']) ?>" required>
                </div>
            </div>

            <div class="grid grid-2 mb-3">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $post['category_id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="status" class="form-label">Status Publikasi</label>
                    <select name="status" id="status" class="form-select">
                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Diterbitkan (Published)</option>
                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Konsep (Draft)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="excerpt" class="form-label">Ringkasan Singkat (Excerpt)</label>
                <textarea name="excerpt" id="excerpt" class="form-textarea" style="min-height: 70px;"><?= e($post['excerpt']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="content" class="form-label">Isi Konten Artikel <span style="color: var(--danger);">*</span></label>
                <textarea name="content" id="content" class="form-textarea" style="min-height: 240px;" required><?= e($post['content']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="thumbnail" class="form-label">Gambar Thumbnail</label>
                <?php if (!empty($post['thumbnail'])): ?>
                    <div class="mb-2">
                        <img src="<?= upload_url($post['thumbnail']) ?>" alt="Current Thumb" style="max-width: 180px; height: 110px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: block;">
                        <small class="text-muted">Thumbnail saat ini. Unggah file baru di bawah untuk mengganti.</small>
                    </div>
                <?php endif; ?>
                <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*" data-preview="thumb-preview">
                <div class="mt-2">
                    <img id="thumb-preview" src="" alt="Preview Baru" style="display: none; max-width: 180px; height: 110px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                </div>
            </div>

            <div class="d-flex justify-end gap-2 mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <a href="<?= base_url('admin/posts') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan &rarr;</button>
            </div>
        </form>
    </div>
</div>
