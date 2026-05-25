<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Bio Data
            $table->string('tempat_lahir')->nullable()->after('nik');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('jenis_kelamin')->nullable()->after('tanggal_lahir');
            $table->string('agama')->nullable()->after('jenis_kelamin');

            // Detail Alamat
            $table->string('kode_pos')->nullable()->after('alamat');

            // Data Keluarga
            $table->unsignedTinyInteger('anak_ke')->nullable()->after('kode_pos');
            $table->unsignedTinyInteger('jml_saudara')->nullable()->after('anak_ke');

            // Data Ayah
            $table->string('nama_ayah')->nullable()->after('jml_saudara');
            $table->string('nik_ayah')->nullable()->after('nama_ayah');
            $table->string('pendidikan_ayah')->nullable()->after('nik_ayah');
            $table->string('pekerjaan_ayah')->nullable()->after('pendidikan_ayah');
            $table->string('penghasilan_ayah')->nullable()->after('pekerjaan_ayah');

            // Data Ibu
            $table->string('nama_ibu')->nullable()->after('penghasilan_ayah');
            $table->string('nik_ibu')->nullable()->after('nama_ibu');
            $table->string('pendidikan_ibu')->nullable()->after('nik_ibu');
            $table->string('pekerjaan_ibu')->nullable()->after('pendidikan_ibu');
            $table->string('penghasilan_ibu')->nullable()->after('pekerjaan_ibu');

            // Kontak Orang Tua
            $table->string('no_hp_ortu')->nullable()->after('penghasilan_ibu');

            // Data Tambahan
            $table->string('kewarganegaraan')->default('WNI')->after('no_hp_ortu');
            $table->string('kebutuhan_khusus')->nullable()->after('kewarganegaraan');
            $table->string('no_kip')->nullable()->after('kebutuhan_khusus');
            $table->string('no_kk')->nullable()->after('no_kip');
            $table->string('nama_kepala_keluarga')->nullable()->after('no_kk');
            $table->string('transportasi')->nullable()->after('nama_kepala_keluarga');
            $table->decimal('jarak_tempuh', 5, 2)->nullable()->after('transportasi');

            // Data Fisik
            $table->unsignedSmallInteger('tinggi_badan')->nullable()->after('jarak_tempuh');
            $table->unsignedSmallInteger('berat_badan')->nullable()->after('tinggi_badan');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama',
                'kode_pos', 'anak_ke', 'jml_saudara',
                'nama_ayah', 'nik_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah',
                'nama_ibu', 'nik_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu',
                'no_hp_ortu',
                'kewarganegaraan', 'kebutuhan_khusus', 'no_kip', 'no_kk', 'nama_kepala_keluarga',
                'transportasi', 'jarak_tempuh',
                'tinggi_badan', 'berat_badan',
            ]);
        });
    }
};
