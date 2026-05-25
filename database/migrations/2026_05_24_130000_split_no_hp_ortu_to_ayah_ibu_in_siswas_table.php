<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('no_hp_ayah', 20)->nullable()->after('penghasilan_ibu');
            $table->string('no_hp_ibu', 20)->nullable()->after('no_hp_ayah');
            $table->dropColumn('no_hp_ortu');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('no_hp_ortu')->nullable()->after('penghasilan_ibu');
            $table->dropColumn(['no_hp_ayah', 'no_hp_ibu']);
        });
    }
};
