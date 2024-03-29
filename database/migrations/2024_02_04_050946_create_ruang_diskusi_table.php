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
        Schema::create('ruang_diskusi', function (Blueprint $table) {
            $table->string('ruang_id')->primary();
            $table->string('nama_ruang');
            $table->string('deskripsi_ruang');
            $table->string('user_id');
            $table->string('profil_ruang')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruang_diskusi');
    }
};
