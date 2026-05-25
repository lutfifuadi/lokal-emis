<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sekolah;

class SekolahApiController extends Controller
{
    public function index()
    {
        $sekolahId = activeSekolahId();
        if ($sekolahId) {
            $sekolah = Sekolah::where('id', $sekolahId)->get();
        } else {
            $sekolah = Sekolah::all();
        }
        return response()->json($sekolah);
    }

    public function store(Request $request)
    {
        if (activeSekolahId()) {
            return response()->json(['message' => 'Anda tidak memiliki hak untuk menambah sekolah.'], 403);
        }

        $validated = $request->validate([
            'npsn' => 'required|string|unique:sekolah,npsn|max:50',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $sekolah = Sekolah::create($validated);
        return response()->json($sekolah, 201);
    }

    public function show($id)
    {
        $sekolahId = activeSekolahId();
        if ($sekolahId && $sekolahId != $id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $sekolah = Sekolah::findOrFail($id);
        return response()->json($sekolah);
    }

    public function update(Request $request, $id)
    {
        $sekolahId = activeSekolahId();
        if ($sekolahId && $sekolahId != $id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $sekolah = Sekolah::findOrFail($id);

        $validated = $request->validate([
            'npsn' => 'required|string|max:50|unique:sekolah,npsn,' . $id,
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $sekolah->update($validated);
        return response()->json($sekolah);
    }

    public function destroy($id)
    {
        if (activeSekolahId()) {
            return response()->json(['message' => 'Anda tidak memiliki hak untuk menghapus sekolah.'], 403);
        }

        $sekolah = Sekolah::findOrFail($id);
        $sekolah->delete();
        return response()->json(['message' => 'Sekolah berhasil dihapus.']);
    }
}
