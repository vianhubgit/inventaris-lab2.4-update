<?php

namespace App\Models;

use App\Enums\ProcurementStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procurement extends Model
{
    /** @use HasFactory<\Database\Factories\ProcurementFactory> */
    use HasFactory, SoftDeletes;

    /** Pilihan kelas peminjam untuk dropdown & validasi. */
    public const KELAS_OPTIONS = [
        'X TKJ A',
        'X TKJ B',
        'XI TKJ A',
        'XI TKJ B',
        'XII TKJ A',
        'XII TKJ B',
    ];

    protected $fillable = [
        'user_id',
        'kelas',
        'category_id',
        'item_id',
        'is_new_item',
        'nama_barang_baru',
        'jumlah',
        'jumlah_disetujui',
        'alasan',
        'status',
        'catatan_admin',
        'requested_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProcurementStatus::class,
            'is_new_item' => 'boolean',
            'jumlah' => 'integer',
            'jumlah_disetujui' => 'integer',
            'requested_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** Nama barang yang diajukan: item lama atau nama barang baru. */
    public function getNamaBarangAttribute(): string
    {
        if ($this->is_new_item) {
            return $this->nama_barang_baru ?: '(Barang Baru)';
        }

        return $this->item?->nama ?? '-';
    }
}
