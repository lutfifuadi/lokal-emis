<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\PerubahanData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // --- Statistik Utama (Header Cards) ---
        $stats = [
            'total_siswa'   => Siswa::count(),
            'total_guru'    => Guru::count(),
            'total_jurusan' => Jurusan::count(),
            'total_kelas'   => Kelas::count(),
            'total_sekolah' => Sekolah::count(),
            'total_users'   => User::count(),
        ];

        // --- Breakdown Siswa per Gender (untuk donut chart) ---
        $siswaGender = Siswa::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin')
            ->toArray();

        $siswaLakilaki  = $siswaGender['L'] ?? 0;
        $siswaPerempuan = $siswaGender['P'] ?? 0;

        // --- Perubahan Data 6 Bulan Terakhir (untuk bar chart) ---
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $perubahanPerBulan = PerubahanData::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // Lengkapi 6 bulan terakhir (isi 0 jika tidak ada data)
        $chartLabels = [];
        $chartData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $key   = Carbon::now()->subMonths($i)->format('Y-m');
            $label = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $chartLabels[] = $label;
            $chartData[]   = $perubahanPerBulan[$key] ?? 0;
        }

        // --- Antrian Persetujuan Terbaru (5 data) ---
        $pendingApprovalsCount = PerubahanData::where('status', 'pending')->count();
        $recentPending = PerubahanData::with(['siswa', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // --- Statistik Status Perubahan (untuk tracker card) ---
        $approvalStats = [
            'pending'  => PerubahanData::where('status', 'pending')->count(),
            'approved' => PerubahanData::where('status', 'approved')->count(),
            'rejected' => PerubahanData::where('status', 'rejected')->count(),
        ];
        $approvalTotal = array_sum($approvalStats) ?: 1;

        // --- Info Sekolah ---
        $sekolahDefault = Sekolah::first();

        return view('admin.dashboard', compact(
            'stats',
            'pendingApprovalsCount',
            'recentPending',
            'siswaLakilaki',
            'siswaPerempuan',
            'chartLabels',
            'chartData',
            'approvalStats',
            'approvalTotal',
            'sekolahDefault'
        ));
    }
}
