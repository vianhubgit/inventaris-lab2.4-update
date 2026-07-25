import './bootstrap';
import { initDarkMode } from './dark-mode';
import { initLoader } from './loader';
import { initCharts } from './charts';
import { initCascade } from './cascade';
import { initRealtimeNotifications } from './realtime';
import { initPwa } from './pwa';

/**
 * Entry point aplikasi Inventaris Lab TKJ.
 * Semua modul kecil di-init di sini agar tetap terorganisir & bebas dependency CDN.
 */
document.addEventListener('DOMContentLoaded', () => {
    initDarkMode();
    initLoader();
    initCharts();
    initCascade();
    initSidebar();
    initConfirmDialogs();
    initProcurementToggle();
    initNotifications();
    initRealtimeNotifications();
    initPwa();
});

/** Dropdown notifikasi (lonceng) di topbar. */
function initNotifications() {
    const toggle = document.querySelector('[data-notif-toggle]');
    const panel = document.querySelector('[data-notif-panel]');
    if (!toggle || !panel) return;

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        panel.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!panel.contains(e.target) && !toggle.contains(e.target)) {
            panel.classList.add('hidden');
        }
    });
}

/** Toggle sidebar pada layar mobile. */
function initSidebar() {
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.querySelector('[data-sidebar]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    if (!toggle || !sidebar) return;

    const open = () => {
        sidebar.classList.remove('-translate-x-full');
        backdrop?.classList.remove('hidden');
    };
    const close = () => {
        sidebar.classList.add('-translate-x-full');
        backdrop?.classList.add('hidden');
    };

    toggle.addEventListener('click', () =>
        sidebar.classList.contains('-translate-x-full') ? open() : close()
    );
    backdrop?.addEventListener('click', close);
}

/** Pengajuan barang: toggle antara "barang lama" dan "barang baru". */
function initProcurementToggle() {
    const radios = document.querySelectorAll('input[name="is_new_item"]');
    const existing = document.querySelector('[data-existing-item]');
    const fresh = document.querySelector('[data-new-item]');
    if (!radios.length || !existing || !fresh) return;

    const apply = (isNew) => {
        existing.classList.toggle('hidden', isNew);
        fresh.classList.toggle('hidden', !isNew);
    };

    radios.forEach((r) => r.addEventListener('change', () => apply(r.value === '1' && r.checked)));
    const checked = document.querySelector('input[name="is_new_item"]:checked');
    apply(checked ? checked.value === '1' : false);
}

/** Konfirmasi sebelum submit form berbahaya (mis. hapus). */
function initConfirmDialogs() {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            const message = form.getAttribute('data-confirm') || 'Apakah Anda yakin?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });
}
