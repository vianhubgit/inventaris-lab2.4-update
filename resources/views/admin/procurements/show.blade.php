@extends('layouts.app')

@section('title', 'Detail Pengajuan')
@section('page-title', 'Pengajuan #' . $procurement->id)

@section('actions')
    <a href="{{ url()->previous(route('admin.procurements.index')) }}" class="btn-secondary">Kembali</a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="card p-6 lg:col-span-2">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div><dt class="text-xs uppercase text-gray-400">Barang</dt><dd class="font-medium">{{ $procurement->nama_barang }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-400">Kelas Peminjam</dt><dd class="font-medium">{{ $procurement->kelas ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-400">Jumlah Diminta</dt><dd class="font-medium">{{ $procurement->jumlah }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-400">Jumlah Disetujui</dt><dd class="font-medium">{{ $procurement->jumlah_disetujui ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-400">Pengaju</dt><dd>{{ $procurement->user?->name }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-400">Tanggal</dt><dd>{{ $procurement->requested_at?->translatedFormat('d F Y H:i') }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Alasan</dt><dd>{{ $procurement->alasan }}</dd></div>
                @if($procurement->catatan_admin)
                    <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Catatan Admin</dt><dd>{{ $procurement->catatan_admin }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="mb-1 font-semibold">Status Saat Ini</h3>
                <p class="mb-4"><x-badge :class="$procurement->status->badge()">{{ $procurement->status->label() }}</x-badge></p>

                <form method="POST" action="{{ route('admin.procurements.status', $procurement) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="form-label">Ubah Status</label>
                        <select name="status" class="form-select">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($procurement->status->value === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Jumlah Disetujui</label>
                        <input type="number" name="jumlah_disetujui" min="0" max="{{ $procurement->jumlah }}"
                               value="{{ old('jumlah_disetujui', $procurement->jumlah_disetujui ?? $procurement->jumlah) }}"
                               class="form-input">
                        <p class="mt-1 text-xs text-gray-400">Diminta {{ $procurement->jumlah }} unit. Boleh disetujui lebih sedikit (maks {{ $procurement->jumlah }}).</p>
                        @error('jumlah_disetujui')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan_admin" rows="3" class="form-textarea">{{ $procurement->catatan_admin }}</textarea>
                    </div>
                    <button class="btn-primary w-full">Simpan</button>
                </form>
            </div>

            <div class="card p-6">
                <form method="POST" action="{{ route('admin.procurements.destroy', $procurement) }}" data-confirm="Hapus pengajuan ini?">
                    @csrf @method('DELETE')
                    <button class="btn-danger w-full">Hapus Laporan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
