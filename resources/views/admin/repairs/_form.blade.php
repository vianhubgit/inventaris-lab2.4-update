    <input type="hidden" name="_prev" value="{{ url()->previous() }}">
@php($repair = $repair ?? null)

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="form-label">Barang <span class="text-red-500">*</span></label>
        <select name="item_id" class="form-select" required>
            <option value="">— Pilih Barang —</option>
            @foreach($items as $i)
                <option value="{{ $i->id }}" @selected(in_array(old('item_id', $repair?->item_id ?? ($report?->item_id ?? null)), $i->ids))>{{ $i->nama }}</option>
            @endforeach
        </select>
        @error('item_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    @if(isset($report) && $report)
        <input type="hidden" name="report_id" value="{{ $report->id }}">
    @endif

    <div>
        <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" value="{{ old('tanggal', optional($repair?->tanggal)->format('Y-m-d') ?? date('Y-m-d')) }}" class="form-input" required>
        @error('tanggal')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label">Status <span class="text-red-500">*</span></label>
        <select name="status" class="form-select" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $repair?->status?->value ?? 'proses') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label">Biaya (Rp)</label>
        <input type="number" name="biaya" min="0" step="0.01" value="{{ old('biaya', $repair?->biaya) }}" class="form-input" placeholder="opsional">
        @error('biaya')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label class="form-label">Deskripsi Perbaikan <span class="text-red-500">*</span></label>
        <textarea name="deskripsi" rows="3" class="form-textarea" required>{{ old('deskripsi', $repair?->deskripsi) }}</textarea>
        @error('deskripsi')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button class="btn-primary">{{ $repair ? 'Simpan Perubahan' : 'Simpan' }}</button>
    <a href="{{ url()->previous(route('admin.repairs.index')) }}" class="btn-secondary">Batal</a>
</div>
