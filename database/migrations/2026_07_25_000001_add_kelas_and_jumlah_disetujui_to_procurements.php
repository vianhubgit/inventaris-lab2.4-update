<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            // Kelas peminjam (mis. "X TKJ A").
            $table->string('kelas')->nullable()->after('user_id');
            // Jumlah yang disetujui admin (boleh < jumlah yang diminta).
            $table->unsignedInteger('jumlah_disetujui')->nullable()->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'jumlah_disetujui']);
        });
    }
};
