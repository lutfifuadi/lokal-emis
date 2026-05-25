<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('google_sheet_settings', function (Blueprint $table) {
            $table->longText('credentials_json')->nullable()->after('spreadsheet_id')
                ->comment('JSON credentials Service Account Google API');
        });
    }

    public function down(): void
    {
        Schema::table('google_sheet_settings', function (Blueprint $table) {
            $table->dropColumn('credentials_json');
        });
    }
};
