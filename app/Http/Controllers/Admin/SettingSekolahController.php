<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingSekolahController extends Controller
{
    public function index()
    {
        $appMode = setting('app_mode');
        $defaultSekolahId = setting('default_sekolah_id');
        $sekolahs = Sekolah::all();

        return view('admin.master.setting-sekolah', compact('appMode', 'defaultSekolahId', 'sekolahs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_mode' => 'required|in:single,multi',
            'default_sekolah_id' => 'required_if:app_mode,single|nullable|exists:sekolah,id',
        ]);

        Setting::set('app_mode', $request->app_mode);
        Setting::set('default_sekolah_id', $request->app_mode === 'single' ? $request->default_sekolah_id : null);

        return redirect()->route('admin.master.setting-sekolah')
            ->with('success', 'Mode sekolah berhasil diperbarui.');
    }
}
