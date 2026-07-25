/**
 * Notifikasi realtime sederhana via polling (tanpa server WebSocket).
 * Memperbarui badge & isi dropdown lonceng setiap beberapa detik
 * sehingga admin/sekretaris melihat notifikasi baru tanpa reload halaman.
 */
export function initRealtimeNotifications() {
    const root = document.querySelector('[data-notif]');
    if (!root) return;

    const feedUrl = root.getAttribute('data-notif-feed');
    const badge = root.querySelector('[data-notif-badge]');
    const list = root.querySelector('[data-notif-list]');
    if (!feedUrl || !badge || !list) return;

    const INTERVAL = 8000; // 8 detik — terasa realtime, tetap ringan untuk LAN

    const renderBadge = (count) => {
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : String(count);
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    };

    const renderList = (items) => {
        list.innerHTML = '';
        if (!items.length) {
            const p = document.createElement('p');
            p.className = 'px-4 py-6 text-center text-sm text-gray-400';
            p.textContent = 'Belum ada notifikasi.';
            list.appendChild(p);
            return;
        }
        items.forEach((it) => {
            const a = document.createElement('a');
            a.href = it.url;
            a.className =
                'flex items-start gap-3 px-4 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-700/40' +
                (it.read ? '' : ' bg-brand-50/60 dark:bg-brand-900/10');

            const dot = document.createElement('span');
            dot.className =
                'mt-1 h-2 w-2 shrink-0 rounded-full ' + (it.read ? 'bg-transparent' : 'bg-brand-500');

            const wrap = document.createElement('div');
            wrap.className = 'min-w-0';

            const title = document.createElement('p');
            title.className = 'truncate text-sm font-medium';
            title.textContent = it.title;

            const msg = document.createElement('p');
            msg.className = 'truncate text-xs text-gray-500 dark:text-gray-400';
            msg.textContent = it.message;

            const time = document.createElement('p');
            time.className = 'mt-0.5 text-[11px] text-gray-400';
            time.textContent = it.time;

            wrap.append(title, msg, time);
            a.append(dot, wrap);
            list.appendChild(a);
        });
    };

    const poll = async () => {
        try {
            const res = await fetch(feedUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const data = await res.json();
            renderBadge(data.count ?? 0);
            renderList(data.items ?? []);
        } catch (e) {
            // Diamkan error jaringan sementara; coba lagi di interval berikutnya.
        }
    };

    // Ambil segera saat halaman dimuat agar notifikasi baru langsung tampak,
    // lalu polling berkala; berhenti sementara saat tab tidak aktif untuk hemat sumber daya.
    poll();
    let timer = setInterval(poll, INTERVAL);
    document.addEventListener('visibilitychange', () => {
        clearInterval(timer);
        if (!document.hidden) {
            poll();
            timer = setInterval(poll, INTERVAL);
        }
    });
}
