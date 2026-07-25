@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('actions')
    @if(auth()->user()->unreadNotifications()->count() > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button class="btn-secondary">Tandai semua dibaca</button>
        </form>
    @endif
    @if($notifications->total() > 0)
        <form method="POST" action="{{ route('notifications.clear') }}" data-confirm="Yakin ingin menghapus SEMUA notifikasi? Tindakan ini tidak dapat dibatalkan.">
            @csrf
            @method('DELETE')
            <button class="btn-danger">Bersihkan semua</button>
        </form>
    @endif
@endsection

@section('content')
    <div class="card divide-y divide-gray-100 dark:divide-gray-700">
        @forelse($notifications as $n)
            @php($data = $n->data)
            <div class="flex items-start gap-4 p-4 transition hover:bg-gray-50 dark:hover:bg-gray-700/40 {{ $n->read_at ? '' : 'bg-brand-50/60 dark:bg-brand-900/10' }}">
                <a href="{{ route('notifications.read', $n->id) }}" class="flex min-w-0 grow items-start gap-4">
                    <span @class([
                        'mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full',
                        'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300' => ($data['icon'] ?? '') === 'report',
                        'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300' => ($data['icon'] ?? '') === 'procurement',
                        'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300' => ($data['icon'] ?? '') === 'item',
                    ])>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </span>
                    <div class="min-w-0 grow">
                        <p class="font-semibold {{ $n->read_at ? '' : 'text-brand-700 dark:text-brand-300' }}">{{ $data['title'] ?? 'Notifikasi' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $data['message'] ?? '' }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $n->created_at->diffForHumans() }}</p>
                    </div>
                </a>
                <div class="flex shrink-0 items-center gap-3">
                    @unless($n->read_at)
                        <span class="h-2.5 w-2.5 rounded-full bg-brand-500" title="Belum dibaca"></span>
                    @endunless
                    <form method="POST" action="{{ route('notifications.destroy', $n->id) }}" data-confirm="Yakin ingin menghapus notifikasi ini?">
                        @csrf
                        @method('DELETE')
                        <button class="text-gray-400 transition hover:text-red-600" title="Hapus notifikasi" aria-label="Hapus notifikasi">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <x-empty title="Belum ada notifikasi" subtitle="Notifikasi akan muncul di sini." />
        @endforelse
    </div>

    <div class="mt-4"><x-paginator :paginator="$notifications" /></div>
@endsection
