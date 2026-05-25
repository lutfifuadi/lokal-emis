<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sekolah_id')->constrained('sekolah')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');
            $table->string('nisn')->unique();
            $table->string('nik')->nullable();
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('kontak_darurat')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
