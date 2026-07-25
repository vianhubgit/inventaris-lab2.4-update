<?php

namespace App\Exports;

use App\Models\Repair;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RepairsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @param  array<string,mixed>  $filters
     */
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection()
    {
        return Repair::query()
            ->with(['item', 'user'])
            ->when($this->filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->latest('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Barang', 'Deskripsi', 'Biaya', 'Status', 'Petugas'];
    }

    /**
     * @param  Repair  $repair
     */
    public function map($repair): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $repair->tanggal?->format('d-m-Y'),
            $repair->item?->nama,
            $repair->deskripsi,
            $repair->biaya ? (float) $repair->biaya : 0,
            $repair->status->label(),
            $repair->user?->name,
        ];
    }
}
