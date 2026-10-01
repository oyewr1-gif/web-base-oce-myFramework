/**
 * CoreUI Micro JS Helper
 * Tanpa library eksternal / 100% Vanilla JS
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Alert Dismiss
    document.querySelectorAll('.alert-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const alert = btn.closest('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-6px)';
                setTimeout(() => alert.remove(), 200);
            }
        });
    });

    // 2. Mobile Sidebar Toggle
    const sidebarToggleBtn = document.getElementById('sidebar-toggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const backdrop = document.querySelector('.admin-sidebar-backdrop');

    if (sidebarToggleBtn && adminSidebar) {
        sidebarToggleBtn.addEventListener('click', function () {
            adminSidebar.classList.toggle('open');
            if (backdrop) backdrop.classList.toggle('active');
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            if (adminSidebar) adminSidebar.classList.remove('open');
            backdrop.classList.remove('active');
        });
    }

    // 3. Auto Slug Generator (misal input title -> input slug)
    const titleInput = document.getElementById('title-input');
    const slugInput = document.getElementById('slug-input');

    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function () {
            // Hanya auto-generate jika slug belum diisi manual atau masih sama
            if (!slugInput.dataset.manualEdited) {
                const slug = titleInput.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
            }
        });

        slugInput.addEventListener('input', function () {
            slugInput.dataset.manualEdited = 'true';
        });
    }

    // 4. Modal Triggers
    document.querySelectorAll('[data-modal-target]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const targetId = trigger.getAttribute('data-modal-target');
            const modal = document.getElementById(targetId);
            if (modal) modal.classList.add('active');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(function (closeBtn) {
        closeBtn.addEventListener('click', function () {
            const modal = closeBtn.closest('.modal-overlay');
            if (modal) modal.classList.remove('active');
        });
    });

    // 5. Image Preview Helper
    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    fileInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            const previewId = input.getAttribute('data-preview');
            const previewEl = document.getElementById(previewId);
            if (previewEl && input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewEl.src = e.target.result;
                    previewEl.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
});
