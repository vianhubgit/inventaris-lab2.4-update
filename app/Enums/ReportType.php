<?php

namespace App\Enums;

enum ReportType: string
{
    case RUSAK = 'rusak';
    case HILANG = 'hilang';
    case UMUM = 'umum';

    public function label(): string
    {
        return match ($this) {
            self::RUSAK => 'Barang Rusak',
            self::HILANG => 'Barang Hilang',
            self::UMUM => 'Barang TEFA',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::RUSAK => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
            self::HILANG => 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
            self::UMUM => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        };
    }

    /** Apakah jenis laporan ini terikat ke kelompok & meja tertentu. */
    public function requiresPlacement(): bool
    {
        return $this !== self::UMUM;
    }

    /** @return array<string,string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $c) => [$c->value => $c->label()])
            ->all();
    }
}
