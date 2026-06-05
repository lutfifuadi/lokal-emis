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
                'email',
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
                'tanggal_lahir_ayah',
                'bulan_lahir_ayah',
                'tahun_lahir_ayah',
                'pendidikan_ayah',
                'pekerjaan_ayah',
                'penghasilan_ayah',
                'kewarganegaraan_ayah',
                'status_ayah',
                'alamat_ayah',
                'rt_ayah',
                'rw_ayah',
                'desa_ayah',
                'kecamatan_ayah',
                'kabupaten_ayah',
                'provinsi_ayah',
                'kode_pos_ayah',
                'nama_ibu',
                'nik_ibu',
                'tempat_lahir_ibu',
                'tanggal_lahir_ibu',
                'bulan_lahir_ibu',
                'tahun_lahir_ibu',
                'pendidikan_ibu',
                'pekerjaan_ibu',
                'penghasilan_ibu',
                'kewarganegaraan_ibu',
                'status_ibu',
                'alamat_ibu',
                'rt_ibu',
                'rw_ibu',
                'desa_ibu',
                'kecamatan_ibu',
                'kabupaten_ibu',
                'provinsi_ibu',
                'kode_pos_ibu',
                'nama_wali',
                'nik_wali',
                'phone_wali',
                'pendidikan_wali',
                'pekerjaan_wali',
                'penghasilan_wali',
                'alamat_wali',
                'kewarganegaraan_wali',
                'status_wali',
                'tanggal_lahir_wali',
                'bulan_lahir_wali',
                'tahun_lahir_wali',
                'rw_wali',
                'rt_wali',
                'desa_wali',
                'kecamatan_wali',
                'kabupaten_wali',
                'provinsi_wali',
                'kode_pos_wali',
                'nomor_kk',
                'nomor_kip',
                'asal_sekolah',
                'nama_sekolah',
                'npsn',
                'jenis_sekolah',
                'status_sekolah',
                'alamat_sekolah',
                'pernah_tk',
                'pernah_paud',
                'aktivitas_belajar',
                'kesulitan',
                'kebutuhan_alat_bantu',
                'kebutuhan_pendamping',
                'kebutuhan_penyesuaian',
                'kebutuhan_disabilitas',
                'imunisasi_hepatitis_b',
                'imunisasi_bcg',
                'imunisasi_polio',
                'imunisasi_dpt',
                'imunisasi_campak',
                'imunisasi_hib',
                'imunisasi_covid19',
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

        $tanggalLahirAyah = $this->buildDate(
            $p['tahun_lahir_ayah'] ?? null,
            $p['bulan_lahir_ayah'] ?? null,
            $p['tanggal_lahir_ayah'] ?? null
        );

        $tanggalLahirIbu = $this->buildDate(
            $p['tahun_lahir_ibu'] ?? null,
            $p['bulan_lahir_ibu'] ?? null,
            $p['tanggal_lahir_ibu'] ?? null
        );

        $tanggalLahirWali = $this->buildDate(
            $p['tahun_lahir_wali'] ?? null,
            $p['bulan_lahir_wali'] ?? null,
            $p['tanggal_lahir_wali'] ?? null
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
            'Email' => $p['email'] ?? '',
            'Tempat Lahir' => $p['tempat_lahir'] ?? '',
            'Tanggal Lahir' => $tanggalLahir,
            'Bulan Lahir' => (string) ($p['bulan_lahir'] ?? ''),
            'Tahun Lahir' => (string) ($p['tahun_lahir'] ?? ''),
            'Jenis Kelamin' => $p['jenis_kelamin'] ?? '',
            'Agama' => 'ISLAM',
            'Kewarganegaraan' => $p['kewarganegaraan'] ?? '',
            'Kebutuhan Khusus' => $p['kebutuhan_khusus'] ?? '',
            'Kesulitan' => $p['kesulitan'] ?? '',
            'Kebutuhan Alat Bantu' => $p['kebutuhan_alat_bantu'] ?? '',
            'Kebutuhan Pendamping' => $p['kebutuhan_pendamping'] ?? '',
            'Kebutuhan Penyesuaian' => $p['kebutuhan_penyesuaian'] ?? '',
            'Kebutuhan Disabilitas' => $p['kebutuhan_disabilitas'] ?? '',
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
            'Tanggal Lahir Ayah' => $tanggalLahirAyah,
            'Pendidikan Ayah' => $p['pendidikan_ayah'] ?? '',
            'Pekerjaan Ayah' => $p['pekerjaan_ayah'] ?? '',
            'Penghasilan Ayah' => $p['penghasilan_ayah'] ?? '',
            'Kewarganegaraan Ayah' => $p['kewarganegaraan_ayah'] ?? '',
            'Status Ayah' => $p['status_ayah'] ?? '',
            'Alamat Ayah' => $p['alamat_ayah'] ?? '',
            'RT Ayah' => (string) ($p['rt_ayah'] ?? ''),
            'RW Ayah' => (string) ($p['rw_ayah'] ?? ''),
            'Desa Ayah' => $p['desa_ayah'] ?? '',
            'Kecamatan Ayah' => $p['kecamatan_ayah'] ?? '',
            'Kabupaten Ayah' => $p['kabupaten_ayah'] ?? '',
            'Provinsi Ayah' => $p['provinsi_ayah'] ?? '',
            'Kode Pos Ayah' => $p['kode_pos_ayah'] ?? '',
            'Nama Ibu' => $p['nama_ibu'] ?? '',
            'NIK Ibu' => $p['nik_ibu'] ?? '',
            'Tempat Lahir Ibu' => $p['tempat_lahir_ibu'] ?? '',
            'Tanggal Lahir Ibu' => $tanggalLahirIbu,
            'Pendidikan Ibu' => $p['pendidikan_ibu'] ?? '',
            'Pekerjaan Ibu' => $p['pekerjaan_ibu'] ?? '',
            'Penghasilan Ibu' => $p['penghasilan_ibu'] ?? '',
            'Kewarganegaraan Ibu' => $p['kewarganegaraan_ibu'] ?? '',
            'Status Ibu' => $p['status_ibu'] ?? '',
            'Alamat Ibu' => $p['alamat_ibu'] ?? '',
            'RT Ibu' => (string) ($p['rt_ibu'] ?? ''),
            'RW Ibu' => (string) ($p['rw_ibu'] ?? ''),
            'Desa Ibu' => $p['desa_ibu'] ?? '',
            'Kecamatan Ibu' => $p['kecamatan_ibu'] ?? '',
            'Kabupaten Ibu' => $p['kabupaten_ibu'] ?? '',
            'Provinsi Ibu' => $p['provinsi_ibu'] ?? '',
            'Kode Pos Ibu' => $p['kode_pos_ibu'] ?? '',
            'Nama Wali' => $p['nama_wali'] ?? '',
            'NIK Wali' => $p['nik_wali'] ?? '',
            'No. HP Wali' => $p['phone_wali'] ?? '',
            'Pendidikan Wali' => $p['pendidikan_wali'] ?? '',
            'Pekerjaan Wali' => $p['pekerjaan_wali'] ?? '',
            'Penghasilan Wali' => $p['penghasilan_wali'] ?? '',
            'Kewarganegaraan Wali' => $p['kewarganegaraan_wali'] ?? '',
            'Status Wali' => $p['status_wali'] ?? '',
            'Tanggal Lahir Wali' => $tanggalLahirWali,
            'Alamat Wali' => $p['alamat_wali'] ?? '',
            'RT Wali' => (string) ($p['rt_wali'] ?? ''),
            'RW Wali' => (string) ($p['rw_wali'] ?? ''),
            'Desa Wali' => $p['desa_wali'] ?? '',
            'Kecamatan Wali' => $p['kecamatan_wali'] ?? '',
            'Kabupaten Wali' => $p['kabupaten_wali'] ?? '',
            'Provinsi Wali' => $p['provinsi_wali'] ?? '',
            'Kode Pos Wali' => $p['kode_pos_wali'] ?? '',
            'Nama Kepala Keluarga' => '',
            'No. KK' => $p['nomor_kk'] ?? '',
            'No. KIP' => $p['nomor_kip'] ?? '',
            'Asal Sekolah' => $asalSekolah,
            'NPSN Sekolah Asal' => (string) ($p['npsn'] ?? ''),
            'Jenis Sekolah Asal' => $p['jenis_sekolah'] ?? '',
            'Status Sekolah Asal' => $p['status_sekolah'] ?? '',
            'Alamat Sekolah Asal' => $p['alamat_sekolah'] ?? '',
            'Pernah TK' => $p['pernah_tk'] ?? '',
            'Pernah PAUD' => $p['pernah_paud'] ?? '',
            'Aktivitas Belajar' => $p['aktivitas_belajar'] ?? '',
            'Cita-cita' => $p['cita_cita'] ?? '',
            'Hobi' => $p['hobi'] ?? '',
            'Riwayat Penyakit' => $p['riwayat_penyakit'] ?? '',
            'Imunisasi Hepatitis B' => $p['imunisasi_hepatitis_b'] ?? '',
            'Imunisasi BCG' => $p['imunisasi_bcg'] ?? '',
            'Imunisasi Polio' => $p['imunisasi_polio'] ?? '',
            'Imunisasi DPT' => $p['imunisasi_dpt'] ?? '',
            'Imunisasi Campak' => $p['imunisasi_campak'] ?? '',
            'Imunisasi Hib' => $p['imunisasi_hib'] ?? '',
            'Imunisasi Covid19' => $p['imunisasi_covid19'] ?? '',
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
