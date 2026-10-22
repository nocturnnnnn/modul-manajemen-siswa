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
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->bigIncrements('kd_daftar');
            $table->unsignedBigInteger('kd_siswa');
            $table->string('no_pendaftaran', 20)->unique();
            $table->date('tgl_daftar');
            $table->string('jalur', 30);
            $table->string('asal_sekolah', 100);
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
        Schema::dropIfExists('pendaftaran');
    }
};
