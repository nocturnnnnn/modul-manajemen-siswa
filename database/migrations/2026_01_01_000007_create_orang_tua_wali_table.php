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
        Schema::create('orang_tua_wali', function (Blueprint $table) {
            $table->bigIncrements('kd_ortu');
            $table->unsignedBigInteger('kd_siswa');
            $table->enum('hubungan', ['ayah', 'ibu', 'wali']);
            $table->string('nm_ortu', 100);
            $table->string('nik', 16)->nullable();
            $table->string('pekerjaan', 50)->nullable();
            $table->string('pendidikan', 30)->nullable();
            $table->string('no_hp', 15)->nullable();
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
        Schema::dropIfExists('orang_tua_wali');
    }
};
