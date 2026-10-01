<div style="display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 2rem; align-items: start;">
    <!-- Konten Artikel -->
    <article class="card" style="padding: 2.25rem;">
        <div class="mb-3">
            <a href="<?= base_url() ?>" class="text-muted" style="font-size: 0.85rem;">&larr; Kembali ke Beranda</a>
        </div>

        <div class="d-flex align-center gap-2 mb-2">
            <span class="badge badge-info"><?= e($post['category_name'] ?? 'Umum') ?></span>
            <small class="text-muted"><?= date('d F Y', strtotime($post['created_at'])) ?></small>
            <small class="text-muted">&bull; <?= (int)$post['views'] ?> views</small>
        </div>

        <h1 style="font-size: 2rem; margin-bottom: 1.25rem; line-height: 1.25;"><?= e($post['title']) ?></h1>

        <div class="d-flex align-center gap-2 mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700;">
                <?= strtoupper(substr($post['author_name'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <div class="font-semibold" style="font-size: 0.92rem;"><?= e($post['author_name'] ?? 'Admin') ?></div>
                <small class="text-muted">Penulis</small>
            </div>
        </div>

        <?php if (!empty($post['thumbnail'])): ?>
            <div class="mb-4">
                <img src="<?= upload_url($post['thumbnail']) ?>" alt="<?= e($post['title']) ?>" style="width: 100%; max-height: 420px; object-fit: cover; border-radius: var(--radius-md);">
            </div>
        <?php endif; ?>

        <div class="article-body" style="font-size: 1.05rem; line-height: 1.8; color: #334155;">
            <?= $post['content'] ?>
        </div>
    </article>

    <!-- Sidebar Kategori -->
    <aside>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Kategori Artikel</h4>
            </div>
            <div class="card-body" style="padding: 0.5rem 1rem;">
                <ul style="list-style: none;">
                    <?php foreach ($categories as $cat): ?>
                        <li style="border-bottom: 1px solid var(--border-color); padding: 0.65rem 0;">
                            <a href="<?= base_url('post/category/' . $cat['slug']) ?>" class="d-flex align-center justify-between" style="color: var(--text-main);">
                                <span><?= e($cat['name']) ?></span>
                                <span class="badge badge-secondary"><?= (int)$cat['total_posts'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </aside>
</div>
