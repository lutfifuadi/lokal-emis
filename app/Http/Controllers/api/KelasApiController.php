<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class KelasApiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->sekolah_id) {
            $kelas = Kelas::where('sekolah_id', $user->sekolah_id)->get();
        } else {
            $kelas = Kelas::all();
        }
        return response()->json($kelas);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $sekolah_id = $user->sekolah_id ?? $request->sekolah_id;

        if (!$sekolah_id) {
            return response()->json(['message' => 'sekolah_id diperlukan.'], 422);
        }

        $request->merge(['sekolah_id' => $sekolah_id]);

        $validated = $request->validate([
            'sekolah_id' => 'required|exists:sekolah,id',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'nama' => 'required|string|max:255',
            'tingkat' => 'required|integer|min:1|max:20',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
        ]);

        $kelas = Kelas::create($validated);
        return response()->json($kelas, 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $kelas = Kelas::findOrFail($id);

        if ($user->sekolah_id && $user->sekolah_id != $kelas->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($kelas);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $kelas = Kelas::findOrFail($id);

        if ($user->sekolah_id && $user->sekolah_id != $kelas->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'nama' => 'required|string|max:255',
            'tingkat' => 'required|integer|min:1|max:20',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
        ]);

        $kelas->update($validated);
        return response()->json($kelas);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $kelas = Kelas::findOrFail($id);

        if ($user->sekolah_id && $user->sekolah_id != $kelas->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $kelas->delete();
        return response()->json(['message' => 'Kelas berhasil dihapus.']);
    }
}
