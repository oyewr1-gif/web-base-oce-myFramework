<!-- Card Upload Form -->
<div class="card mb-4">
    <div class="card-header">
        <h4 class="card-title">Unggah Media Baru</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/media/upload') ?>" method="POST" enctype="multipart/form-data" class="d-flex align-center gap-3 flex-wrap">
            <?= csrf_field() ?>

            <div style="flex-grow: 1; min-width: 250px;">
                <input type="file" name="media_file" class="form-control" accept="image/*,.pdf" required>
            </div>

            <button type="submit" class="btn btn-primary">
                📤 Unggah File
            </button>
        </form>
        <div class="form-text mt-2">Dukungan format: JPG, JPEG, PNG, GIF, WebP, SVG, PDF. Maksimal 10MB.</div>
    </div>
</div>

<!-- Galeri File Media -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Pustaka Media (<?= count($mediaList) ?> File)</h4>
    </div>
    <div class="card-body">
        <?php if (empty($mediaList)): ?>
            <div class="text-center text-muted" style="padding: 3rem 1rem;">
                Belum ada file media yang diunggah. Gunakan form di atas untuk mengunggah gambar atau dokumen.
            </div>
        <?php else: ?>
            <div class="grid grid-4">
                <?php foreach ($mediaList as $item): ?>
                    <?php 
                        $isImage = strpos($item['mime_type'], 'image/') === 0;
                        $fileUrl = base_url($item['file_path']);
                    ?>
                    <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column;">
                        <div style="height: 140px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <?php if ($isImage): ?>
                                <img src="<?= $fileUrl ?>" alt="<?= e($item['original_name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="font-size: 2.5rem; color: var(--secondary);">📄</div>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 0.85rem; font-size: 0.82rem; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div class="mb-2">
                                <strong style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e($item['original_name']) ?>">
                                    <?= e($item['original_name']) ?>
                                </strong>
                                <small class="text-muted"><?= round($item['file_size'] / 1024, 1) ?> KB &bull; <?= date('d/m/y', strtotime($item['created_at'])) ?></small>
                            </div>

                            <div class="d-flex align-center justify-between gap-1" style="border-top: 1px solid var(--border-color); padding-top: 0.5rem;">
                                <button type="button" class="btn btn-sm btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" onclick="navigator.clipboard.writeText('<?= $fileUrl ?>'); alert('URL file berhasil disalin!');">
                                    📋 Salin URL
                                </button>
                                <a href="<?= base_url('admin/media/delete/' . $item['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?');" class="btn btn-sm btn-danger" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" title="Hapus">
                                    🗑️
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
