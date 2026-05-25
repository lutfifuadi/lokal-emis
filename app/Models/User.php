<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasRoles, HasFactory, Notifiable;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'sekolah_id',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        $role = $this->roles->first()?->name;
        $avatarNum = match ($role) {
            'Super Admin' => 1,
            'Dinas' => 2,
            'Operator' => 3,
            'Kepala Sekolah' => 4,
            'Guru' => 5,
            'Siswa' => 6,
            'Orang Tua' => 7,
            default => 1,
        };
        return asset("assets/img/avatars/{$avatarNum}.png");
    }
}

