@extends('layouts.app')

@section('title', 'Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('actions')
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pengguna
    </a>
@endsection

@section('content')
    <div class="card mb-4 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3" data-no-loader>
            <div class="grow">
                <label class="form-label">Cari</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="Nama atau username...">
            </div>
            <div>
                <label class="form-label">Peran</label>
                <select name="role" class="form-select">
                    <option value="">Semua</option>
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-secondary">Filter</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-default">
                <thead>
                    <tr><th>Nama</th><th>Username</th><th>Peran</th><th>Status</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="font-medium">{{ $u->name }}</td>
                            <td>{{ $u->username }}</td>
                            <td><x-badge class="bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">{{ $u->role->label() }}</x-badge></td>
                            <td>
                                @if($u->is_active)
                                    <x-badge class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Aktif</x-badge>
                                @else
                                    <x-badge class="bg-gray-200 text-gray-600 dark:bg-gray-700">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Hapus pengguna {{ $u->name }}?">
                                        @csrf @method('DELETE')
                                        <button class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty title="Tidak ada pengguna" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$users" /></div>
@endsection
