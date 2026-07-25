@php
    $nav = fn ($pattern) => request()->routeIs($pattern) ? 'sidebar-link sidebar-link-active' : 'sidebar-link';
@endphp

<p class="px-3 pb-1 pt-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Menu</p>

<a href="{{ route('sekretaris.dashboard') }}" class="{{ $nav('sekretaris.dashboard') }}">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>
<a href="{{ route('sekretaris.inventory.index') }}" class="{{ $nav('sekretaris.inventory.*') }}">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    Lihat Inventaris
</a>
<a href="{{ route('notifications.index') }}" class="{{ $nav('notifications.*') }}">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    Notifikasi
</a>

<p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Laporan</p>

<a href="{{ route('sekretaris.reports.create', ['type' => 'rusak']) }}" class="{{ request()->routeIs('sekretaris.reports.create') && ! in_array(request('type'), ['hilang', 'umum']) ? 'sidebar-link sidebar-link-active' : 'sidebar-link' }}">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.5 0L3.18 16.25A2 2 0 005 19z"/></svg>
    Lapor Barang Rusak
</a>
<a href="{{ route('sekretaris.reports.create', ['type' => 'hilang']) }}" class="{{ request()->routeIs('sekretaris.reports.create') && request('type') === 'hilang' ? 'sidebar-link sidebar-link-active' : 'sidebar-link' }}">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
    Lapor Barang Hilang
</a>
<a href="{{ route('sekretaris.reports.create', ['type' => 'umum']) }}" class="{{ request()->routeIs('sekretaris.reports.create') && request('type') === 'umum' ? 'sidebar-link sidebar-link-active' : 'sidebar-link' }}">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
    Lapor Barang TEFA
</a>
<a href="{{ route('sekretaris.reports.index') }}" class="{{ $nav('sekretaris.reports.index') }}">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
    Riwayat Laporan
</a>

<p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Pengajuan</p>

<a href="{{ route('sekretaris.procurements.create') }}" class="{{ $nav('sekretaris.procurements.create') }}">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Peminjaman Barang
</a>
<a href="{{ route('sekretaris.procurements.index') }}" class="{{ $nav('sekretaris.procurements.index') }}">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Status Pinjaman
</a>
