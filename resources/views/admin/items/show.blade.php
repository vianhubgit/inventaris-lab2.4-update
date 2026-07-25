@extends('layouts.app')

@section('title', $item->nama)
@section('page-title', $item->nama)
@section('breadcrumb', 'Detail barang & riwayat')

@section('actions')
    <a href="{{ route('admin.items.edit', $item) }}" class="btn-secondary">Edit</a>
    <a href="{{ url()->previous(route('admin.items.index')) }}" class="btn-secondary">Kembali</a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div><dt class="text-xs uppercase text-gray-400">Kategori</dt><dd class="font-medium">{{ $item->category?->nama }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Status</dt><dd><x-badge :class="$item->status->badge()">{{ $item->status->label() }}</x-badge></dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Lokasi</dt><dd class="font-medium">{{ $item->lokasi_lengkap }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Jumlah Total</dt><dd class="text-2xl font-bold">{{ $item->jumlah_total }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Keterangan</dt><dd>{{ $item->keterangan ?: '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Dibuat</dt><dd>{{ $item->created_at->translatedFormat('d F Y H:i') }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Diperbarui</dt><dd>{{ $item->updated_at->translatedFormat('d F Y H:i') }}</dd></div>
                </dl>
            </div>

            {{-- Riwayat perbaikan --}}
            <div class="card p-6">
                <h3 class="mb-4 font-semibold">Riwayat Perbaikan</h3>
                @forelse($item->repairs as $r)
                    <div class="mb-3 flex items-start justify-between gap-3 border-b border-gray-100 pb-3 last:border-0 dark:border-gray-700">
                        <div>
                            <p class="text-sm">{{ $r->deskripsi }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $r->tanggal?->format('d-m-Y') }} &bull; {{ $r->user?->name }}</p>
                        </div>
                        <x-badge :class="$r->status->badge()">{{ $r->status->label() }}</x-badge>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada riwayat perbaikan.</p>
                @endforelse
            </div>

            {{-- Riwayat audit --}}
            <div class="card p-6">
                <h3 class="mb-4 font-semibold">Riwayat Audit</h3>
                @forelse($item->audits as $a)
                    <div class="mb-2 flex items-center justify-between text-sm">
                        <span>{{ $a->tanggal?->format('d-m-Y') }} — tercatat {{ $a->jumlah_tercatat }}, fisik {{ $a->jumlah_fisik }}</span>
                        <span class="font-semibold {{ $a->selisih == 0 ? 'text-emerald-600' : 'text-red-600' }}">Selisih {{ $a->selisih }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada audit.</p>
                @endforelse
            </div>
        </div>

        {{-- Atur jumlah --}}
        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="mb-4 font-semibold">Atur Jumlah Cepat</h3>
                <form method="POST" action="{{ route('admin.items.adjust', $item) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="form-label">Aksi</label>
                        <select name="mode" class="form-select">
                            <option value="tambah">Tambah (+)</option>
                            <option value="kurang">Kurangi (−)</option>
                            <option value="set">Set ke nilai</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" min="0" value="1" class="form-input" required>
                    </div>
                    <button class="btn-primary w-full">Terapkan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
