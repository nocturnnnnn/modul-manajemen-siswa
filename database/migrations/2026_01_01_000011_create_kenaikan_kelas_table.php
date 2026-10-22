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
        Schema::create('kenaikan_kelas', function (Blueprint $table) {
            $table->bigIncrements('kd_kenaikan');
            $table->unsignedBigInteger('kd_siswa');
            $table->unsignedInteger('kd_tahun');
            $table->unsignedInteger('kd_kelas_asal');
            $table->unsignedInteger('kd_kelas_tujuan');
            $table->enum('status', ['naik', 'tinggal']);
            $table->timestamps();

            $table->foreign('kd_siswa')
                ->references('kd_siswa')
                ->on('siswa')
                ->cascadeOnDelete();

            $table->foreign('kd_tahun')
                ->references('kd_tahun')
                ->on('tahun_ajaran')
                ->restrictOnDelete();

            $table->foreign('kd_kelas_asal')
                ->references('kd_kelas')
                ->on('kelas')
                ->restrictOnDelete();

            $table->foreign('kd_kelas_tujuan')
                ->references('kd_kelas')
                ->on('kelas')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kenaikan_kelas');
    }
};
