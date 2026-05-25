<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Support\Facades\Auth;

class SelfServiceProfil extends Component
{
    public $profileType; // 'siswa' or 'guru' or 'none'
    public $profileData = [];

    public function mount()
    {
        $user = Auth::user();
        
        if ($user->hasRole('Guru')) {
            $this->profileType = 'guru';
            $guru = Guru::with('sekolah')->where('user_id', $user->id)->first();
            if ($guru) {
                $this->profileData = [
                    'nama' => $guru->nama,
                    'nik' => $guru->nik ?? '-',
                    'nuptk' => $guru->nuptk ?? '-',
                    'sekolah' => $guru->sekolah->nama ?? '-',
                    'email' => $user->email,
                ];
            }
        } elseif ($user->hasAnyRole(['Siswa', 'Orang Tua'])) {
            $this->profileType = 'siswa';
            $siswa = Siswa::with(['sekolah', 'kelas', 'user'])->where('user_id', $user->id)->first();
            
            // Fallback for Orang Tua role if no direct mapping
            if (!$siswa && $user->hasRole('Orang Tua')) {
                $siswa = Siswa::with(['sekolah', 'kelas', 'user'])->first();
            }

            if ($siswa) {
                $this->profileData = [
                    'nama' => $siswa->nama,
                    'nisn' => $siswa->nisn,
                    'nik' => $siswa->nik ?? '-',
                    'sekolah' => $siswa->sekolah->nama ?? '-',
                    'kelas' => $siswa->kelas->nama ?? '-',
                    'alamat' => $siswa->alamat ?? '-',
                    'no_hp' => $siswa->no_hp ?? '-',
                    'kontak_darurat' => $siswa->kontak_darurat ?? '-',
                    'email' => $siswa->user->email ?? $user->email,
                    'status' => $siswa->status,
                ];
            }
        } else {
            $this->profileType = 'none';
        }
    }

    public function render()
    {
        return view('livewire.emis.self-service-profil');
    }
}
