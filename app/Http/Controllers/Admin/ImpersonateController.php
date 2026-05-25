<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Mulai sesi impersonation — login sebagai user lain.
     * Dipanggil via route POST /admin/impersonate/{id}
     */
    public function start(Request $request, $id)
    {
        // Tidak bisa impersonate diri sendiri
        if (Auth::id() == $id) {
            return back()->with('error', 'Anda tidak dapat login sebagai diri sendiri.');
        }

        $targetUser = User::findOrFail($id);
        $targetRole = $targetUser->roles->first()?->name;

        // Hanya boleh impersonate role yang lebih rendah (bukan admin level)
        $protectedRoles = ['Super Admin', 'Dinas', 'Operator'];
        if (in_array($targetRole, $protectedRoles)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk login sebagai pengguna dengan role tersebut.');
        }

        // Simpan ID admin asli ke session
        session(['impersonated_by' => Auth::id()]);

        // Login sebagai user target
        Auth::loginUsingId($id);

        // Redirect ke dashboard (akan diarahkan sesuai role oleh route '/')
        return redirect('/');
    }

    /**
     * Hentikan sesi impersonation dan kembalikan login ke admin asal.
     * Dipanggil via route POST /admin/impersonate/stop
     */
    public function stop(Request $request)
    {
        $originalId = session('impersonated_by');

        if (!$originalId) {
            // Tidak ada sesi impersonation aktif, redirect ke home
            return redirect('/');
        }

        // Hapus session impersonation
        session()->forget('impersonated_by');

        // Login kembali sebagai admin asal
        Auth::loginUsingId($originalId);

        session()->flash('message', 'Sesi impersonation telah diakhiri. Anda kembali ke akun admin.');

        return redirect()->route('admin.master.users');
    }
}
