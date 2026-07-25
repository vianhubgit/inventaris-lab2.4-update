<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah jenis laporan 'umum' (universal, tanpa kelompok & meja).
        DB::statement("ALTER TABLE reports MODIFY COLUMN type ENUM('rusak', 'hilang', 'umum') NOT NULL");
    }

    public function down(): void
    {
        // Kembalikan laporan 'umum' agar tidak melanggar enum lama.
        DB::statement("UPDATE reports SET type = 'rusak' WHERE type = 'umum'");
        DB::statement("ALTER TABLE reports MODIFY COLUMN type ENUM('rusak', 'hilang') NOT NULL");
    }
};
