<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerubahanData;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalApiController extends Controller
{
    public function antrian(Request $request)
    {
        $status = $request->input('status', 'pending');

        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return response()->json(['message' => 'Status tidak valid.'], 400);
        }

        $query = PerubahanData::with(['siswa.sekolah', 'user']);
        $sekolahId = activeSekolahId();

        if ($sekolahId) {
            $query->whereHas('siswa', function($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            });
        }

        $query->where('status', $status);

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function approve($id)
    {
        $request = PerubahanData::findOrFail($id);

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan ini sudah ditindaklanjuti.'], 400);
        }

        DB::transaction(function() use ($request) {
            $request->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $siswa = Siswa::findOrFail($request->siswa_id);
            $siswa->update([
                $request->field => $request->new_value
            ]);

            if ($request->field === 'nama') {
                $siswa->user->update([
                    'name' => $request->new_value
                ]);
            }
        });

        return response()->json([
            'message' => 'Usulan perubahan data berhasil disetujui.',
            'data' => $request->fresh(['siswa.user'])
        ]);
    }

    public function reject(Request $request, $id)
    {
        $requestData = PerubahanData::findOrFail($id);

        if ($requestData->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan ini sudah ditindaklanjuti.'], 400);
        }

        $validated = $request->validate([
            'rejected_reason' => 'required|string|max:500',
        ]);

        $requestData->update([
            'status' => 'rejected',
            'rejected_reason' => $validated['rejected_reason'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Usulan perubahan data telah ditolak.',
            'data' => $requestData
        ]);
    }
}
