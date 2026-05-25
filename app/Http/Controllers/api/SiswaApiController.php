<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiswaApiController extends Controller
{
    public function index()
    {
        $query = Siswa::with(['user', 'sekolah', 'kelas']);
        $sekolahId = activeSekolahId();

        if ($sekolahId) {
            $query->where('sekolah_id', $sekolahId);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $sekolahId = activeSekolahId() ?? $request->sekolah_id;

        if (!$sekolahId) {
            return response()->json(['message' => 'sekolah_id diperlukan.'], 422);
        }

        $validated = $request->validate([
            'sekolah_id' => 'required|exists:sekolah,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:50|unique:siswas,nisn',
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'kontak_darurat' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if (activeSekolahId()) {
            $validated['sekolah_id'] = activeSekolahId();
        }

        $siswa = null;
        DB::transaction(function() use ($validated, &$siswa) {
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'username' => $validated['nisn'],
                'password' => bcrypt($validated['password'] ?? $validated['nisn']),
                'sekolah_id' => $validated['sekolah_id'],
            ]);

            $user->assignRole('Siswa');

            $siswa = Siswa::create([
                'user_id' => $user->id,
                'sekolah_id' => $validated['sekolah_id'],
                'kelas_id' => $validated['kelas_id'] ?? null,
                'nisn' => $validated['nisn'],
                'nik' => $validated['nik'] ?? null,
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'kontak_darurat' => $validated['kontak_darurat'] ?? null,
                'status' => $validated['status'],
            ]);
        });

        return response()->json($siswa->load(['user', 'sekolah', 'kelas']), 201);
    }

    public function show($id)
    {
        $sekolahId = activeSekolahId();
        $siswa = Siswa::with(['user', 'sekolah', 'kelas'])->findOrFail($id);

        if ($sekolahId && $sekolahId != $siswa->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($siswa);
    }

    public function update(Request $request, $id)
    {
        $sekolahId = activeSekolahId();
        $siswa = Siswa::findOrFail($id);

        if ($sekolahId && $sekolahId != $siswa->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:50|unique:siswas,nisn,' . $id,
            'nik' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'kontak_darurat' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
            'email' => 'required|email|max:255|unique:users,email,' . $siswa->user_id,
            'password' => 'nullable|string|min:8',
        ]);

        DB::transaction(function() use ($validated, $siswa) {
            $userData = [
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'username' => $validated['nisn'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = bcrypt($validated['password']);
            }

            $siswa->user->update($userData);

            $siswa->update([
                'kelas_id' => $validated['kelas_id'] ?? null,
                'nisn' => $validated['nisn'],
                'nik' => $validated['nik'] ?? null,
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'kontak_darurat' => $validated['kontak_darurat'] ?? null,
                'status' => $validated['status'],
            ]);
        });

        return response()->json($siswa->load(['user', 'sekolah', 'kelas']));
    }

    public function destroy($id)
    {
        $sekolahId = activeSekolahId();
        $siswa = Siswa::findOrFail($id);

        if ($sekolahId && $sekolahId != $siswa->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        User::find($siswa->user_id)->delete();

        return response()->json(['message' => 'Siswa berhasil dihapus.']);
    }
}
