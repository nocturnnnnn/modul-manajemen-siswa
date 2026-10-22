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
        Schema::create('kontak_siswa', function (Blueprint $table) {
            $table->bigIncrements('kd_kontak');
            $table->unsignedBigInteger('kd_siswa');
            $table->string('jenis_kontak', 20); // HP, WhatsApp, Rumah
            $table->string('nomor', 15);
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
        Schema::dropIfExists('kontak_siswa');
    }
};
