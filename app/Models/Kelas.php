<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    /** Nama tabel eksplisit agar tidak dipluralkan menjadi bentuk lain. */
    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'keterangan',
    ];

    /** Berapa banyak pengajuan peminjaman yang memakai kelas ini. */
    public function getUsageCountAttribute(): int
    {
        return Procurement::where('kelas', $this->nama)->count();
    }
}
