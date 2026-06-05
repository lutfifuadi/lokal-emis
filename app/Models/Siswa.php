<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
        'user_id', 'sekolah_id', 'kelas_id',
        'nisn', 'nik', 'nama', 'email',
        'tempat_lahir', 'tanggal_lahir', 'bulan_lahir', 'tahun_lahir', 'jenis_kelamin', 'agama',
        'alamat', 'kode_pos', 'rt', 'rw', 'desa', 'kecamatan', 'kabupaten', 'provinsi',
        'no_hp', 'no_hp_ayah', 'no_hp_ibu',
        'anak_ke', 'jml_saudara', 'status_dalam_keluarga', 'status_tempat_tinggal', 'pembiaya',
        'nama_ayah', 'nik_ayah', 'tempat_lahir_ayah', 'tanggal_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah',
        'kewarganegaraan_ayah', 'status_ayah', 'alamat_ayah', 'rt_ayah', 'rw_ayah', 'desa_ayah', 'kecamatan_ayah', 'kabupaten_ayah', 'provinsi_ayah', 'kode_pos_ayah',
        'nama_ibu', 'nik_ibu', 'tempat_lahir_ibu', 'tanggal_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu',
        'kewarganegaraan_ibu', 'status_ibu', 'alamat_ibu', 'rt_ibu', 'rw_ibu', 'desa_ibu', 'kecamatan_ibu', 'kabupaten_ibu', 'provinsi_ibu', 'kode_pos_ibu',
        'nama_wali', 'nik_wali', 'no_hp_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali',
        'kewarganegaraan_wali', 'status_wali', 'tanggal_lahir_wali', 'alamat_wali', 'rt_wali', 'rw_wali', 'desa_wali', 'kecamatan_wali', 'kabupaten_wali', 'provinsi_wali', 'kode_pos_wali',
        'kewarganegaraan', 'kebutuhan_khusus', 'kesulitan', 'kebutuhan_alat_bantu', 'kebutuhan_pendamping', 'kebutuhan_penyesuaian', 'kebutuhan_disabilitas',
        'no_kip', 'no_kk', 'nama_kepala_keluarga',
        'transportasi', 'jarak_tempuh',
        'asal_sekolah', 'npsn_sekolah_asal', 'jenis_sekolah_asal', 'status_sekolah_asal', 'alamat_sekolah_asal',
        'cita_cita', 'hobi', 'pernah_tk', 'pernah_paud', 'aktivitas_belajar',
        'riwayat_penyakit',
        'imunisasi_hepatitis_b', 'imunisasi_bcg', 'imunisasi_polio', 'imunisasi_dpt', 'imunisasi_campak', 'imunisasi_hib', 'imunisasi_covid19',
        'prestasi', 'beasiswa',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'bulan_lahir' => 'integer',
        'tahun_lahir' => 'integer',
        'anak_ke' => 'integer',
        'jml_saudara' => 'integer',
        'tanggal_lahir_ayah' => 'date',
        'tanggal_lahir_ibu' => 'date',
        'tanggal_lahir_wali' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function perubahanData(): HasMany
    {
        return $this->hasMany(PerubahanData::class, 'siswa_id');
    }
}
