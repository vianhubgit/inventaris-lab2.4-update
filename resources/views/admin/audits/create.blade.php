@extends('layouts.app')

@section('title', 'Audit Baru')
@section('page-title', 'Audit Inventaris Baru')

@section('content')
    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('admin.audits.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Barang <span class="text-red-500">*</span></label>
                <select name="item_id" class="form-select" required>
                    <option value="">— Pilih Barang —</option>
                    @foreach($items as $i)
                        <option value="{{ $i->id }}" @selected(in_array(old('item_id'), $i->ids))>{{ $i->nama }} (tercatat: {{ $i->jumlah_total }})</option>
                    @endforeach
                </select>
                @error('item_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="form-label">Jumlah Fisik <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_fisik" min="0" value="{{ old('jumlah_fisik') }}" class="form-input" required>
                    @error('jumlah_fisik')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="form-input" required>
                    @error('tanggal')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" rows="3" class="form-textarea">{{ old('keterangan') }}</textarea>
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="sinkronkan" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm">Sinkronkan jumlah tercatat ke hasil fisik</span>
            </label>
            <div class="flex gap-3">
                <button class="btn-primary">Simpan Audit</button>
                <a href="{{ url()->previous(route('admin.audits.index')) }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
