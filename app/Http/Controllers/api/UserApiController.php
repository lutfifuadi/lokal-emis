<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guru;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserApiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = User::with(['roles', 'sekolah']);

        if ($user->sekolah_id) {
            $query->where('sekolah_id', $user->sekolah_id);
        }

        $query->whereHas('roles', function($q) {
            $q->whereIn('name', ['Super Admin', 'Dinas', 'Operator', 'Kepala Sekolah', 'Guru', 'Siswa', 'Orang Tua']);
        });

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $sekolah_id = $currentUser->sekolah_id ?? $request->sekolah_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:Super Admin,Dinas,Operator,Kepala Sekolah,Guru,Siswa,Orang Tua',
            'sekolah_id' => 'required_if:role,Operator,Kepala Sekolah,Guru,Siswa,Orang Tua|nullable|exists:sekolah,id',
            'nik' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
        ]);

        // Override school ID for operators
        if ($currentUser->sekolah_id && in_array($validated['role'], ['Operator', 'Kepala Sekolah', 'Guru', 'Siswa', 'Orang Tua'])) {
            $validated['sekolah_id'] = $currentUser->sekolah_id;
        }

        $user = null;
        DB::transaction(function() use ($validated, &$user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'sekolah_id' => in_array($validated['role'], ['Dinas', 'Super Admin']) ? null : $validated['sekolah_id'],
            ]);

            $user->assignRole($validated['role']);

            if ($validated['role'] === 'Guru') {
                Guru::create([
                    'user_id' => $user->id,
                    'sekolah_id' => $validated['sekolah_id'],
                    'nik' => $validated['nik'] ?? null,
                    'nuptk' => $validated['nuptk'] ?? null,
                    'nama' => $user->name,
                ]);
            }
        });

        return response()->json($user->load(['roles', 'sekolah']), 201);
    }

    public function show($id)
    {
        $currentUser = Auth::user();
        $user = User::with(['roles', 'sekolah'])->findOrFail($id);

        if ($currentUser->sekolah_id && $currentUser->sekolah_id != $user->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($user->hasRole('Guru')) {
            $user->guru = Guru::where('user_id', $user->id)->first();
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        if ($currentUser->sekolah_id && $currentUser->sekolah_id != $user->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:Super Admin,Dinas,Operator,Kepala Sekolah,Guru,Siswa,Orang Tua',
            'sekolah_id' => 'required_if:role,Operator,Kepala Sekolah,Guru,Siswa,Orang Tua|nullable|exists:sekolah,id',
            'nik' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
        ]);

        if ($currentUser->sekolah_id && in_array($validated['role'], ['Operator', 'Kepala Sekolah', 'Guru', 'Siswa', 'Orang Tua'])) {
            $validated['sekolah_id'] = $currentUser->sekolah_id;
        }

        DB::transaction(function() use ($validated, $user) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'sekolah_id' => in_array($validated['role'], ['Dinas', 'Super Admin']) ? null : $validated['sekolah_id'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = bcrypt($validated['password']);
            }

            $user->update($userData);
            $user->syncRoles([$validated['role']]);

            if ($validated['role'] === 'Guru') {
                Guru::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'sekolah_id' => $validated['sekolah_id'],
                        'nik' => $validated['nik'] ?? null,
                        'nuptk' => $validated['nuptk'] ?? null,
                        'nama' => $user->name,
                    ]
                );
            } else {
                Guru::where('user_id', $user->id)->delete();
            }
        });

        return response()->json($user->load(['roles', 'sekolah']));
    }

    public function destroy($id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        if ($currentUser->sekolah_id && $currentUser->sekolah_id != $user->sekolah_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($currentUser->id == $id) {
            return response()->json(['message' => 'Anda tidak dapat menghapus akun sendiri.'], 400);
        }

        $user->delete();
        return response()->json(['message' => 'Pengguna berhasil dihapus.']);
    }
}
