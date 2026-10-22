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
        Schema::create('dokumen_siswa', function (Blueprint $table) {
            $table->bigIncrements('kd_dokumen');
            $table->unsignedBigInteger('kd_siswa');
            $table->unsignedInteger('kd_jenis');
            $table->string('nomor_dokumen', 50)->nullable();
            $table->string('file_path', 255);
            $table->enum('status_verifikasi', ['belum', 'terverifikasi', 'ditolak'])->default('belum');
            $table->string('catatan_verifikasi', 255)->nullable();
            $table->dateTime('tgl_upload');
            $table->timestamps();

            $table->foreign('kd_siswa')
                ->references('kd_siswa')
                ->on('siswa')
                ->cascadeOnDelete();

            $table->foreign('kd_jenis')
                ->references('kd_jenis')
                ->on('jenis_dokumen')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_siswa');
    }
};
