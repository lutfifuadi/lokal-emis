<?php

namespace App\Services;

use App\Models\GoogleSheetSetting;
use App\Models\User;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected ?Google_Client $client = null;
    protected ?Google_Service_Sheets $service = null;
    protected bool $authenticated = false;

    public function authenticate(GoogleSheetSetting $setting): bool
    {
        try {
            if (empty($setting->credentials_json)) {
                Log::error('Google Sheet credentials not found in database for entity: ' . $setting->entity);
                return false;
            }

            $credentials = json_decode($setting->credentials_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Google Sheet credentials JSON tidak valid untuk entity: ' . $setting->entity);
                return false;
            }

            $this->client = new Google_Client();
            $this->client->setApplicationName(config('google-sheet.application_name'));
            $this->client->setScopes(config('google-sheet.scopes'));
            $this->client->setAuthConfig($credentials);
            $this->client->setAccessType('offline');

            $subject = config('google-sheet.auth_config.subject');
            if ($subject) {
                $this->client->setSubject($subject);
            }

            $token = $this->client->fetchAccessTokenWithAssertion();
            if (isset($token['error'])) {
                Log::error('Google Sheet auth error: ' . ($token['error_description'] ?? $token['error']));
                return false;
            }

            $this->service = new Google_Service_Sheets($this->client);
            $this->authenticated = true;
            return true;
        } catch (\Exception $e) {
            Log::error('Google Sheet authentication failed: ' . $e->getMessage());
            return false;
        }
    }

    public function testConnection(GoogleSheetSetting $setting): array
    {
        if (!$this->authenticated && !$this->authenticate($setting)) {
            return ['success' => false, 'message' => 'Autentikasi Google gagal. Periksa credentials JSON.'];
        }

        try {
            $spreadsheetId = $this->extractSpreadsheetId($setting->spreadsheet_url) ?? $setting->spreadsheet_id;
            if (!$spreadsheetId) {
                return ['success' => false, 'message' => 'Spreadsheet ID tidak ditemukan. Periksa URL spreadsheet.'];
            }

            $range = $setting->sheet_name . '!' . $setting->sheet_range;
            $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            $setting->update([
                'last_test_at' => now(),
                'last_test_ok' => true,
                'spreadsheet_id' => $spreadsheetId,
            ]);

            return [
                'success' => true,
                'message' => 'Koneksi berhasil. ' . (count($values ?? []) - 1) . ' baris data ditemukan (tidak termasuk header).',
                'row_count' => count($values ?? []) - 1,
                'headers' => $values[0] ?? [],
                'spreadsheet_id' => $spreadsheetId,
            ];
        } catch (\Exception $e) {
            $setting->update([
                'last_test_at' => now(),
                'last_test_ok' => false,
            ]);

            return ['success' => false, 'message' => 'Gagal terhubung: ' . $e->getMessage()];
        }
    }

    public function getSheetData(GoogleSheetSetting $setting): array
    {
        if (!$this->authenticated && !$this->authenticate($setting)) {
            throw new \Exception('Autentikasi Google gagal.');
        }

        $spreadsheetId = $setting->spreadsheet_id ?? $this->extractSpreadsheetId($setting->spreadsheet_url);
        if (!$spreadsheetId) {
            throw new \Exception('Spreadsheet ID tidak valid.');
        }

        $range = $setting->sheet_name . '!' . $setting->sheet_range;
        $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            return [];
        }

        $headers = $values[0];
        $rows = [];

        for ($i = 1; $i < count($values); $i++) {
            $row = [];
            foreach ($headers as $index => $header) {
                $row[$header] = $values[$i][$index] ?? '';
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public function appendSheetData(GoogleSheetSetting $setting, array $headers, array $rows): bool
    {
        if (!$this->authenticated && !$this->authenticate($setting)) {
            throw new \Exception('Autentikasi Google gagal.');
        }

        $spreadsheetId = $setting->spreadsheet_id ?? $this->extractSpreadsheetId($setting->spreadsheet_url);
        if (!$spreadsheetId) {
            throw new \Exception('Spreadsheet ID tidak valid.');
        }

        $range = $setting->sheet_name . '!' . $setting->sheet_range;

        $values = [$headers];
        foreach ($rows as $row) {
            $mapped = [];
            foreach ($headers as $header) {
                $mapped[] = $row[$header] ?? '';
            }
            $values[] = $mapped;
        }

        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values,
        ]);

        $this->service->spreadsheets_values->update($spreadsheetId, $range, $body, ['valueInputOption' => 'RAW']);

        return true;
    }

    public function syncToDatabase(GoogleSheetSetting $setting): array
    {
        // Nonaktifkan batasan waktu eksekusi agar sinkronisasi tidak mengalami timeout
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        if (!$this->authenticated && !$this->authenticate($setting)) {
            throw new \Exception('Autentikasi Google gagal.');
        }

        $entity = $setting->entity;
        $modelClass = $this->getModelClass($entity);

        if (!$modelClass) {
            throw new \Exception('Entity ' . $entity . ' tidak dikenal.');
        }

        $data = $this->getSheetData($setting);
        if (empty($data)) {
            return ['imported' => 0, 'updated' => 0, 'message' => 'Sheet kosong atau tidak ada data.'];
        }

        $mapping = $setting->mapping_config ?? [];
        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                $dbData = $this->mapSheetRowToDatabase($row, $mapping, $entity);

                if (empty($dbData)) {
                    continue;
                }

                if ($entity === 'siswa') {
                    $sekolahId = !empty($dbData['sekolah_id']) ? $dbData['sekolah_id'] : null;
                    $nisn = $dbData['nisn'] ?? null;

                    if (empty($dbData['email']) && $nisn) {
                        $dbData['email'] = $nisn . '@siswa.emis.local';
                    }

                    $user = null;
                    if (!empty($dbData['email'])) {
                        $user = User::where('email', $dbData['email'])->first();
                    }
                    if (!$user && $nisn) {
                        $user = User::where('username', $nisn)->first();
                    }

                    if ($user) {
                        $user->update([
                            'name' => $dbData['nama'] ?? $user->name,
                            'username' => $nisn ?? $user->username,
                            'email' => $dbData['email'] ?? $user->email,
                            'sekolah_id' => $sekolahId ?? $user->sekolah_id,
                        ]);
                    } else {
                        $user = User::create([
                            'name' => $dbData['nama'] ?? '',
                            'email' => $dbData['email'],
                            'username' => $nisn,
                            'password' => Hash::make($dbData['password'] ?? $nisn ?? 'password123'),
                            'sekolah_id' => $sekolahId,
                        ]);
                    }
                    $user->syncRoles(['Siswa']);
                    $dbData['user_id'] = $user->id;
                    $dbData['sekolah_id'] = $sekolahId;

                    unset($dbData['email'], $dbData['password']);
                }

                $uniqueKey = $this->getUniqueKey($entity, $dbData);
                if ($uniqueKey) {
                    $existing = $modelClass::where($uniqueKey['field'], $uniqueKey['value'])->first();
                    if ($existing) {
                        $existing->update($dbData);
                        $updated++;
                    } else {
                        $modelClass::create($dbData);
                        $imported++;
                    }
                } else {
                    $modelClass::create($dbData);
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = 'Baris ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }

        $setting->update(['last_sync_at' => now()]);

        return [
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors,
            'message' => 'Sinkronisasi selesai. ' . $imported . ' baru, ' . $updated . ' diperbarui.',
        ];
    }

    public function syncToSheet(GoogleSheetSetting $setting): array
    {
        // Nonaktifkan batasan waktu eksekusi agar ekspor tidak mengalami timeout
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        if (!$this->authenticated && !$this->authenticate($setting)) {
            throw new \Exception('Autentikasi Google gagal.');
        }

        $entity = $setting->entity;
        $modelClass = $this->getModelClass($entity);

        if (!$modelClass) {
            throw new \Exception('Entity ' . $entity . ' tidak dikenal.');
        }

        $mapping = $setting->mapping_config ?? [];
        $sheetHeaders = array_values($mapping);
        $dbFields = array_keys($mapping);

        $eagerLoads = match ($entity) {
            'jurusan', 'tahun-ajaran', 'siswa' => ['sekolah'],
            'kelas' => ['sekolah', 'jurusan', 'tahunAjaran'],
            default => [],
        };
        $records = $modelClass::with($eagerLoads)->get();
        $rows = [];

        foreach ($records as $record) {
            $exportData = $this->resolveExportForeignKeys($record, $entity, $mapping);
            $row = [];
            foreach ($sheetHeaders as $index => $header) {
                $dbField = $dbFields[$index] ?? null;
                $row[$header] = $dbField ? ($exportData[$dbField] ?? $record->{$dbField} ?? '') : '';
            }
            $rows[] = $row;
        }

        $this->clearSheet($setting);
        $this->appendSheetData($setting, $sheetHeaders, $rows);

        $setting->update(['last_sync_at' => now()]);

        return [
            'exported' => count($rows),
            'message' => 'Ekspor selesai. ' . count($rows) . ' baris data dikirim ke Google Sheet.',
        ];
    }

    public function clearSheetRange(GoogleSheetSetting $setting): void
    {
        $this->clearSheet($setting);
    }

    protected function clearSheet(GoogleSheetSetting $setting): void
    {
        if (!$this->authenticated && !$this->authenticate($setting)) {
            throw new \Exception('Autentikasi Google gagal.');
        }

        $spreadsheetId = $setting->spreadsheet_id ?? $this->extractSpreadsheetId($setting->spreadsheet_url);
        $range = $setting->sheet_name . '!' . $setting->sheet_range;

        $this->service->spreadsheets_values->clear($spreadsheetId, $range, new \Google_Service_Sheets_ClearValuesRequest());
    }

    protected function mapSheetRowToDatabase(array $row, array $mapping, string $entity): array
    {
        $data = [];

        if (!empty($mapping)) {
            foreach ($mapping as $dbField => $sheetColumn) {
                $data[$dbField] = $row[$sheetColumn] ?? '';
            }
        } else {
            $data = $row;
        }

        $data = $this->resolveForeignKeys($data, $entity);
        $data = $this->normalizeEmptyValues($data, $entity);

        return $data;
    }

    protected function normalizeEmptyValues(array $data, string $entity): array
    {
        $numericFields = match ($entity) {
            'siswa' => ['anak_ke', 'jml_saudara', 'sekolah_id', 'kelas_id'],
            default => [],
        };

        foreach ($numericFields as $field) {
            if (isset($data[$field]) && (!is_numeric($data[$field]) || $data[$field] === '')) {
                $data[$field] = null;
            }
        }

        if ($entity === 'siswa') {
            if (isset($data['tanggal_lahir']) && $data['tanggal_lahir'] === '') {
                $data['tanggal_lahir'] = null;
            }
            if (isset($data['jarak_tempuh']) && (!is_numeric($data['jarak_tempuh']) || $data['jarak_tempuh'] === '')) {
                $data['jarak_tempuh'] = null;
            }
        }

        return $data;
    }

    protected function resolveForeignKeys(array $data, string $entity): array
    {
        switch ($entity) {
            case 'jurusan':
            case 'tahun-ajaran':
            case 'kelas':
            case 'siswa':
                if (!empty($data['npsn_sekolah'])) {
                    $sekolah = \App\Models\Sekolah::where('npsn', $data['npsn_sekolah'])->first();
                    $data['sekolah_id'] = $sekolah?->id;
                }
                if (!empty($data['npsn_sekolah_asal'])) {
                    $sekolah = \App\Models\Sekolah::where('npsn', $data['npsn_sekolah_asal'])->first();
                    $data['sekolah_id'] = $sekolah?->id;
                }
                unset($data['npsn_sekolah'], $data['npsn_sekolah_asal']);
                break;
        }

        switch ($entity) {
            case 'kelas':
                if (!empty($data['kode_jurusan'])) {
                    $jurusan = \App\Models\Jurusan::where('kode', $data['kode_jurusan'])
                        ->when($data['sekolah_id'] ?? null, fn($q, $id) => $q->where('sekolah_id', $id))
                        ->first();
                    $data['jurusan_id'] = $jurusan?->id;
                }
                unset($data['kode_jurusan']);

                if (!empty($data['tahun']) && !empty($data['semester'])) {
                    $ta = \App\Models\TahunAjaran::where('tahun', $data['tahun'])
                        ->where('semester', $data['semester'])
                        ->when($data['sekolah_id'] ?? null, fn($q, $id) => $q->where('sekolah_id', $id))
                        ->first();
                    $data['tahun_ajaran_id'] = $ta?->id;
                }
                unset($data['tahun'], $data['semester']);
                break;
        }

        return $data;
    }

    protected function resolveExportForeignKeys($record, string $entity, array $mapping): array
    {
        $data = [];
        $dbFields = array_keys($mapping);

        foreach ($dbFields as $field) {
            $data[$field] = $record->{$field} ?? '';
        }

        switch ($entity) {
            case 'jurusan':
            case 'tahun-ajaran':
            case 'kelas':
            case 'siswa':
                if (in_array('npsn_sekolah', $dbFields) && $record->sekolah) {
                    $data['npsn_sekolah'] = $record->sekolah->npsn;
                }
                break;
        }

        switch ($entity) {
            case 'kelas':
                if (in_array('kode_jurusan', $dbFields) && $record->jurusan) {
                    $data['kode_jurusan'] = $record->jurusan->kode;
                }
                if (in_array('tahun', $dbFields) && $record->tahunAjaran) {
                    $data['tahun'] = $record->tahunAjaran->tahun;
                }
                if (in_array('semester', $dbFields) && $record->tahunAjaran) {
                    $data['semester'] = $record->tahunAjaran->semester;
                }
                break;
        }

        return $data;
    }

    protected function getUniqueKey(string $entity, array $data): ?array
    {
        return match ($entity) {
            'sekolah' => !empty($data['npsn']) ? ['field' => 'npsn', 'value' => $data['npsn']] : null,
            'jurusan' => (!empty($data['kode']) && !empty($data['sekolah_id']))
                ? ['field' => 'kode', 'value' => $data['kode']] : null,
            'users' => !empty($data['email']) ? ['field' => 'email', 'value' => $data['email']] : null,
            'siswa' => !empty($data['nisn']) ? ['field' => 'nisn', 'value' => $data['nisn']] : null,
            default => null,
        };
    }

    protected function extractSpreadsheetId(string $url): ?string
    {
        preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9_-]+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    protected function getModelClass(string $entity): ?string
    {
        return match ($entity) {
            'sekolah' => \App\Models\Sekolah::class,
            'jurusan' => \App\Models\Jurusan::class,
            'tahun-ajaran' => \App\Models\TahunAjaran::class,
            'kelas' => \App\Models\Kelas::class,
            'users' => \App\Models\User::class,
            'siswa' => \App\Models\Siswa::class,
            default => null,
        };
    }
}
