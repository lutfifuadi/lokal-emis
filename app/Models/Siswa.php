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
        'nisn', 'nik', 'nama',
        'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama',
        'alamat', 'kode_pos', 'rt', 'rw', 'desa', 'kecamatan', 'kabupaten', 'provinsi',
        'no_hp', 'no_hp_ayah', 'no_hp_ibu',
        'anak_ke', 'jml_saudara', 'status_dalam_keluarga', 'status_tempat_tinggal', 'pembiaya',
        'nama_ayah', 'nik_ayah', 'tempat_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah',
        'nama_ibu', 'nik_ibu', 'tempat_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu',
        'nama_wali', 'nik_wali', 'no_hp_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'alamat_wali',
        'kewarganegaraan', 'kebutuhan_khusus', 'no_kip', 'no_kk', 'nama_kepala_keluarga',
        'transportasi', 'jarak_tempuh',
        'asal_sekolah', 'npsn_sekolah_asal', 'jenis_sekolah_asal', 'status_sekolah_asal', 'alamat_sekolah_asal',
        'cita_cita', 'hobi', 'riwayat_penyakit',
        'prestasi', 'beasiswa',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'jarak_tempuh' => 'decimal:2',
        'anak_ke' => 'integer',
        'jml_saudara' => 'integer',
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
