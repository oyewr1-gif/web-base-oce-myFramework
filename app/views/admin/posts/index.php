<div class="card">
    <div class="card-header">
        <h4 class="card-title">Daftar Semua Artikel</h4>
        <a href="<?= base_url('admin/posts/create') ?>" class="btn btn-sm btn-primary">+ Tulis Artikel Baru</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 70px;">Thumb</th>
                    <th>Judul & Slug</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Tanggal</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted" style="padding: 2.5rem;">
                            Belum ada artikel. Klik <strong>+ Tulis Artikel Baru</strong> untuk mulai menambahkan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?php if (!empty($post['thumbnail'])): ?>
                                    <img src="<?= upload_url($post['thumbnail']) ?>" alt="" style="width: 50px; height: 38px; object-fit: cover; border-radius: var(--radius-sm);">
                                <?php else: ?>
                                    <div style="width: 50px; height: 38px; background: #e2e8f0; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                        📄
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="font-size: 0.95rem;"><?= e($post['title']) ?></strong><br>
                                <small class="text-muted"><?= e($post['slug']) ?></small>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= e($post['category_name'] ?? 'Tanpa Kategori') ?></span>
                            </td>
                            <td><small><?= e($post['author_name'] ?? 'Admin') ?></small></td>
                            <td>
                                <?php if ($post['status'] === 'published'): ?>
                                    <span class="badge badge-success">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= (int)$post['views'] ?></small></td>
                            <td><small class="text-muted"><?= date('d/m/Y', strtotime($post['created_at'])) ?></small></td>
                            <td class="text-right">
                                <div class="d-flex justify-end gap-1">
                                    <a href="<?= base_url('post/read/' . $post['slug']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Lihat di Web">👁️</a>
                                    <a href="<?= base_url('admin/posts/edit/' . $post['id']) ?>" class="btn btn-sm btn-secondary" title="Edit">✏️</a>
                                    <a href="<?= base_url('admin/posts/delete/' . $post['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');" class="btn btn-sm btn-danger" title="Hapus">🗑️</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
