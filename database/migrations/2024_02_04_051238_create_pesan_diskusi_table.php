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
        Schema::create('pesan_diskusi', function (Blueprint $table) {
            $table->id('pesan_id');
            $table->string('isi_pesan');
            $table->unsignedBigInteger('user_id');
            $table->string('ruang_id'); 
            $table->unsignedBigInteger('member_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');;
            $table->foreign('member_id')->references('member_id')->on('member')->onDelete('cascade');;
            $table->foreign('ruang_id')->references('ruang_id')->on('ruang_diskusi')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan_diskusi');
    }
};
