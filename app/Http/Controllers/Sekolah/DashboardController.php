<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\PerubahanData;

class DashboardController extends Controller
{
    public function index()
    {
        $sekolahId = activeSekolahId();
        
        $stats = [
            'total_siswa' => Siswa::where('sekolah_id', $sekolahId)->count(),
            'total_guru' => Guru::where('sekolah_id', $sekolahId)->count(),
            'total_kelas' => Kelas::where('sekolah_id', $sekolahId)->count(),
            'total_sekolah' => 1,
        ];

        $pendingApprovalsCount = PerubahanData::where('status', 'pending')
            ->whereHas('siswa', fn($q) => $q->where('sekolah_id', $sekolahId))
            ->count();

        return view('sekolah.dashboard', compact('stats', 'pendingApprovalsCount'));
    }
}
