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
        Schema::create('keluarga', function (Blueprint $table) {
            $table->bigIncrements('kd_keluarga');
            $table->unsignedBigInteger('kd_siswa')->unique();
            $table->string('no_kk', 16);
            $table->unsignedSmallInteger('anak_ke');
            $table->unsignedSmallInteger('jumlah_saudara');
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
        Schema::dropIfExists('keluarga');
    }
};
