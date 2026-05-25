<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolah')->onDelete('cascade');
            $table->string('tahun');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_ajarans');
    }
};
