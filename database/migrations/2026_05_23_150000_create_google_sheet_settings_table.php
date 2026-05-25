<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_sheet_settings', function (Blueprint $table) {
            $table->id();
            $table->string('entity', 50)->unique()->comment('sekolah, jurusan, tahun-ajaran, kelas, users, siswa');
            $table->string('spreadsheet_url');
            $table->string('sheet_name');
            $table->string('sheet_range', 50)->default('A:Z');
            $table->string('spreadsheet_id')->nullable();
            $table->json('mapping_config')->nullable()->comment('Mapping kolom database ke kolom sheet');
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('last_test_at')->nullable();
            $table->boolean('last_test_ok')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_sheet_settings');
    }
};
