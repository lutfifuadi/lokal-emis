<?php

namespace App\Jobs;

use App\Models\GoogleSheetSetting;
use App\Services\GoogleSheetService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncGoogleSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public GoogleSheetSetting $setting;
    public string $direction;

    /**
     * Create a new job instance.
     */
    public function __construct(GoogleSheetSetting $setting, string $direction = 'import')
    {
        $this->setting = $setting;
        $this->direction = $direction;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleSheetService $googleSheetService): void
    {
        Log::info("Memulai SyncGoogleSheetJob untuk entity: {$this->setting->entity}, arah: {$this->direction}");

        try {
            $result = $this->direction === 'export'
                ? $googleSheetService->syncToSheet($this->setting)
                : $googleSheetService->syncToDatabase($this->setting);

            Log::info("SyncGoogleSheetJob selesai dengan sukses untuk entity: {$this->setting->entity}. Hasil: " . json_encode($result));
        } catch (\Exception $e) {
            Log::error("SyncGoogleSheetJob gagal untuk entity: {$this->setting->entity} dengan error: " . $e->getMessage());
            throw $e;
        }
    }
}
