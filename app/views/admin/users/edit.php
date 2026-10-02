<div style="max-width: 650px; margin: 0 auto;">
    <div class="card">
        <div class="card-header d-flex justify-between align-center">
            <h4 class="card-title">Edit Data Pengguna: <?= e($user['name'] ?? '') ?></h4>
            <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline">&larr; Kembali</a>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/users/edit/' . $user['id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= e($user['name']) ?>" required autofocus>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="username" class="form-label">Username <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" value="<?= e($user['username']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email <span style="color: var(--danger);">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru (Opsional)</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        <div class="form-text">Isi hanya jika ingin mengganti password akun ini.</div>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Level / Hak Akses <span style="color: var(--danger);">*</span></label>
                        <?php 
                        $currentUserId = (int)(current_user()['id'] ?? 0);
                        $isSelf = ((int)$user['id'] === $currentUserId);
                        ?>
                        <select name="role" id="role" class="form-control" required <?= $isSelf ? 'disabled' : '' ?>>
                            <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>✍️ Editor Artikel</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>⚡ Administrator</option>
                        </select>
                        <?php if ($isSelf): ?>
                            <input type="hidden" name="role" value="<?= e($user['role']) ?>">
                            <div class="form-text" style="color: #b45309;">Anda tidak dapat mengubah level peran akun Anda sendiri.</div>
                        <?php else: ?>
                            <div class="form-text">Pilih level peran yang sesuai untuk pengguna ini.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex justify-end gap-2 mt-4">
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan &rarr;</button>
                </div>
            </form>
        </div>
    </div>
</div>
