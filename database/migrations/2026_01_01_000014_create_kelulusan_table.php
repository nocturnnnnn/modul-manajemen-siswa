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
        Schema::create('kelulusan', function (Blueprint $table) {
            $table->bigIncrements('kd_lulus');
            $table->unsignedBigInteger('kd_siswa')->unique();
            $table->unsignedInteger('kd_tahun');
            $table->date('tgl_lulus');
            $table->string('no_ijazah', 30)->unique()->nullable();
            $table->timestamps();

            $table->foreign('kd_siswa')
                ->references('kd_siswa')
                ->on('siswa')
                ->cascadeOnDelete();

            $table->foreign('kd_tahun')
                ->references('kd_tahun')
                ->on('tahun_ajaran')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelulusan');
    }
};
