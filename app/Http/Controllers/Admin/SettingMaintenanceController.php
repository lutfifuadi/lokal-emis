<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingMaintenanceController extends Controller
{
    public function index()
    {
        $maintenanceMode = setting('maintenance_mode', 'off');
        return view('admin.master.setting-maintenance', compact('maintenanceMode'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'required|in:on,off',
        ]);

        Setting::set('maintenance_mode', $request->maintenance_mode);

        $status = $request->maintenance_mode === 'on' ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.master.setting-maintenance')
            ->with('success', "Mode maintenance berhasil {$status}.");
    }
}
