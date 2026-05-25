<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurusans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolah')->onDelete('cascade');
            $table->string('nama');
            $table->string('kode');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurusans');
    }
};
