<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'sekolah_id',
        'tahun',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'tahun_ajaran_id');
    }
}
