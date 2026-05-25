<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Alamat detail
            $table->string('rt', 5)->nullable()->after('kode_pos');
            $table->string('rw', 5)->nullable()->after('rt');
            $table->string('desa', 255)->nullable()->after('rw');
            $table->string('kecamatan', 255)->nullable()->after('desa');
            $table->string('kabupaten', 255)->nullable()->after('kecamatan');
            $table->string('provinsi', 255)->nullable()->after('kabupaten');

            // Sosial
            $table->string('status_dalam_keluarga', 100)->nullable()->after('jml_saudara');
            $table->string('status_tempat_tinggal', 100)->nullable()->after('status_dalam_keluarga');
            $table->string('pembiaya', 100)->nullable()->after('status_tempat_tinggal');

            // Orang tua - tempat lahir
            $table->string('tempat_lahir_ayah', 255)->nullable()->after('penghasilan_ayah');
            $table->string('tempat_lahir_ibu', 255)->nullable()->after('penghasilan_ibu');

            // Wali
            $table->string('nama_wali', 255)->nullable()->after('tempat_lahir_ibu');
            $table->string('nik_wali', 20)->nullable()->after('nama_wali');
            $table->string('no_hp_wali', 20)->nullable()->after('nik_wali');
            $table->string('pendidikan_wali', 100)->nullable()->after('no_hp_wali');
            $table->string('pekerjaan_wali', 100)->nullable()->after('pendidikan_wali');
            $table->string('penghasilan_wali', 50)->nullable()->after('pekerjaan_wali');
            $table->text('alamat_wali')->nullable()->after('penghasilan_wali');

            // Sekolah asal
            $table->string('asal_sekolah', 255)->nullable()->after('alamat_wali');
            $table->string('npsn_sekolah_asal', 20)->nullable()->after('asal_sekolah');
            $table->string('jenis_sekolah_asal', 100)->nullable()->after('npsn_sekolah_asal');
            $table->string('status_sekolah_asal', 100)->nullable()->after('jenis_sekolah_asal');
            $table->text('alamat_sekolah_asal')->nullable()->after('status_sekolah_asal');

            // Minat & kesehatan
            $table->string('cita_cita', 255)->nullable()->after('alamat_sekolah_asal');
            $table->string('hobi', 255)->nullable()->after('cita_cita');
            $table->text('riwayat_penyakit')->nullable()->after('hobi');

            // Prestasi & beasiswa (JSON)
            $table->text('prestasi')->nullable()->after('riwayat_penyakit');
            $table->text('beasiswa')->nullable()->after('prestasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'rt', 'rw', 'desa', 'kecamatan', 'kabupaten', 'provinsi',
                'status_dalam_keluarga', 'status_tempat_tinggal', 'pembiaya',
                'tempat_lahir_ayah', 'tempat_lahir_ibu',
                'nama_wali', 'nik_wali', 'no_hp_wali', 'pendidikan_wali',
                'pekerjaan_wali', 'penghasilan_wali', 'alamat_wali',
                'asal_sekolah', 'npsn_sekolah_asal', 'jenis_sekolah_asal',
                'status_sekolah_asal', 'alamat_sekolah_asal',
                'cita_cita', 'hobi', 'riwayat_penyakit',
                'prestasi', 'beasiswa',
            ]);
        });
    }
};
