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
        Schema::create('album_foto', function (Blueprint $table) {
            $table->id('album_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('member_id')->nullable();
            $table->string('nama_album');
            $table->string('deskripsi_album');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('member_id')->references('member_id')->on('member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album');
    }
};
