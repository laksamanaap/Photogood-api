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
        Schema::create('download_foto', function (Blueprint $table) {
            $table->id('download_id');
            $table->unsignedBigInteger('foto_id');
            $table->unsignedBigInteger('member_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('foto_id')->references('foto_id')->on('foto');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('member_id')->references('member_id')->on('member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download');
    }
};
