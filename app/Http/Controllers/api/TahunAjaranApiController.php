<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class TahunAjaranApiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->sekolah_id) {
            $tahunAjarans = TahunAjaran::where('sekolah_id', $user->sekolah_id)->get();
        } else {
            $tahunAjarans = TahunAjaran::all();
        }
        return response()->json($tahunAjarans);
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
            'tahun' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['is_active'])) {
            TahunAjaran::where('sekolah_id', $sekolah_id)->update(['is_active' => false]);
        }

        $tahunAjaran = TahunAjaran::create($validated);
        return response()->json($tahunAjaran, 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $tahunAjaran = TahunAjaran::findOrFail($id);

        if ($user->sekolah_id && $user->sekolah_id != $tahunAjaran->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($tahunAjaran);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $tahunAjaran = TahunAjaran::findOrFail($id);

        if ($user->sekolah_id && $user->sekolah_id != $tahunAjaran->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'tahun' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['is_active'])) {
            TahunAjaran::where('sekolah_id', $tahunAjaran->sekolah_id)->update(['is_active' => false]);
        }

        $tahunAjaran->update($validated);
        return response()->json($tahunAjaran);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $tahunAjaran = TahunAjaran::findOrFail($id);

        if ($user->sekolah_id && $user->sekolah_id != $tahunAjaran->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $tahunAjaran->delete();
        return response()->json(['message' => 'Tahun Ajaran berhasil dihapus.']);
    }
}
