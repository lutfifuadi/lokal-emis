<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\PerubahanData;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $myPendingRequests = collect();
        $mySiswaProfile = null;

        $siswa = Siswa::where('user_id', $user->id)->first();
        if (!$siswa && $user->hasRole('Orang Tua')) {
            $siswa = Siswa::first();
        }
        if ($siswa) {
            $mySiswaProfile = $siswa;
            $myPendingRequests = PerubahanData::where('siswa_id', $siswa->id)
                ->where('status', 'pending')
                ->get();
        }

        return view('siswa.dashboard', compact('myPendingRequests', 'mySiswaProfile'));
    }
}
