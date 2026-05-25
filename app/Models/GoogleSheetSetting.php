<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleSheetSetting extends Model
{
    protected $table = 'google_sheet_settings';

    protected $fillable = [
        'entity',
        'spreadsheet_url',
        'sheet_name',
        'sheet_range',
        'spreadsheet_id',
        'credentials_json',
        'mapping_config',
        'is_active',
        'last_sync_at',
        'last_test_at',
        'last_test_ok',
    ];

    protected $casts = [
        'mapping_config' => 'array',
        'credentials_json' => 'encrypted',
        'is_active' => 'boolean',
        'last_test_ok' => 'boolean',
        'last_sync_at' => 'datetime',
        'last_test_at' => 'datetime',
    ];

    public static function entities(): array
    {
        return [
            'sekolah' => 'Sekolah',
            'jurusan' => 'Jurusan',
            'tahun-ajaran' => 'Tahun Ajaran',
            'kelas' => 'Kelas',
            'users' => 'Pengguna',
            'siswa' => 'Siswa',
        ];
    }

    public static function defaultMapping(string $entity): array
    {
        return match ($entity) {
            'sekolah' => [
                'npsn' => 'NPSN',
                'nsm' => 'NSM',
                'nama' => 'Nama Sekolah',
                'alamat' => 'Alamat',
                'kontak' => 'Kontak',
                'email' => 'Email',
                'website' => 'Website',
                'nama_kepala' => 'Nama Kepala Sekolah',
                'nip_kepala' => 'NIP Kepala Sekolah',
                'jenis_sekolah' => 'Jenis Sekolah',
                'status_sekolah' => 'Status Sekolah',
                'jenjang' => 'Jenjang',
            ],
            'tahun-ajaran' => [
                'npsn_sekolah' => 'NPSN Sekolah',
                'tahun' => 'Tahun Ajaran',
                'semester' => 'Semester',
            ],
            'kelas' => [
                'npsn_sekolah' => 'NPSN Sekolah',
                'kode_jurusan' => 'Kode Jurusan',
                'nama' => 'Nama Kelas',
                'tingkat' => 'Tingkat',
                'tahun' => 'Tahun Ajaran',
                'semester' => 'Semester',
            ],
            'jurusan' => [
                'npsn_sekolah' => 'NPSN Sekolah',
                'kode' => 'Kode Jurusan',
                'nama' => 'Nama Jurusan',
            ],
            'siswa' => [
                'nisn' => 'NISN',
                'nik' => 'NIK',
                'nama' => 'Nama Lengkap',
                'tempat_lahir' => 'Tempat Lahir',
                'tanggal_lahir' => 'Tanggal Lahir',
                'jenis_kelamin' => 'Jenis Kelamin',
                'agama' => 'Agama',
                'kewarganegaraan' => 'Kewarganegaraan',
                'kebutuhan_khusus' => 'Kebutuhan Khusus',
                'alamat' => 'Alamat',
                'kode_pos' => 'Kode Pos',
                'rt' => 'RT',
                'rw' => 'RW',
                'desa' => 'Desa/Kelurahan',
                'kecamatan' => 'Kecamatan',
                'kabupaten' => 'Kabupaten/Kota',
                'provinsi' => 'Provinsi',
                'no_hp' => 'No. HP',
                'no_hp_ayah' => 'No. HP Ayah',
                'no_hp_ibu' => 'No. HP Ibu',
                'transportasi' => 'Transportasi',
                'jarak_tempuh' => 'Jarak Tempuh (km)',
                'anak_ke' => 'Anak Ke-',
                'jml_saudara' => 'Jumlah Saudara',
                'status_dalam_keluarga' => 'Status Dalam Keluarga',
                'status_tempat_tinggal' => 'Status Tempat Tinggal',
                'pembiaya' => 'Pembiaya',
                'nama_ayah' => 'Nama Ayah',
                'nik_ayah' => 'NIK Ayah',
                'tempat_lahir_ayah' => 'Tempat Lahir Ayah',
                'pendidikan_ayah' => 'Pendidikan Ayah',
                'pekerjaan_ayah' => 'Pekerjaan Ayah',
                'penghasilan_ayah' => 'Penghasilan Ayah',
                'nama_ibu' => 'Nama Ibu',
                'nik_ibu' => 'NIK Ibu',
                'tempat_lahir_ibu' => 'Tempat Lahir Ibu',
                'pendidikan_ibu' => 'Pendidikan Ibu',
                'pekerjaan_ibu' => 'Pekerjaan Ibu',
                'penghasilan_ibu' => 'Penghasilan Ibu',
                'nama_wali' => 'Nama Wali',
                'nik_wali' => 'NIK Wali',
                'no_hp_wali' => 'No. HP Wali',
                'pendidikan_wali' => 'Pendidikan Wali',
                'pekerjaan_wali' => 'Pekerjaan Wali',
                'penghasilan_wali' => 'Penghasilan Wali',
                'alamat_wali' => 'Alamat Wali',
                'nama_kepala_keluarga' => 'Nama Kepala Keluarga',
                'no_kk' => 'No. KK',
                'no_kip' => 'No. KIP',
                'asal_sekolah' => 'Asal Sekolah',
                'npsn_sekolah_asal' => 'NPSN Sekolah Asal',
                'jenis_sekolah_asal' => 'Jenis Sekolah Asal',
                'status_sekolah_asal' => 'Status Sekolah Asal',
                'alamat_sekolah_asal' => 'Alamat Sekolah Asal',
                'cita_cita' => 'Cita-cita',
                'hobi' => 'Hobi',
                'riwayat_penyakit' => 'Riwayat Penyakit',
                'prestasi' => 'Prestasi',
                'beasiswa' => 'Beasiswa',
                'status' => 'Status',
                'kelas_id' => 'ID Kelas',
                'sekolah_id' => 'ID Sekolah',
            ],
            default => [],
        };
    }
}
