<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->string('nsm')->nullable()->after('npsn');
            $table->string('website')->nullable()->after('email');
            $table->string('nama_kepala')->nullable()->after('website');
            $table->string('nip_kepala')->nullable()->after('nama_kepala');
            $table->string('jenis_sekolah')->nullable()->after('nip_kepala');
            $table->string('status_sekolah')->nullable()->after('jenis_sekolah');
            $table->string('jenjang')->nullable()->after('status_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn(['nsm', 'website', 'nama_kepala', 'nip_kepala', 'jenis_sekolah', 'status_sekolah', 'jenjang']);
        });
    }
};
