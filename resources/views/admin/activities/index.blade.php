@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')

@section('content')
    <div class="card mb-4 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3" data-no-loader>
            <div class="grow">
                <label class="form-label">Cari deskripsi</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="Kata kunci...">
            </div>
            <div>
                <label class="form-label">Aksi</label>
                <select name="action" class="form-select">
                    <option value="">Semua</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}" @selected(request('action') === $a)>{{ ucfirst($a) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-secondary">Filter</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-default">
                <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Deskripsi</th><th>IP</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-sm">{{ $log->created_at->format('d-m-Y H:i') }}</td>
                            <td class="text-sm">{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td><x-badge class="bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">{{ ucfirst($log->action) }}</x-badge></td>
                            <td class="text-sm">{{ $log->description }}</td>
                            <td class="text-xs text-gray-400">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty title="Belum ada aktivitas" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$logs" /></div>
@endsection
