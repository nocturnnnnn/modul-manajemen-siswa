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
        Schema::create('alumni', function (Blueprint $table) {
            $table->bigIncrements('kd_alumni');
            $table->unsignedBigInteger('kd_lulus')->unique();
            $table->string('kegiatan_lanjutan', 100);
            $table->string('instansi', 100);
            $table->string('kontak', 50)->nullable();
            $table->timestamps();

            $table->foreign('kd_lulus')
                ->references('kd_lulus')
                ->on('kelulusan')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};
