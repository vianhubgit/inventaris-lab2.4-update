<?php

namespace App\Exports;

use App\Models\StockAudit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockAuditsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @param  array<string,mixed>  $filters
     */
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection()
    {
        return StockAudit::query()
            ->with(['item', 'user'])
            ->when($this->filters['item_id'] ?? null, fn ($q, $v) => $q->where('item_id', $v))
            ->latest('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Barang', 'Tercatat', 'Fisik', 'Selisih', 'Petugas', 'Keterangan'];
    }

    /**
     * @param  StockAudit  $audit
     */
    public function map($audit): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $audit->tanggal?->format('d-m-Y'),
            $audit->item?->nama,
            $audit->jumlah_tercatat,
            $audit->jumlah_fisik,
            $audit->selisih,
            $audit->user?->name,
            $audit->keterangan,
        ];
    }
}
