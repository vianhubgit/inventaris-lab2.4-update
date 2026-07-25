/**
 * Service worker Inventaris Lab TKJ.
 * - Aset statis (build & gambar): cache-first + perbarui di latar (stale-while-revalidate).
 * - Halaman (navigasi): network-first, fallback ke cache saat offline.
 * Catatan: hanya aktif pada secure context (HTTPS atau localhost).
 */
const CACHE = 'inventaris-v1';
const APP_SHELL = ['/'];

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(APP_SHELL).catch(() => {}))
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return;

    // Aset statis: cache-first, perbarui di latar belakang.
    if (url.pathname.startsWith('/build/') || url.pathname.startsWith('/images/')) {
        event.respondWith(
            caches.open(CACHE).then(async (cache) => {
                const cached = await cache.match(req);
                const network = fetch(req)
                    .then((res) => {
                        if (res && res.ok) cache.put(req, res.clone());
                        return res;
                    })
                    .catch(() => cached);
                return cached || network;
            })
        );
        return;
    }

    // Halaman & permintaan lain: utamakan jaringan, fallback ke cache saat offline.
    event.respondWith(
        fetch(req)
            .then((res) => {
                if (res && res.ok && req.mode === 'navigate') {
                    const copy = res.clone();
                    caches.open(CACHE).then((cache) => cache.put(req, copy));
                }
                return res;
            })
            .catch(() => caches.match(req).then((cached) => cached || caches.match('/')))
    );
});
