/**
 * Progressive Web App: registrasi service worker + tombol "Instal Aplikasi".
 * Tombol [data-pwa-install] tampil di topbar & halaman login.
 *
 * - Chrome/Edge/Android (HTTPS): memicu prompt instalasi bawaan.
 * - iPhone/iPad (Safari): menampilkan instruksi "Tambah ke Layar Utama".
 * - Sudah terpasang (standalone): tombol disembunyikan.
 */
export function initPwa() {
    // Service worker hanya tersedia pada secure context (HTTPS/localhost).
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }

    const buttons = document.querySelectorAll('[data-pwa-install]');
    if (!buttons.length) return;

    const isStandalone =
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);

    const hide = () => buttons.forEach((b) => b.classList.add('hidden'));

    // Sudah terpasang → tidak perlu tombol.
    if (isStandalone) {
        hide();
        return;
    }

    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        hide();
    });

    buttons.forEach((btn) =>
        btn.addEventListener('click', async (e) => {
            e.preventDefault();

            if (deferredPrompt) {
                deferredPrompt.prompt();
                await deferredPrompt.userChoice;
                deferredPrompt = null;
                hide();
                return;
            }

            if (isIos) {
                alert(
                    'Memasang di iPhone/iPad:\n\n' +
                    '1. Ketuk tombol Bagikan (kotak dengan panah ke atas)\n' +
                    '2. Pilih "Tambah ke Layar Utama"\n' +
                    '3. Ketuk "Tambah"'
                );
            } else {
                alert(
                    'Memasang aplikasi:\n\n' +
                    'Buka menu browser (⋮) lalu pilih "Instal aplikasi" ' +
                    'atau "Tambahkan ke layar utama".'
                );
            }
        })
    );
}
