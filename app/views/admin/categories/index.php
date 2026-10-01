<div style="display: grid; grid-template-columns: 360px minmax(0, 1fr); gap: 1.5rem; align-items: start;">
    <!-- Form Tambah Kategori -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">+ Tambah Kategori Baru</h4>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/categories/create') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="title-input" class="form-label">Nama Kategori <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="title-input" name="name" class="form-control" placeholder="Contoh: Bisnis, Desain" required>
                </div>

                <div class="form-group">
                    <label for="slug-input" class="form-label">Slug URL</label>
                    <input type="text" id="slug-input" name="slug" class="form-control" placeholder="contoh-bisnis">
                    <div class="form-text">Biarkan kosong untuk auto-generate dari nama kategori.</div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" class="form-textarea" style="min-height: 90px;" placeholder="Deskripsi opsional untuk kategori..."></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Kategori &rarr;</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Kategori -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Daftar Kategori</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th>Total Artikel</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding: 2rem;">Belum ada kategori yang dibuat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <strong><?= e($cat['name']) ?></strong>
                                    <?php if (!empty($cat['description'])): ?>
                                        <br><small class="text-muted"><?= e($cat['description']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= e($cat['slug']) ?></code></td>
                                <td>
                                    <span class="badge badge-secondary"><?= (int)$cat['total_posts'] ?> artikel</span>
                                </td>
                                <td class="text-right">
                                    <a href="<?= base_url('admin/categories/delete/' . $cat['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="btn btn-sm btn-danger" title="Hapus">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
