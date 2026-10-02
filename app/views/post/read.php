<div class="portal-layout">
    <!-- Konten Artikel Utama -->
    <article class="card portal-main" style="padding: 2.25rem;">
        <div class="mb-3">
            <a href="<?= base_url() ?>" class="text-muted font-semibold" style="font-size: 0.85rem; text-decoration: none;">&larr; Kembali ke Beranda</a>
        </div>

        <div class="d-flex align-center gap-2 mb-2 flex-wrap">
            <span class="badge badge-info"><?= e($post['category_name'] ?? 'Umum') ?></span>
            <small class="text-muted"><?= date('d F Y', strtotime($post['created_at'])) ?></small>
            <small class="text-muted">&bull; <?= (int)$post['views'] ?> views</small>
        </div>

        <h1 style="font-size: 2rem; margin-bottom: 1.25rem; line-height: 1.25;"><?= e($post['title']) ?></h1>

        <div class="d-flex align-center gap-2 mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <div style="width: 38px; height: 38px; border-radius: 50%; background: #dcfce7; color: #059669; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                <?= strtoupper(substr($post['author_name'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <div class="font-semibold" style="font-size: 0.92rem;"><?= e($post['author_name'] ?? 'Admin') ?></div>
                <small class="text-muted">Penulis</small>
            </div>
        </div>

        <?php if (!empty($post['thumbnail'])): ?>
            <div class="mb-4">
                <img src="<?= upload_url($post['thumbnail']) ?>" alt="<?= e($post['title']) ?>" style="width: 100%; max-height: 440px; object-fit: cover; border-radius: var(--radius-md);">
            </div>
        <?php endif; ?>

        <div class="article-body" style="font-size: 1.05rem; line-height: 1.85; color: #334155;">
            <?= $post['content'] ?>
        </div>
    </article>

    <!-- Sidebar Portal (Tautan Lembaga, Kategori, & Informasi PonPes Al Munawwariyyah) -->
    <aside class="portal-sidebar">
        <?php require APP_PATH . '/views/partials/frontend_sidebar.php'; ?>
    </aside>
</div>
