<div class="text-center mb-4">
    <a href="<?= base_url() ?>">
        <img src="<?= site_logo_url() ?>" alt="MyFrameWork CMS" style="height: 48px; width: auto; max-width: 240px; margin-bottom: 0.5rem; object-fit: contain;">
    </a>
    <h2 style="margin-top: 0.25rem; font-size: 1.35rem;">Masuk ke Panel Konten</h2>
    <p class="text-muted" style="font-size: 0.88rem;">Silakan login untuk mengelola artikel dan konten website</p>
</div>

<form action="<?= base_url('auth/login') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="identity" class="form-label">Email atau Username</label>
        <input type="text" id="identity" name="identity" class="form-control" placeholder="admin@cms.local / editor@cms.local" required autofocus>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
    </div>

    <div class="form-group mt-4">
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">
            Masuk ke Panel &rarr;
        </button>
    </div>
</form>

<div class="card mt-4" style="background: #f8fafc; border: 1px dashed var(--border-color); padding: 0.85rem; margin-bottom: 0;">
    <div style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.5;">
        <strong>Kredensial Default:</strong><br>
        &bull; <strong>Admin:</strong> <code>admin@cms.local</code> / <code>admin123</code> (Akses Penuh + Users)<br>
        &bull; <strong>Editor:</strong> <code>editor@cms.local</code> / <code>editor123</code> (Khusus Artikel & Media)
    </div>
</div>

<div class="text-center mt-3">
    <a href="<?= base_url() ?>" class="text-muted" style="font-size: 0.85rem;">&larr; Kembali ke Website Utama</a>
</div>
