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
        Schema::create('bookmark_foto', function (Blueprint $table) {
            $table->id('bookmark_id');
            $table->unsignedBigInteger('foto_id');
            $table->unsignedBigInteger('album_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('member_id')->nullable();
            $table->timestamps();

            $table->foreign('foto_id')->references('foto_id')->on('foto');
            $table->foreign('album_id')->references('album_id')->on('album_foto');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('member_id')->references('member_id')->on('member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmark_foto');
    }
};
