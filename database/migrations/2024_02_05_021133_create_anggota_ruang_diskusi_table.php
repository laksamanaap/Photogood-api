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
        Schema::create('anggota_ruang_diskusi', function (Blueprint $table) {
            $table->id('anggota_id');
            $table->string('ruang_id'); 
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('ruang_id')->references('ruang_id')->on('ruang_diskusi');
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_ruang_diskusi');
    }
};
