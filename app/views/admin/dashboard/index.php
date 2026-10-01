<!-- Stat Cards Grid -->
<div class="grid grid-4 mb-4">
    <div class="stat-card">
        <div class="stat-icon primary">📝</div>
        <div>
            <div class="stat-val"><?= (int)$stats['total_posts'] ?></div>
            <div class="stat-label">Total Artikel</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">🚀</div>
        <div>
            <div class="stat-val"><?= (int)$stats['published_posts'] ?></div>
            <div class="stat-label">Diterbitkan</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">📁</div>
        <div>
            <div class="stat-val"><?= (int)$stats['total_categories'] ?></div>
            <div class="stat-label">Kategori</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">🖼️</div>
        <div>
            <div class="stat-val"><?= (int)$stats['total_media'] ?></div>
            <div class="stat-label">Media & File</div>
        </div>
    </div>
</div>

<!-- Aksi Cepat -->
<div class="card mb-4" style="background: #ffffff;">
    <div class="card-body d-flex align-center justify-between flex-wrap gap-2">
        <div>
            <h4 style="margin-bottom: 0.2rem;">Aksi Cepat</h4>
            <p class="text-muted" style="margin-bottom: 0; font-size: 0.88rem;">Pintasan umum untuk pengelolaan CMS Anda</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('admin/posts/create') ?>" class="btn btn-primary">+ Tulis Artikel Baru</a>
            <a href="<?= base_url('admin/categories') ?>" class="btn btn-secondary">+ Tambah Kategori</a>
            <a href="<?= base_url('admin/media') ?>" class="btn btn-secondary">Upload File Media</a>
        </div>
    </div>
</div>

<!-- Tabel Artikel Terbaru -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Artikel Terbaru</h4>
        <a href="<?= base_url('admin/posts') ?>" class="btn btn-sm btn-outline">Lihat Semua Artikel &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Judul Artikel</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentPosts)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 2rem;">
                            Belum ada artikel. Klik tombol <strong>+ Tulis Artikel Baru</strong> di atas untuk membuat.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentPosts as $post): ?>
                        <tr>
                            <td>
                                <strong><?= e($post['title']) ?></strong><br>
                                <small class="text-muted"><?= e($post['slug']) ?></small>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= e($post['category_name'] ?? 'Tanpa Kategori') ?></span>
                            </td>
                            <td><?= e($post['author_name'] ?? 'Admin') ?></td>
                            <td>
                                <?php if ($post['status'] === 'published'): ?>
                                    <span class="badge badge-success">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= date('d M Y', strtotime($post['created_at'])) ?></small></td>
                            <td class="text-right">
                                <div class="d-flex justify-end gap-1">
                                    <a href="<?= base_url('post/read/' . $post['slug']) ?>" target="_blank" class="btn btn-sm btn-outline">Lihat</a>
                                    <a href="<?= base_url('admin/posts/edit/' . $post['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
