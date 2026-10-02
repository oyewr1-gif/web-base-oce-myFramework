<!-- Hero Section Publik -->
<div class="card" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); color: white; border: none; padding: 2.75rem 2rem; border-radius: var(--radius-lg); margin-bottom: 2.5rem;">
    <h1 style="color: white; font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem;"><?= e($siteTitle) ?></h1>
    <p style="color: #c7d2fe; font-size: 1.15rem; max-width: 650px; margin-bottom: 0;">
        <?= e($siteTagline) ?>
    </p>
</div>

<!-- Kategori Filter -->
<div class="mb-4">
    <div class="d-flex align-center gap-2 flex-wrap">
        <span class="text-muted font-semibold" style="font-size: 0.85rem;">Topik:</span>
        <a href="<?= base_url() ?>" class="badge <?= empty($activeCategory) ? 'badge-primary' : 'badge-secondary' ?>" style="padding: 0.35rem 0.75rem;">Semua Artikel</a>
        <?php foreach ($categories as $cat): ?>
            <?php $isActive = !empty($activeCategory) && $activeCategory['slug'] === $cat['slug']; ?>
            <a href="<?= base_url('post/category/' . $cat['slug']) ?>" class="badge <?= $isActive ? 'badge-primary' : 'badge-secondary' ?>" style="padding: 0.35rem 0.75rem;">
                <?= e($cat['name']) ?> (<?= (int)$cat['total_posts'] ?>)
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Grid Artikel Terbaru -->
<h2 class="mb-3" style="font-size: 1.35rem;">
    <?= !empty($activeCategory) ? 'Artikel dalam Kategori: ' . e($activeCategory['name']) : 'Artikel Terbaru' ?>
</h2>

<?php if (empty($posts)): ?>
    <div class="card text-center" style="padding: 3rem 1.5rem;">
        <p class="text-muted" style="font-size: 1.1rem; margin-bottom: 0;">Belum ada artikel yang dipublikasikan dalam kategori ini.</p>
    </div>
<?php else: ?>
    <div class="grid grid-3">
        <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <?php if (!empty($post['thumbnail'])): ?>
                    <img src="<?= upload_url($post['thumbnail']) ?>" alt="<?= e($post['title']) ?>" class="post-card-thumb">
                <?php else: ?>
                    <div class="post-card-thumb d-flex align-center justify-center" style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; font-size: 2rem;">
                        📄
                    </div>
                <?php endif; ?>

                <div class="post-card-body">
                    <div class="d-flex align-center justify-between mb-2">
                        <span class="badge badge-info"><?= e($post['category_name'] ?? 'Umum') ?></span>
                        <small class="text-muted"><?= date('d M Y', strtotime($post['created_at'])) ?></small>
                    </div>

                    <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem; line-height: 1.3;">
                        <a href="<?= base_url('post/read/' . $post['slug']) ?>" style="color: var(--text-main);">
                            <?= e($post['title']) ?>
                        </a>
                    </h3>

                    <p class="text-muted" style="font-size: 0.88rem; flex-grow: 1; margin-bottom: 1rem;">
                        <?= e($post['excerpt'] ?? substr(strip_tags($post['content'] ?? ''), 0, 110) . '...') ?>
                    </p>

                    <div class="d-flex align-center justify-between mt-2" style="border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
                        <small class="text-muted">Oleh: <strong><?= e($post['author_name'] ?? 'Admin') ?></strong></small>
                        <a href="<?= base_url('post/read/' . $post['slug']) ?>" class="font-semibold" style="font-size: 0.88rem;">
                            Baca &rarr;
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
