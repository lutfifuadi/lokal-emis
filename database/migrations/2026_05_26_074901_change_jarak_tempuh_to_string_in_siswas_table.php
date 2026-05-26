<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah kolom jarak_tempuh dari decimal ke string
     * karena data dari Google Sheets berisi teks deskriptif
     * seperti "KURANG DARI 5 KM", "ANTARA 5 – 10 KM", dll.
     */
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('jarak_tempuh', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Saat rollback, kosongkan nilai yang tidak numerik terlebih dahulu
            // lalu ubah kembali ke decimal
            \DB::statement("UPDATE siswas SET jarak_tempuh = NULL WHERE jarak_tempuh IS NOT NULL AND jarak_tempuh NOT REGEXP '^[0-9]+(\\.[0-9]+)?$'");
            $table->decimal('jarak_tempuh', 5, 2)->nullable()->change();
        });
    }
};
