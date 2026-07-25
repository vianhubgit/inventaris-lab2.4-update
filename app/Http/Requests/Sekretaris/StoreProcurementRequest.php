<?php

namespace App\Http\Requests\Sekretaris;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProcurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Procurement::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'kelas' => ['required', Rule::exists('kelas', 'nama')->whereNull('deleted_at')],
            'item_id' => ['required', 'exists:items,id'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:10000'],
            'alasan' => ['required', 'string', 'max:1000'],
        ];
    }

    /** Jumlah pinjaman tidak boleh melebihi stok barang di inventaris. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $itemId = $this->input('item_id');
            $jumlah = (int) $this->input('jumlah');

            if ($itemId && $jumlah > 0) {
                $stok = (int) (Item::whereKey($itemId)->value('jumlah_total') ?? 0);
                if ($jumlah > $stok) {
                    $validator->errors()->add(
                        'jumlah',
                        "Jumlah melebihi stok yang tersedia (stok: {$stok})."
                    );
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'kelas' => 'kelas peminjam',
            'item_id' => 'barang',
            'jumlah' => 'jumlah',
            'alasan' => 'alasan',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Silakan pilih barang yang akan dipinjam.',
            'kelas.required' => 'Silakan pilih kelas peminjam.',
        ];
    }
}
