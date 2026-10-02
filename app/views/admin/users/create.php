<div style="max-width: 650px; margin: 0 auto;">
    <div class="card">
        <div class="card-header d-flex justify-between align-center">
            <h4 class="card-title">+ Tambah Pengguna Baru</h4>
            <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline">&larr; Kembali</a>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/users/create') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required autofocus>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="username" class="form-label">Username <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="contoh: budi_editor" required>
                        <div class="form-text">Hanya huruf kecil, angka, dan underscore.</div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email <span style="color: var(--danger);">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="budi@domain.com" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="password" class="form-label">Password <span style="color: var(--danger);">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Level / Hak Akses <span style="color: var(--danger);">*</span></label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="editor" selected>✍️ Editor Artikel (Penulis / Redaktur)</option>
                            <option value="admin">⚡ Administrator (Akses Penuh)</option>
                        </select>
                        <div class="form-text">Pilih 'Editor' untuk akun yang hanya mengelola konten artikel & media.</div>
                    </div>
                </div>

                <div class="d-flex justify-end gap-2 mt-4">
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Pengguna Baru &rarr;</button>
                </div>
            </form>
        </div>
    </div>
</div>
