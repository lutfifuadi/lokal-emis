<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['kontak_darurat', 'tinggi_badan', 'berat_badan']);
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('kontak_darurat')->nullable()->after('no_hp');
            $table->integer('tinggi_badan')->nullable()->after('no_kip');
            $table->integer('berat_badan')->nullable()->after('tinggi_badan');
        });
    }
};
