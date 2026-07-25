    <input type="hidden" name="_prev" value="{{ url()->previous() }}">
@php
    $item = $item ?? null;
    // Bangun struktur data untuk dropdown bertingkat lab → meja.
    $cascade = ['labs' => [], 'items' => []];
    foreach ($labs as $lab) {
        $groups = [];
        foreach ($lab->groups as $g) {
            $groups[$g->id] = [
                'nama' => $g->display_name,
                'tables' => $g->tables->map(fn ($t) => ['id' => $t->id, 'nama' => $t->display_name])->values(),
            ];
        }
        $cascade['labs'][$lab->id] = ['groups' => $groups];
    }
@endphp

<script type="application/json" data-cascade>@json($cascade)</script>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="form-label">Nama Barang <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $item?->nama) }}" class="form-input" required>
        @error('nama')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Kategori <span class="text-red-500">*</span></label>
        <select name="category_id" class="form-select" required>
            <option value="">— Pilih Kategori —</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $item?->category_id) == $c->id)>{{ $c->nama }}</option>
            @endforeach
        </select>
        @error('category_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Status <span class="text-red-500">*</span></label>
        <select name="status" class="form-select" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $item?->status?->value ?? 'baik') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Lokasi (Lab) <span class="text-red-500">*</span></label>
        <select name="lab_id" data-cascade-lab class="form-select" required>
            <option value="">— Pilih Lab —</option>
            @foreach($labs as $l)
                <option value="{{ $l->id }}" @selected(old('lab_id', $item?->lab_id) == $l->id)>{{ $l->nama }}</option>
            @endforeach
        </select>
        @error('lab_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Unit (opsional)</label>
        <select name="lab_table_id" data-cascade-table data-selected="{{ old('lab_table_id', $item?->lab_table_id) }}" class="form-select">
            <option value="">— Tanpa Meja —</option>
        </select>
        <p class="mt-1 text-xs text-gray-400">Hanya untuk lab berkelompok (Lab A/B). Kosongkan untuk TEFA.</p>
        @error('lab_table_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Jumlah Total <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_total" min="0" value="{{ old('jumlah_total', $item?->jumlah_total ?? 1) }}" class="form-input" required>
        @error('jumlah_total')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" rows="3" class="form-textarea">{{ old('keterangan', $item?->keterangan) }}</textarea>
        @error('keterangan')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button class="btn-primary">{{ $item ? 'Simpan Perubahan' : 'Tambah Barang' }}</button>
    <a href="{{ url()->previous(route('admin.items.index')) }}" class="btn-secondary">Batal</a>
</div>
