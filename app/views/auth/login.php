<div class="text-center mb-4">
    <div style="font-size: 2.5rem; line-height: 1;">⚡</div>
    <h2 style="margin-top: 0.5rem; font-size: 1.5rem;">CMS Admin</h2>
    <p class="text-muted" style="font-size: 0.88rem;">Silakan login untuk mengelola konten website</p>
</div>

<form action="<?= base_url('auth/login') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="identity" class="form-label">Email atau Username</label>
        <input type="text" id="identity" name="identity" class="form-control" placeholder="admin@cms.local" required autofocus>
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
    <div style="font-size: 0.8rem; color: var(--text-muted);">
        <strong>Kredensial Bawaan Seeder:</strong><br>
        Email: <code>admin@cms.local</code><br>
        Password: <code>admin123</code>
    </div>
</div>

<div class="text-center mt-3">
    <a href="<?= base_url() ?>" class="text-muted" style="font-size: 0.85rem;">&larr; Kembali ke Website Utama</a>
</div>
