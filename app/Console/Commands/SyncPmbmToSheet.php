<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetService;
use App\Models\GoogleSheetSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncPmbmToSheet extends Command
{
    protected $signature = 'pmbm:sync-to-sheet
        {--limit= : Batas jumlah data yang diambil dari PMBM}
        {--offset= : Offset data PMBM}
        {--status=lulus : Filter status pendaftar}
        {--daftar-ulang=1 : Filter daftar_ulang_selesai (0/1)}';

    protected $description = 'Sync data pendaftar dari database PMBM ke Google Sheet Siswa';

    protected GoogleSheetService $googleSheetService;

    public function __construct(GoogleSheetService $googleSheetService)
    {
        parent::__construct();
        $this->googleSheetService = $googleSheetService;
    }

    public function handle(): int
    {
        $setting = GoogleSheetSetting::where('entity', 'siswa')->first();

        if (!$setting) {
            $this->error('Konfigurasi Google Sheet untuk entity siswa tidak ditemukan.');
            return 1;
        }

        if (!$setting->is_active) {
            $this->error('Sinkronisasi untuk siswa sedang nonaktif.');
            return 1;
        }

        $mapping = $setting->mapping_config ?? [];
        if (empty($mapping)) {
            $this->error('Mapping config untuk siswa kosong.');
            return 1;
        }

        $sheetHeaders = array_values($mapping);

        $this->info('Mengambil data dari database PMBM...');
        $pmbmData = $this->fetchPmbmData();
        $this->info('Ditemukan ' . count($pmbmData) . ' data pendaftar.');

        if (empty($pmbmData)) {
            $this->warn('Tidak ada data dari PMBM.');
            return 0;
        }

        $rows = [];
        $bar = $this->output->createProgressBar(count($pmbmData));
        $bar->start();

        foreach ($pmbmData as $p) {
            $row = $this->mapPmbmToSheetRow($p, $sheetHeaders);
            $rows[] = $row;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->writeToSheet($setting, $sheetHeaders, $rows);

        $this->info('Selesai! ' . count($rows) . ' data berhasil disinkronkan ke Google Sheet.');

        return 0;
    }

    protected function fetchPmbmData(): array
    {
        $query = DB::connection('pmbm_mysql')
            ->table('pendaftar')
            ->select([
                'nisn',
                'nik',
                'name',
                'tempat_lahir',
                'tanggal_lahir',
                'bulan_lahir',
                'tahun_lahir',
                'jenis_kelamin',
                'kewarganegaraan',
                'kebutuhan_khusus',
                'alamat_lengkap',
                'kode_pos',
                'rt',
                'rw',
                'desa',
                'kecamatan',
                'kabupaten',
                'provinsi',
                'phone_wa',
                'phone_ayah',
                'phone_ibu',
                'transportasi',
                'jarak_rumah',
                'anak_ke',
                'jumlah_saudara',
                'status_dalam_keluarga',
                'status_tempat_tinggal',
                'pembiaya',
                'pembiaya_lainnya',
                'nama_ayah',
                'nik_ayah',
                'tempat_lahir_ayah',
                'pendidikan_ayah',
                'pekerjaan_ayah',
                'penghasilan_ayah',
                'nama_ibu',
                'nik_ibu',
                'tempat_lahir_ibu',
                'pendidikan_ibu',
                'pekerjaan_ibu',
                'penghasilan_ibu',
                'nama_wali',
                'nik_wali',
                'phone_wali',
                'pendidikan_wali',
                'pekerjaan_wali',
                'penghasilan_wali',
                'alamat_wali',
                'nomor_kk',
                'nomor_kip',
                'asal_sekolah',
                'nama_sekolah',
                'npsn',
                'jenis_sekolah',
                'status_sekolah',
                'alamat_sekolah',
                'cita_cita',
                'hobi',
                'riwayat_penyakit',
                'prestasi_nama',
                'prestasi_jenis',
                'prestasi_tahun',
                'prestasi_tingkat',
                'prestasi_penyelenggara',
                'prestasi_bidang',
                'prestasi_peringkat',
                'beasiswa_nama',
                'beasiswa_jenis',
                'beasiswa_nomor',
                'beasiswa_tahun',
                'beasiswa_nominal',
            ])
            ->orderBy('id');

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        if ($offset = $this->option('offset')) {
            $query->offset((int) $offset);
        }

        $statuses = explode(',', $this->option('status'));
        $query->whereIn('status', $statuses);
        $query->where('daftar_ulang_selesai', (int) $this->option('daftar-ulang'));

        return $query->get()->map(fn($item) => (array) $item)->toArray();
    }

    protected function mapPmbmToSheetRow(array $p, array $sheetHeaders): array
    {
        $tanggalLahir = $this->buildDate(
            $p['tahun_lahir'] ?? null,
            $p['bulan_lahir'] ?? null,
            $p['tanggal_lahir'] ?? null
        );

        $prestasi = $this->buildJsonField([
            'nama' => $p['prestasi_nama'] ?? '',
            'jenis' => $p['prestasi_jenis'] ?? '',
            'tahun' => $p['prestasi_tahun'] ?? '',
            'tingkat' => $p['prestasi_tingkat'] ?? '',
            'penyelenggara' => $p['prestasi_penyelenggara'] ?? '',
            'bidang' => $p['prestasi_bidang'] ?? '',
            'peringkat' => $p['prestasi_peringkat'] ?? '',
        ]);

        $beasiswa = $this->buildJsonField([
            'nama' => $p['beasiswa_nama'] ?? '',
            'jenis' => $p['beasiswa_jenis'] ?? '',
            'nomor' => $p['beasiswa_nomor'] ?? '',
            'tahun' => $p['beasiswa_tahun'] ?? '',
            'nominal' => $p['beasiswa_nominal'] ?? '',
        ]);

        $asalSekolah = !empty($p['nama_sekolah']) ? $p['nama_sekolah'] : ($p['asal_sekolah'] ?? '');

        $pembiaya = $p['pembiaya'] ?? '';
        if (empty($pembiaya) && !empty($p['pembiaya_lainnya'])) {
            $pembiaya = $p['pembiaya_lainnya'];
        }

        $mapped = [
            'NISN' => $p['nisn'] ?? '',
            'NIK' => $p['nik'] ?? '',
            'Nama Lengkap' => $p['name'] ?? '',
            'Tempat Lahir' => $p['tempat_lahir'] ?? '',
            'Tanggal Lahir' => $tanggalLahir,
            'Jenis Kelamin' => $p['jenis_kelamin'] ?? '',
            'Agama' => 'ISLAM',
            'Kewarganegaraan' => $p['kewarganegaraan'] ?? '',
            'Kebutuhan Khusus' => $p['kebutuhan_khusus'] ?? '',
            'Alamat' => $p['alamat_lengkap'] ?? '',
            'Kode Pos' => $p['kode_pos'] ?? '',
            'RT' => (string) ($p['rt'] ?? ''),
            'RW' => (string) ($p['rw'] ?? ''),
            'Desa/Kelurahan' => $p['desa'] ?? '',
            'Kecamatan' => $p['kecamatan'] ?? '',
            'Kabupaten/Kota' => $p['kabupaten'] ?? '',
            'Provinsi' => $p['provinsi'] ?? '',
            'No. HP' => $p['phone_wa'] ?? '',
            'No. HP Ayah' => $p['phone_ayah'] ?? '',
            'No. HP Ibu' => $p['phone_ibu'] ?? '',
            'Transportasi' => $p['transportasi'] ?? '',
            'Jarak Tempuh (km)' => (string) ($p['jarak_rumah'] ?? ''),
            'Anak Ke-' => (string) ($p['anak_ke'] ?? ''),
            'Jumlah Saudara' => (string) ($p['jumlah_saudara'] ?? ''),
            'Status Dalam Keluarga' => $p['status_dalam_keluarga'] ?? '',
            'Status Tempat Tinggal' => $p['status_tempat_tinggal'] ?? '',
            'Pembiaya' => $pembiaya,
            'Nama Ayah' => $p['nama_ayah'] ?? '',
            'NIK Ayah' => $p['nik_ayah'] ?? '',
            'Tempat Lahir Ayah' => $p['tempat_lahir_ayah'] ?? '',
            'Pendidikan Ayah' => $p['pendidikan_ayah'] ?? '',
            'Pekerjaan Ayah' => $p['pekerjaan_ayah'] ?? '',
            'Penghasilan Ayah' => $p['penghasilan_ayah'] ?? '',
            'Nama Ibu' => $p['nama_ibu'] ?? '',
            'NIK Ibu' => $p['nik_ibu'] ?? '',
            'Tempat Lahir Ibu' => $p['tempat_lahir_ibu'] ?? '',
            'Pendidikan Ibu' => $p['pendidikan_ibu'] ?? '',
            'Pekerjaan Ibu' => $p['pekerjaan_ibu'] ?? '',
            'Penghasilan Ibu' => $p['penghasilan_ibu'] ?? '',
            'Nama Wali' => $p['nama_wali'] ?? '',
            'NIK Wali' => $p['nik_wali'] ?? '',
            'No. HP Wali' => $p['phone_wali'] ?? '',
            'Pendidikan Wali' => $p['pendidikan_wali'] ?? '',
            'Pekerjaan Wali' => $p['pekerjaan_wali'] ?? '',
            'Penghasilan Wali' => $p['penghasilan_wali'] ?? '',
            'Alamat Wali' => $p['alamat_wali'] ?? '',
            'Nama Kepala Keluarga' => '',
            'No. KK' => $p['nomor_kk'] ?? '',
            'No. KIP' => $p['nomor_kip'] ?? '',
            'Asal Sekolah' => $asalSekolah,
            'NPSN Sekolah Asal' => (string) ($p['npsn'] ?? ''),
            'Jenis Sekolah Asal' => $p['jenis_sekolah'] ?? '',
            'Status Sekolah Asal' => $p['status_sekolah'] ?? '',
            'Alamat Sekolah Asal' => $p['alamat_sekolah'] ?? '',
            'Cita-cita' => $p['cita_cita'] ?? '',
            'Hobi' => $p['hobi'] ?? '',
            'Riwayat Penyakit' => $p['riwayat_penyakit'] ?? '',
            'Prestasi' => $prestasi,
            'Beasiswa' => $beasiswa,
            'Status' => 'aktif',
            'ID Kelas' => '',
            'ID Sekolah' => '',
        ];

        $row = [];
        foreach ($sheetHeaders as $header) {
            $row[$header] = $mapped[$header] ?? '';
        }

        return $row;
    }

    protected function buildDate(?int $year, ?int $month, ?int $day): string
    {
        if (!$year || !$month || !$day) return '';
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    protected function buildJsonField(array $data): string
    {
        $filtered = array_filter($data, fn($v) => !empty($v));
        return empty($filtered) ? '' : json_encode($filtered, JSON_UNESCAPED_UNICODE);
    }

    protected function writeToSheet(GoogleSheetSetting $setting, array $headers, array $rows): void
    {
        try {
            $this->info('Membersihkan sheet...');
            $this->googleSheetService->clearSheetRange($setting);

            $this->info('Menulis ' . count($rows) . ' baris ke Google Sheet...');
            $this->googleSheetService->appendSheetData($setting, $headers, $rows);
            $setting->update(['last_sync_at' => now()]);
        } catch (\Throwable $e) {
            $this->error('Gagal menulis ke Google Sheet: ' . $e->getMessage());
            Log::error('SyncPmbmToSheet - Gagal menulis ke sheet: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
