<div class="card mb-4">
    <div class="card-header d-flex justify-between align-center flex-wrap gap-2">
        <div>
            <h4 class="card-title" style="margin-bottom: 0.2rem;">👥 Master Pengguna (Users)</h4>
            <p class="text-muted" style="margin-bottom: 0; font-size: 0.85rem;">Kelola seluruh akun pengguna dan hak akses level peran (Admin & Editor)</p>
        </div>
        <div>
            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
                + Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Level / Peran</th>
                    <th>Terdaftar Sejak</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 2.5rem;">
                            Belum ada pengguna lain. Klik tombol <strong>+ Tambah Pengguna Baru</strong> di atas untuk membuat akun.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $currentUserId = (int)(current_user()['id'] ?? 0);
                    foreach ($users as $u): 
                    ?>
                        <tr>
                            <td>
                                <div class="d-flex align-center gap-2">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: <?= $u['role'] === 'admin' ? '#4f46e5' : '#059669' ?>; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                                        <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= e($u['name']) ?></strong>
                                        <?php if ((int)$u['id'] === $currentUserId): ?>
                                            <span style="font-size: 0.72rem; background: #e0e7ff; color: #4338ca; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">Akun Anda</span>
                                        <?php endif; ?>
                                        <br>
                                        <small class="text-muted">@<?= e($u['username']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code><?= e($u['email']) ?></code>
                            </td>
                            <td>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="badge badge-primary">⚡ Administrator</span>
                                <?php elseif ($u['role'] === 'editor'): ?>
                                    <span class="badge badge-success">✍️ Editor Artikel</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?= e(ucfirst($u['role'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= !empty($u['created_at']) ? date('d M Y, H:i', strtotime($u['created_at'])) : '-' ?></small>
                            </td>
                            <td class="text-right">
                                <div class="d-flex justify-end gap-1">
                                    <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-sm btn-secondary">
                                        Edit
                                    </a>
                                    <?php if ((int)$u['id'] !== $currentUserId): ?>
                                        <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" class="btn btn-sm btn-outline" style="color: var(--danger); border-color: #fecdd3;" onclick="return confirm('Apakah Anda yakin ingin menghapus akun pengguna <?= e(addslashes($u['name'])) ?>?');">
                                            Hapus
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Info Kartu Penjelasan Level Hak Akses -->
<div class="card" style="background: #ffffff; border: 1px solid #e2e8f0;">
    <div class="card-header">
        <h4 class="card-title" style="font-size: 0.95rem;">💡 Keterangan Hak Akses Peran (Roles & Permissions)</h4>
    </div>
    <div class="card-body" style="font-size: 0.88rem; line-height: 1.6; color: #475569;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="padding: 12px; background: #f8fafc; border-left: 4px solid #4f46e5; border-radius: 4px;">
                <strong style="color: #1e1b4b;">⚡ Administrator:</strong>
                <p style="margin: 4px 0 0 0; font-size: 0.82rem;">Memiliki kontrol penuh atas seluruh sistem, termasuk membuat & menghapus akun pengguna (Master Users), pengaturan situs, kelola artikel, kategori, dan media.</p>
            </div>
            <div style="padding: 12px; background: #f8fafc; border-left: 4px solid #059669; border-radius: 4px;">
                <strong style="color: #064e3b;">✍️ Editor Artikel:</strong>
                <p style="margin: 4px 0 0 0; font-size: 0.82rem;">Fokus pada produksi dan kurasi konten. Dapat menulis, mengedit, mempublikasikan artikel, mengunggah media, dan mengelola kategori. Tidak dapat mengakses Master Users maupun Pengaturan Sistem.</p>
            </div>
        </div>
    </div>
</div>
