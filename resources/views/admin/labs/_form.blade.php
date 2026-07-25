    <input type="hidden" name="_prev" value="{{ url()->previous() }}">
@php($lab = $lab ?? null)

<div class="space-y-4">
    <div>
        <label class="form-label">Nama Lab <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $lab?->nama) }}" class="form-input" placeholder="mis. Lab A" required>
        @error('nama')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label">Kode <span class="text-red-500">*</span></label>
        <input type="text" name="kode" value="{{ old('kode', $lab?->kode) }}" class="form-input" placeholder="mis. LAB_A" required>
        <p class="mt-1 text-xs text-gray-400">Tanpa spasi (huruf, angka, - dan _).</p>
        @error('kode')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label">Keterangan</label>
        <input type="text" name="keterangan" value="{{ old('keterangan', $lab?->keterangan) }}" class="form-input">
    </div>
<label class="flex items-center gap-2">
    <input
        type="checkbox"
        name="has_groups"
        value="1"
        @checked(old('has_groups', $lab?->has_groups ?? true))
        class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">

    <span class="text-sm">
        Memiliki tata letak (Kelompok atau Lemari).
    </span>
</label>
</div>

<div class="mt-6 flex gap-3">
    <button class="btn-primary">{{ $lab ? 'Simpan Perubahan' : 'Tambah Lab' }}</button>
    <a href="{{ url()->previous(route('admin.labs.index')) }}" class="btn-secondary">Batal</a>
</div>
