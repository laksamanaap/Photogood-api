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
        Schema::create('member', function (Blueprint $table) {
            $table->id('member_id');
            $table->unsignedBigInteger('user_id');
            $table->string('username');
            $table->string('nama_lengkap');
            $table->string('password');
            $table->string('email');
            $table->string('alamat');
            $table->string('status');
            $table->string('followers')->nullable();
            $table->string('foto_profil')->nullable();
            $table->timestamps();  

            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member');
    }
};
