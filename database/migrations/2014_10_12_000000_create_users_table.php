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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username');
            $table->string('nama_lengkap')->nullable();
            $table->string('password');
            $table->string('email');
            $table->string('alamat')->nullable();
            $table->string('status');
            $table->string('foto_profil')->nullable();
            $table->string('login_tokens')->nullable();
            $table->timestamps();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
