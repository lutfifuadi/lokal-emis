<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sekolah_id')->constrained('sekolah')->onDelete('cascade');
            $table->string('nik')->nullable();
            $table->string('nuptk')->nullable();
            $table->string('nama');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
