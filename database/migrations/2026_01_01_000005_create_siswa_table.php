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
        Schema::create('siswa', function (Blueprint $table) {
            $table->bigIncrements('kd_siswa');
            $table->string('nis', 20)->unique();
            $table->string('nisn', 10)->unique();
            $table->string('nik', 16)->unique();
            $table->string('nm_siswa', 100);
            $table->string('tempat_lahir', 50);
            $table->date('tgl_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->unsignedInteger('kd_agama');
            $table->text('alamat');
            $table->string('foto', 255)->nullable();
            $table->enum('status_siswa', ['aktif', 'mutasi', 'pindah', 'lulus', 'alumni'])->default('aktif');
            $table->timestamps();

            $table->foreign('kd_agama')
                ->references('kd_agama')
                ->on('agama')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
