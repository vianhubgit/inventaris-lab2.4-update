    <input type="hidden" name="_prev" value="{{ url()->previous() }}">
@php
    $procurement = $procurement ?? null;

    // Lokasi barang terpilih (untuk preselect saat edit).
    $selItemId = old('item_id', $procurement?->item_id);
    $selItem = $items->firstWhere('id', $selItemId);
    $selLab = old('lab_id', $selItem?->lab_id);

    // Data cascade Lokasi (lab) > Barang untuk cascade.js (tanpa kelompok & posisi).
    $cascade = ['labs' => [], 'items' => []];
    foreach ($labs as $__lab) {
        $cascade['labs'][$__lab->id] = ['groups' => []];
    }
    foreach ($items as $__it) {
        $cascade['items'][] = [
            'id' => $__it->id,
            'nama' => $__it->nama,
            'lab_id' => $__it->lab_id,
            'lab_table_id' => null,
        ];
    }
    // Peta stok per barang untuk membatasi jumlah pinjaman.
    $stockMap = $items->pluck('jumlah_total', 'id');
@endphp

<div class="space-y-4">
    <script type="application/json" data-cascade>@json($cascade)</script>
    <script type="application/json" data-stock>@json($stockMap)</script>

    <div>
        <label class="form-label">Kelas Peminjam <span class="text-red-500">*</span></label>
        <select name="kelas" class="form-select" required>
            <option value="">— Pilih Kelas —</option>
            @foreach($kelasList as $kelas)
                <option value="{{ $kelas }}" @selected(old('kelas', $procurement?->kelas) === $kelas)>{{ $kelas }}</option>
            @endforeach
        </select>
        @error('kelas')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="form-label">Lokasi <span class="text-red-500">*</span></label>
            <select data-cascade-lab class="form-select" required>
                <option value="">— Pilih Lokasi —</option>
                @foreach($labs as $l)
                    <option value="{{ $l->id }}" @selected($selLab == $l->id)>{{ $l->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Barang <span class="text-red-500">*</span></label>
            <select name="item_id" data-cascade-item data-selected="{{ $selItemId }}" class="form-select" required>
                <option value="">— Tentukan Lokasi —</option>
            </select>
            @error('item_id')<p class="form-error">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="form-label">Jumlah Pinjaman <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah" min="1" value="{{ old('jumlah', $procurement?->jumlah ?? 1) }}" class="form-input" required data-jumlah>
        <p class="mt-1 text-xs text-gray-400" data-stock-info>Pilih barang untuk melihat stok tersedia.</p>
        @error('jumlah')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Alasan Peminjaman <span class="text-red-500">*</span></label>
        <textarea name="alasan" rows="3" class="form-textarea" required>{{ old('alasan', $procurement?->alasan) }}</textarea>
        @error('alasan')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button class="btn-primary">{{ $procurement ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}</button>
    <a href="{{ url()->previous(route('sekretaris.procurements.index')) }}" class="btn-secondary">Batal</a>
</div>

<script>
    (function () {
        // Batasi jumlah pinjaman sesuai stok barang yang dipilih.
        const stockEl = document.querySelector('script[data-stock]');
        if (!stockEl) return;
        const stock = JSON.parse(stockEl.textContent || '{}');
        const itemSel = document.querySelector('[data-cascade-item]');
        const jumlahInput = document.querySelector('[data-jumlah]');
        const info = document.querySelector('[data-stock-info]');
        if (!itemSel || !jumlahInput) return;

        const sync = () => {
            const id = itemSel.value;
            if (!id || stock[id] === undefined) {
                info.textContent = 'Pilih barang untuk melihat stok tersedia.';
                jumlahInput.removeAttribute('max');
                return;
            }
            const max = parseInt(stock[id], 10) || 0;
            jumlahInput.max = max;
            info.textContent = 'Stok tersedia: ' + max + ' unit. Maksimal pinjam ' + max + '.';
            if (parseInt(jumlahInput.value, 10) > max) jumlahInput.value = max;
            if (max === 0) info.textContent = 'Stok barang ini habis (0 unit).';
        };

        itemSel.addEventListener('change', sync);
        jumlahInput.addEventListener('input', () => {
            const max = parseInt(jumlahInput.max, 10);
            if (max && parseInt(jumlahInput.value, 10) > max) jumlahInput.value = max;
        });
        // Sinkronkan setelah cascade.js selesai mengisi opsi barang.
        setTimeout(sync, 150);
    })();
</script>
