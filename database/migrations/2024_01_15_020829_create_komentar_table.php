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
        Schema::create('komentar_foto', function (Blueprint $table) {
            $table->id('komentar_id');
            $table->unsignedBigInteger('foto_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('member_id')->nullable();
            $table->string('isi_komentar');
            $table->timestamps();

            $table->foreign('foto_id')->references('foto_id')->on('foto')->onDelete('cascade');;
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');;
            $table->foreign('member_id')->references('member_id')->on('member')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komentar');
    }
};
