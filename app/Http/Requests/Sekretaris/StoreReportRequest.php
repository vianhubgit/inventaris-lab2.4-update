<?php

namespace App\Http\Requests\Sekretaris;

use App\Enums\ReportType;
use App\Models\Item;
use App\Models\LabGroup;
use App\Models\LabTable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Report::class) ?? false;
    }

    /** Laporan umum bersifat universal: tanpa kelompok & meja (jumlah tetap diisi). */
    protected function prepareForValidation(): void
    {
        if ($this->input('type') === ReportType::UMUM->value) {
            $this->merge([
                'lab_group_id' => null,
                'lab_table_id' => null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ReportType::class)],
            'lab_id' => ['required', 'exists:labs,id'],
            'lab_group_id' => ['nullable', 'exists:lab_groups,id'],
            'lab_table_id' => ['nullable', 'exists:lab_tables,id'],
            'item_id' => ['required', 'exists:items,id'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:10000'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ];
    }

    /** Validasi konsistensi lokasi (kelompok & meja sesuai lab) dan barang sesuai lab. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $labId = $this->input('lab_id');

            if ($groupId = $this->input('lab_group_id')) {
                $ok = LabGroup::where('id', $groupId)->where('lab_id', $labId)->exists();
                if (! $ok) {
                    $validator->errors()->add('lab_group_id', 'Kelompok tidak sesuai dengan laboratorium.');
                }
            }

            if ($tableId = $this->input('lab_table_id')) {
                $ok = LabTable::where('id', $tableId)
                    ->whereHas('group', fn ($q) => $q->where('lab_id', $labId))
                    ->exists();
                if (! $ok) {
                    $validator->errors()->add('lab_table_id', 'Meja tidak sesuai dengan laboratorium.');
                }
            }

            if ($itemId = $this->input('item_id')) {
                $ok = Item::where('id', $itemId)->where('lab_id', $labId)->exists();
                if (! $ok) {
                    $validator->errors()->add('item_id', 'Barang tidak berada di laboratorium yang dipilih.');
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'type' => 'jenis laporan',
            'lab_id' => 'laboratorium',
            'lab_group_id' => 'kelompok',
            'lab_table_id' => 'meja',
            'item_id' => 'barang',
            'jumlah' => 'jumlah',
            'foto' => 'foto',
        ];
    }
}
