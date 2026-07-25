<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed kelas awal (sesuai dropdown lama) agar master langsung terisi.
        $now = now();
        $seed = ['X TKJ A', 'X TKJ B', 'XI TKJ A', 'XI TKJ B', 'XII TKJ A', 'XII TKJ B'];
        DB::table('kelas')->insert(array_map(
            fn ($nama) => ['nama' => $nama, 'created_at' => $now, 'updated_at' => $now],
            $seed
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
