<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\PerubahanData;
use Illuminate\Support\Facades\Auth;

class SelfServiceApiController extends Controller
{
    private function getSiswaForUser()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        // Fallback for Orang Tua role
        if (!$siswa && $user->hasRole('Orang Tua')) {
            $siswa = Siswa::first();
        }

        return $siswa;
    }

    public function profil()
    {
        $siswa = $this->getSiswaForUser();

        if (!$siswa) {
            return response()->json(['message' => 'Profil Siswa tidak ditemukan.'], 404);
        }

        return response()->json($siswa->load(['user', 'sekolah', 'kelas']));
    }

    public function submitPerubahan(Request $request)
    {
        $siswa = $this->getSiswaForUser();

        if (!$siswa) {
            return response()->json(['message' => 'Profil Siswa tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'field' => 'required|in:nik,nama,alamat,no_hp,kontak_darurat',
            'new_value' => 'required|string',
        ]);

        // Check if there is already a pending change request for the same field
        $existing = PerubahanData::where('siswa_id', $siswa->id)
            ->where('field', $validated['field'])
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Sudah ada usulan perubahan yang berstatus pending untuk kolom ini.'
            ], 422);
        }

        $old_value = $siswa->{$validated['field']} ?? null;

        $perubahan = PerubahanData::create([
            'user_id' => Auth::id(),
            'siswa_id' => $siswa->id,
            'field' => $validated['field'],
            'old_value' => $old_value,
            'new_value' => $validated['new_value'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Usulan perubahan data berhasil diajukan.',
            'data' => $perubahan
        ], 201);
    }

    public function perubahanHistory()
    {
        $siswa = $this->getSiswaForUser();

        if (!$siswa) {
            return response()->json(['message' => 'Profil Siswa tidak ditemukan.'], 404);
        }

        $history = PerubahanData::where('siswa_id', $siswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }
}
