    <input type="hidden" name="_prev" value="{{ url()->previous() }}">
@php
    $report = $report ?? null;
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
    // Laporan umum: barang dengan nama sama cukup tampil satu (dedup per lab).
    // Menu rusak & hilang tetap menampilkan seluruh barang seperti biasa.
    $isUmum = ($type->value ?? $report?->type?->value) === 'umum';
    $itemSource = $isUmum
        ? $items->unique(fn ($it) => $it->lab_id.'|'.mb_strtolower(trim($it->nama)))->values()
        : $items;
    foreach ($itemSource as $it) {
        $cascade['items'][] = ['id' => $it->id, 'nama' => $it->nama, 'lab_id' => $it->lab_id, 'lab_table_id' => $it->lab_table_id];
    }
@endphp

<script type="application/json" data-cascade>@json($cascade)</script>

<input type="hidden" name="type" value="{{ old('type', $type->value ?? $report?->type?->value) }}">

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <label class="form-label">Laboratorium <span class="text-red-500">*</span></label>
        <select name="lab_id" data-cascade-lab class="form-select" required>
            <option value="">— Pilih Lab —</option>
            @foreach($labs as $l)
                <option value="{{ $l->id }}" @selected(old('lab_id', $report?->lab_id) == $l->id)>{{ $l->nama }}</option>
            @endforeach
        </select>
        @error('lab_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    @if($type->requiresPlacement())
    <div>
        <label class="form-label">Kelompok</label>
        <select name="lab_group_id" data-cascade-group data-selected="{{ old('lab_group_id', $report?->lab_group_id) }}" class="form-select">
            <option value="">— Pilih Kelompok —</option>
        </select>
        @error('lab_group_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Meja</label>
        <select name="lab_table_id" data-cascade-table data-selected="{{ old('lab_table_id', $report?->lab_table_id) }}" class="form-select">
            <option value="">— Pilih Meja —</option>
        </select>
        @error('lab_table_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    @endif

    <div>
        <label class="form-label">Barang <span class="text-red-500">*</span></label>
        <select name="item_id" data-cascade-item data-selected="{{ old('item_id', $report?->item_id) }}" class="form-select" required>
            <option value="">— Pilih Lab dulu —</option>
        </select>
        @error('item_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Jumlah <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah" min="1" value="{{ old('jumlah', $report?->jumlah ?? 1) }}" class="form-input" required>
        @error('jumlah')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Foto (opsional)</label>
        <input type="file" name="foto" accept="image/*" class="form-input">
        <p class="mt-1 text-xs text-gray-400">JPG/PNG/WEBP, maks 4 MB.</p>
        @error('foto')<p class="form-error">{{ $message }}</p>@enderror
        @if($report?->foto_url)
            <img src="{{ $report->foto_url }}" class="mt-2 h-24 rounded border border-gray-200 dark:border-gray-700" alt="Foto saat ini">
        @endif
    </div>

    <div class="sm:col-span-2">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" rows="3" class="form-textarea" placeholder="Jelaskan kondisi barang...">{{ old('keterangan', $report?->keterangan) }}</textarea>
        @error('keterangan')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button class="btn-primary">{{ $report ? 'Simpan Perubahan' : 'Kirim Laporan' }}</button>
    <a href="{{ url()->previous(route('sekretaris.reports.index')) }}" class="btn-secondary">Batal</a>
</div>
