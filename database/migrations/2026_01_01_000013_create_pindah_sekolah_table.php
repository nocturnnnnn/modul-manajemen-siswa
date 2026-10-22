<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pindah_sekolah', function (Blueprint $table) {
            $table->bigIncrements('kd_pindah');
            $table->unsignedBigInteger('kd_siswa');
            $table->string('sekolah_tujuan', 100);
            $table->date('tgl_pindah');
            $table->string('alasan', 255);
            $table->timestamps();

            $table->foreign('kd_siswa')
                ->references('kd_siswa')
                ->on('siswa')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pindah_sekolah');
    }
};
