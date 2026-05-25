<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Auth;

class JurusanApiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->sekolah_id) {
            $jurusans = Jurusan::where('sekolah_id', $user->sekolah_id)->get();
        } else {
            $jurusans = Jurusan::all();
        }
        return response()->json($jurusans);
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
            'kode' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
        ]);

        $jurusan = Jurusan::create($validated);
        return response()->json($jurusan, 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $jurusan = Jurusan::findOrFail($id);

        if ($user->sekolah_id && $user->sekolah_id != $jurusan->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($jurusan);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $jurusan = Jurusan::findOrFail($id);

        if ($user->sekolah_id && $user->sekolah_id != $jurusan->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'kode' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
        ]);

        $jurusan->update($validated);
        return response()->json($jurusan);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $jurusan = Jurusan::findOrFail($id);

        if ($user->sekolah_id && $user->sekolah_id != $jurusan->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $jurusan->delete();
        return response()->json(['message' => 'Jurusan berhasil dihapus.']);
    }
}
