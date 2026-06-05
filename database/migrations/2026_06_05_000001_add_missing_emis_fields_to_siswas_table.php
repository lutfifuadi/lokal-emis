<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Data Murid - kontak & tanggal detail
            $table->string('email', 255)->nullable()->after('agama');
            $table->unsignedTinyInteger('bulan_lahir')->nullable()->after('tanggal_lahir');
            $table->unsignedSmallInteger('tahun_lahir')->nullable()->after('bulan_lahir');

            // Pendidikan & Latar Belakang
            $table->string('pernah_tk', 10)->nullable()->after('hobi');
            $table->string('pernah_paud', 10)->nullable()->after('pernah_tk');
            $table->text('aktivitas_belajar')->nullable()->after('pernah_paud');

            // Kebutuhan Khusus - detail
            $table->text('kesulitan')->nullable()->after('kebutuhan_khusus');
            $table->text('kebutuhan_alat_bantu')->nullable()->after('kesulitan');
            $table->text('kebutuhan_pendamping')->nullable()->after('kebutuhan_alat_bantu');
            $table->text('kebutuhan_penyesuaian')->nullable()->after('kebutuhan_pendamping');
            $table->text('kebutuhan_disabilitas')->nullable()->after('kebutuhan_penyesuaian');

            // Imunisasi
            $table->string('imunisasi_hepatitis_b', 20)->nullable()->after('riwayat_penyakit');
            $table->string('imunisasi_bcg', 20)->nullable()->after('imunisasi_hepatitis_b');
            $table->string('imunisasi_polio', 20)->nullable()->after('imunisasi_bcg');
            $table->string('imunisasi_dpt', 20)->nullable()->after('imunisasi_polio');
            $table->string('imunisasi_campak', 20)->nullable()->after('imunisasi_dpt');
            $table->string('imunisasi_hib', 20)->nullable()->after('imunisasi_campak');
            $table->string('imunisasi_covid19', 20)->nullable()->after('imunisasi_hib');

            // Data Ayah - tambahan
            $table->date('tanggal_lahir_ayah')->nullable()->after('tempat_lahir_ayah');
            $table->string('kewarganegaraan_ayah', 20)->nullable()->after('penghasilan_ayah');
            $table->string('status_ayah', 50)->nullable()->after('kewarganegaraan_ayah');
            $table->text('alamat_ayah')->nullable()->after('status_ayah');
            $table->string('rt_ayah', 5)->nullable()->after('alamat_ayah');
            $table->string('rw_ayah', 5)->nullable()->after('rt_ayah');
            $table->string('desa_ayah', 255)->nullable()->after('rw_ayah');
            $table->string('kecamatan_ayah', 255)->nullable()->after('desa_ayah');
            $table->string('kabupaten_ayah', 255)->nullable()->after('kecamatan_ayah');
            $table->string('provinsi_ayah', 255)->nullable()->after('kabupaten_ayah');
            $table->string('kode_pos_ayah', 10)->nullable()->after('provinsi_ayah');

            // Data Ibu - tambahan
            $table->date('tanggal_lahir_ibu')->nullable()->after('tempat_lahir_ibu');
            $table->string('kewarganegaraan_ibu', 20)->nullable()->after('penghasilan_ibu');
            $table->string('status_ibu', 50)->nullable()->after('kewarganegaraan_ibu');
            $table->text('alamat_ibu')->nullable()->after('status_ibu');
            $table->string('rt_ibu', 5)->nullable()->after('alamat_ibu');
            $table->string('rw_ibu', 5)->nullable()->after('rt_ibu');
            $table->string('desa_ibu', 255)->nullable()->after('rw_ibu');
            $table->string('kecamatan_ibu', 255)->nullable()->after('desa_ibu');
            $table->string('kabupaten_ibu', 255)->nullable()->after('kecamatan_ibu');
            $table->string('provinsi_ibu', 255)->nullable()->after('kabupaten_ibu');
            $table->string('kode_pos_ibu', 10)->nullable()->after('provinsi_ibu');

            // Data Wali - tambahan
            $table->string('kewarganegaraan_wali', 20)->nullable()->after('penghasilan_wali');
            $table->string('status_wali', 50)->nullable()->after('kewarganegaraan_wali');
            $table->date('tanggal_lahir_wali')->nullable()->after('status_wali');
            $table->string('rt_wali', 5)->nullable()->after('alamat_wali');
            $table->string('rw_wali', 5)->nullable()->after('rt_wali');
            $table->string('desa_wali', 255)->nullable()->after('rw_wali');
            $table->string('kecamatan_wali', 255)->nullable()->after('desa_wali');
            $table->string('kabupaten_wali', 255)->nullable()->after('kecamatan_wali');
            $table->string('provinsi_wali', 255)->nullable()->after('kabupaten_wali');
            $table->string('kode_pos_wali', 10)->nullable()->after('provinsi_wali');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'bulan_lahir', 'tahun_lahir',
                'pernah_tk', 'pernah_paud', 'aktivitas_belajar',
                'kesulitan', 'kebutuhan_alat_bantu', 'kebutuhan_pendamping',
                'kebutuhan_penyesuaian', 'kebutuhan_disabilitas',
                'imunisasi_hepatitis_b', 'imunisasi_bcg', 'imunisasi_polio',
                'imunisasi_dpt', 'imunisasi_campak', 'imunisasi_hib', 'imunisasi_covid19',
                'tanggal_lahir_ayah', 'kewarganegaraan_ayah', 'status_ayah',
                'alamat_ayah', 'rt_ayah', 'rw_ayah', 'desa_ayah', 'kecamatan_ayah',
                'kabupaten_ayah', 'provinsi_ayah', 'kode_pos_ayah',
                'tanggal_lahir_ibu', 'kewarganegaraan_ibu', 'status_ibu',
                'alamat_ibu', 'rt_ibu', 'rw_ibu', 'desa_ibu', 'kecamatan_ibu',
                'kabupaten_ibu', 'provinsi_ibu', 'kode_pos_ibu',
                'kewarganegaraan_wali', 'status_wali', 'tanggal_lahir_wali',
                'rt_wali', 'rw_wali', 'desa_wali', 'kecamatan_wali',
                'kabupaten_wali', 'provinsi_wali', 'kode_pos_wali',
            ]);
        });
    }
};
