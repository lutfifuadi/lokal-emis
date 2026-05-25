<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolah';

    protected $fillable = [
        'npsn',
        'nsm',
        'nama',
        'alamat',
        'kontak',
        'email',
        'website',
        'nama_kepala',
        'nip_kepala',
        'jenis_sekolah',
        'status_sekolah',
        'jenjang',
    ];

    public function jurusans(): HasMany
    {
        return $this->hasMany(Jurusan::class, 'sekolah_id');
    }

    public function tahunAjarans(): HasMany
    {
        return $this->hasMany(TahunAjaran::class, 'sekolah_id');
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'sekolah_id');
    }

    public function gurus(): HasMany
    {
        return $this->hasMany(Guru::class, 'sekolah_id');
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'sekolah_id');
    }
}
