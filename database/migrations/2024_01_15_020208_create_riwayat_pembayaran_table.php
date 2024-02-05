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
        Schema::create('riwayat_pembayaran', function (Blueprint $table) {
            $table->string('riwayat_id');
            $table->string('status');
            $table->unsignedBigInteger('user_id');
            $table->integer('nominal_pembayaran');
            $table->string('checkout_link');
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.$
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pembayaran');
    }
};
