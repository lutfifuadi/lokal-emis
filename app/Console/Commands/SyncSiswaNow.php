<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\GoogleSheetService;
use App\Models\GoogleSheetSetting;

class SyncSiswaNow extends Command
{
    protected $signature = 'sync:siswa-now';
    protected $description = 'Sync siswa from Google Sheet directly (sync, not queue)';

    public function handle(GoogleSheetService $service)
    {
        $setting = GoogleSheetSetting::where('entity', 'siswa')->first();
        if (!$setting) {
            $this->error('Setting tidak ditemukan');
            return 1;
        }

        $this->info('Memulai sync siswa...');
        try {
            $result = $service->syncToDatabase($setting);
            $this->info(json_encode($result, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            $this->error('ERROR: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            $this->error($e->getTraceAsString());
        }

        $this->info('Siswa: ' . \App\Models\Siswa::count());
        $this->info('Users: ' . \App\Models\User::role('Siswa')->count());
    }
}
