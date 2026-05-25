<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncGoogleSheetJob;
use App\Models\GoogleSheetSetting;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class GoogleSheetSettingController extends Controller
{
    protected GoogleSheetService $googleSheetService;

    public function __construct(GoogleSheetService $googleSheetService)
    {
        $this->googleSheetService = $googleSheetService;
    }

    public function index()
    {
        $settings = GoogleSheetSetting::all()->keyBy('entity');
        $entities = GoogleSheetSetting::entities();

        return view('admin.master.google-sheet-settings.index', compact('settings', 'entities'));
    }

    public function create()
    {
        $entities = GoogleSheetSetting::entities();
        $existingEntities = GoogleSheetSetting::pluck('entity')->toArray();
        $availableEntities = array_diff_key($entities, array_flip($existingEntities));
        $defaultMapping = [];

        if ($entity = request('entity')) {
            $defaultMapping = GoogleSheetSetting::defaultMapping($entity);
        }

        return view('admin.master.google-sheet-settings.form', compact('availableEntities', 'defaultMapping', 'entities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity' => 'required|string|in:' . implode(',', array_keys(GoogleSheetSetting::entities())),
            'spreadsheet_url' => 'required|url',
            'sheet_name' => 'required|string|max:255',
            'sheet_range' => 'required|string|max:50',
            'credentials_json' => 'required|json',
            'mapping_config_json' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['mapping_config'] = $validated['mapping_config_json']
            ? json_decode($validated['mapping_config_json'], true)
            : GoogleSheetSetting::defaultMapping($validated['entity']);
        unset($validated['mapping_config_json']);

        GoogleSheetSetting::create($validated);

        return redirect()->route('admin.master.google-sheet-settings.index')
            ->with('success', 'Konfigurasi Google Sheet berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $setting = GoogleSheetSetting::findOrFail($id);
        $entities = GoogleSheetSetting::entities();
        $defaultMapping = GoogleSheetSetting::defaultMapping($setting->entity);

        return view('admin.master.google-sheet-settings.form', compact('setting', 'entities', 'defaultMapping'));
    }

    public function update(Request $request, $id)
    {
        $setting = GoogleSheetSetting::findOrFail($id);

        $validated = $request->validate([
            'spreadsheet_url' => 'required|url',
            'sheet_name' => 'required|string|max:255',
            'sheet_range' => 'required|string|max:50',
            'credentials_json' => 'nullable|json',
            'mapping_config_json' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if (empty($validated['credentials_json'])) {
            unset($validated['credentials_json']);
        }

        if (isset($validated['mapping_config_json'])) {
            $validated['mapping_config'] = $validated['mapping_config_json']
                ? json_decode($validated['mapping_config_json'], true)
                : GoogleSheetSetting::defaultMapping($setting->entity);
        }
        unset($validated['mapping_config_json']);

        $setting->update($validated);

        return redirect()->route('admin.master.google-sheet-settings.index')
            ->with('success', 'Konfigurasi Google Sheet berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $setting = GoogleSheetSetting::findOrFail($id);
        $setting->delete();

        return redirect()->route('admin.master.google-sheet-settings.index')
            ->with('success', 'Konfigurasi Google Sheet berhasil dihapus.');
    }

    public function testConnection($id)
    {
        $setting = GoogleSheetSetting::findOrFail($id);

        try {
            $result = $this->googleSheetService->testConnection($setting);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menguji koneksi: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function sync(Request $request, $entity)
    {
        $setting = GoogleSheetSetting::where('entity', $entity)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi Google Sheet untuk ' . $entity . ' tidak ditemukan.',
            ], 404);
        }

        if (!$setting->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Sinkronisasi untuk ' . $entity . ' sedang nonaktif. Aktifkan terlebih dahulu.',
            ], 422);
        }

        $direction = $request->input('direction', 'import');

        try {
            SyncGoogleSheetJob::dispatch($setting, $direction);

            $actionText = $direction === 'export' ? 'Ekspor' : 'Import';

            return response()->json([
                'success'    => true,
                'background' => true,
                'message'    => $actionText . ' data ' . $entity . ' telah dijadwalkan dan sedang berjalan di background. Halaman akan diperbarui otomatis.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menjadwalkan sinkronisasi: ' . $e->getMessage(),
            ], 422);
        }
    }
}
