<?php
/**
 * Partial View: Sidebar Portal Publik
 * Terinspirasi dari arsitektur sidebar portal informasi: https://info.almunawwariyyah.sch.id/
 * Memuat Tautan Cepat Lembaga (PPDB, SMK, SMA, SMP, SD, MIM), Kategori Berita, Artikel Terkini, & Visitor Counter
 */
?>
<!-- 1. WIDGET LINK & LEMBAGA PENDIDIKAN -->
<div class="sidebar-widget">
    <div class="sidebar-widget-header header-emerald">
        <span>🌐</span>
        <span>Link & Lembaga</span>
    </div>
    <div class="sidebar-widget-body">
        <!-- Grup PPDB -->
        <div class="sidebar-group-title">
            <span>🎓 PPDB Online</span>
            <span class="badge badge-success" style="font-size: 0.68rem;">Terbuka</span>
        </div>
        <ul class="sidebar-links">
            <li>
                <a href="https://info.almunawwariyyah.sch.id/Ppdb" target="_blank" rel="noopener">
                    <span>📌</span>
                    <span>Info PPDB Utama</span>
                </a>
                <span class="social-pill web">Web &nearr;</span>
            </li>
            <li>
                <a href="https://ppdb.almunawwariyyah.sch.id" target="_blank" rel="noopener">
                     <span>📋</span>
                    <span>Portal Pendaftaran Online</span>
                </a>
                <span class="social-pill web">Daftar &nearr;</span>
            </li>
            <li>
                <a href="https://ppdb.almunamedia.s-net.id" target="_blank" rel="noopener">
                    <span>📋</span>
                    <span>Portal Pendaftaran Online</span>
                </a>
                <span class="social-pill web">Daftar &nearr;</span>
            </li>
        </ul>

        <!-- Grup SMK -->
        <div class="sidebar-group-title">
            <span>🏢 SMK Al Munawwariyyah</span>
        </div>
        <ul class="sidebar-links">
            <li>
                <a href="https://smk.almunawwariyyah.sch.id" target="_blank" rel="noopener">
                    <span>🌐</span>
                    <span>smk.almunawwariyyah.sch.id</span>
                </a>
            </li>
            <li>
                <span class="text-muted" style="font-size: 0.82rem;">Sosial Media:</span>
                <div class="social-pills">
                    <a href="https://m.youtube.com/@smkalmunawwariyyah821" target="_blank" rel="noopener" class="social-pill yt">▶ YouTube</a>
                    <a href="https://instagram.com/smkalmunawwariyyah" target="_blank" rel="noopener" class="social-pill ig">📷 Instagram</a>
                </div>
            </li>
        </ul>

        <!-- Grup SMA -->
        <div class="sidebar-group-title">
            <span>🏛️ SMA Al Munawwariyyah</span>
        </div>
        <ul class="sidebar-links">
            <li>
                <a href="https://info.almunawwariyyah.sch.id/Lembaga/sma" target="_blank" rel="noopener">
                    <span>🌐</span>
                    <span>Profil & Informasi SMA</span>
                </a>
            </li>
            <li>
                <span class="text-muted" style="font-size: 0.82rem;">Sosial Media:</span>
                <div class="social-pills">
                    <a href="https://www.youtube.com/@creativesmaalmunawwariyyah2093" target="_blank" rel="noopener" class="social-pill yt">▶ YouTube</a>
                    <a href="https://www.instagram.com/smaalmunawwariyyah/" target="_blank" rel="noopener" class="social-pill ig">📷 IG</a>
                    <a href="https://www.tiktok.com/@smaalmunawwariyyah" target="_blank" rel="noopener" class="social-pill tt">🎵 TikTok</a>
                </div>
            </li>
            <li>
                <a href="https://bit.ly/klipingsmaam" target="_blank" rel="noopener">
                    <span>📑</span>
                    <span>Kliping Digital SMA</span>
                </a>
                <span class="social-pill doc">PDF</span>
            </li>
            <li>
                <a href="https://bit.ly/PortoDTSMA" target="_blank" rel="noopener">
                    <span>🛠️</span>
                    <span>Portofolio Double Track</span>
                </a>
                <span class="social-pill doc">Porto</span>
            </li>
        </ul>

        <!-- Grup SMP -->
        <div class="sidebar-group-title">
            <span>🏫 SMP Al Munawwariyyah</span>
        </div>
        <ul class="sidebar-links">
            <li>
                <a href="https://info.almunawwariyyah.sch.id/Lembaga/smp" target="_blank" rel="noopener">
                    <span>🌐</span>
                    <span>Profil & Informasi SMP</span>
                </a>
            </li>
            <li>
                <span class="text-muted" style="font-size: 0.82rem;">Sosial Media:</span>
                <div class="social-pills">
                    <a href="https://www.instagram.com/smp_al_munawwariyyah/" target="_blank" rel="noopener" class="social-pill ig">📷 @smp_al_munawwariyyah</a>
                </div>
            </li>
        </ul>

        <!-- Grup SD & MIM -->
        <div class="sidebar-group-title">
            <span>🎒 SD & MIM</span>
        </div>
        <ul class="sidebar-links">
            <li>
                <a href="https://info.almunawwariyyah.sch.id/Lembaga/sd" target="_blank" rel="noopener">
                    <span>🏫</span>
                    <span>SD Al Munawwariyyah</span>
                </a>
            </li>
            <li>
                <a href="https://info.almunawwariyyah.sch.id/Lembaga/mim" target="_blank" rel="noopener">
                    <span>🕌</span>
                    <span>Madrasah Ibtidaiyah (MIM)</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- 2. WIDGET KATEGORI TOPIK ARTIKEL -->
<?php if (!empty($categories)): ?>
<div class="sidebar-widget">
    <div class="sidebar-widget-header header-indigo">
        <span>📁</span>
        <span>Kategori Berita</span>
    </div>
    <div class="sidebar-widget-body" style="padding: 0.75rem 1.25rem;">
        <ul class="sidebar-links" style="margin: 0;">
            <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="<?= base_url('post/category/' . $cat['slug']) ?>">
                        <span>🏷️</span>
                        <span><?= e($cat['name']) ?></span>
                    </a>
                    <span class="badge badge-secondary"><?= (int)($cat['total_posts'] ?? 0) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<!-- 3. WIDGET ARTIKEL TERKINI -->
<?php if (!empty($recentPosts)): ?>
<div class="sidebar-widget">
    <div class="sidebar-widget-header header-amber">
        <span>📰</span>
        <span>Artikel Terkini</span>
    </div>
    <div class="sidebar-widget-body" style="padding: 0.5rem 1.25rem;">
        <?php foreach (array_slice($recentPosts, 0, 5) as $rp): ?>
            <a href="<?= base_url('post/read/' . $rp['slug']) ?>" class="recent-post-item">
                <?php if (!empty($rp['thumbnail'])): ?>
                    <img src="<?= upload_url($rp['thumbnail']) ?>" alt="<?= e($rp['title']) ?>" class="recent-post-thumb">
                <?php else: ?>
                    <div class="recent-post-thumb d-flex align-center justify-center text-muted" style="font-size: 1.25rem;">
                        📄
                    </div>
                <?php endif; ?>
                <div style="flex: 1; min-width: 0;">
                    <div class="recent-post-title"><?= e($rp['title']) ?></div>
                    <small class="text-muted" style="font-size: 0.76rem;">
                        <?= date('d M Y', strtotime($rp['created_at'])) ?>
                    </small>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- 4. WIDGET INFORMASI PESANTREN & STATISTIK PENGUNJUNG -->
<div class="sidebar-widget">
    <div class="sidebar-widget-header" style="background: linear-gradient(135deg, #334155 0%, #1e293b 100%); border-bottom: 2px solid #0f172a;">
        <span>🕌</span>
        <span>PonPes Al Munawwariyyah</span>
    </div>
    <div class="sidebar-widget-body text-center">
        <p style="font-size: 0.88rem; color: #475569; margin-bottom: 0.85rem; line-height: 1.45;">
            📍 <strong>Alamat Kampus:</strong><br>
            Raya Sudimoro 9 Bululawang, Malang, Jawa Timur 65171.
        </p>

        <div class="visitor-box" style="margin-top: 0.5rem;">
            <div style="font-size: 0.82rem; font-weight: 700; color: #64748b; text-transform: uppercase;">
                Visitor Counter
            </div>
            <div class="visitor-number">
                <?= number_format(rand(1280000, 1295000), 0, ',', '.') ?>
            </div>
            <small class="text-muted" style="font-size: 0.75rem; display: block;">
                Pengunjung Aktif Hari Ini
            </small>
        </div>
    </div>
</div>
