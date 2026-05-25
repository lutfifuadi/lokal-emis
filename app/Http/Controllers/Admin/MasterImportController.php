<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\SekolahImport;
use App\Imports\JurusanImport;
use App\Imports\TahunAjaranImport;
use App\Imports\KelasImport;
use App\Imports\UserImport;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MasterImportController extends Controller
{
    private $importClasses = [
        'sekolah'       => SekolahImport::class,
        'jurusan'       => JurusanImport::class,
        'tahun-ajaran'  => TahunAjaranImport::class,
        'kelas'         => KelasImport::class,
        'users'         => UserImport::class,
        'siswa'         => SiswaImport::class,
    ];

    public function import(Request $request)
    {
        // Nonaktifkan batasan waktu eksekusi agar proses import tidak mengalami timeout
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $request->validate([
            'entity' => 'required|in:' . implode(',', array_keys($this->importClasses)),
            'file'   => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $entity = $request->input('entity');
        $file = $request->file('file');

        $importClass = $this->importClasses[$entity];
        $import = new $importClass();

        try {
            $import->import($file);

            $failures = $import->getFailures();
            $successCount = $import->getRowCount();

            return response()->json([
                'success'       => true,
                'message'       => 'Import berhasil diproses.',
                'total_rows'    => $successCount,
                'created_count' => $import->getCreatedCount(),
                'updated_count' => $import->getUpdatedCount(),
                'error_count'   => count($failures),
                'errors'        => $failures,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimport data: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function downloadSample($entity)
    {
        $samples = [
            'sekolah' => [
                'headers' => ['npsn', 'nama', 'alamat', 'kontak', 'email'],
                'rows' => [
                    ['12345678', 'SDN Cendekia Bangsa', 'Jl. Merdeka No. 1, Jakarta', '021-1234567', 'sdn_cendekia@sch.id'],
                    ['87654321', 'SMP Harapan Jaya', 'Jl. Sudirman No. 10, Bandung', '022-7654321', 'smp_harapan@sch.id'],
                    ['11223344', 'SMA Negeri 1 Surabaya', 'Jl. Pahlawan No. 5, Surabaya', '031-1122334', 'sman1_sby@sch.id'],
                ],
            ],
            'jurusan' => [
                'headers' => ['npsn_sekolah', 'kode', 'nama'],
                'rows' => [
                    ['12345678', 'IPA', 'Ilmu Pengetahuan Alam'],
                    ['12345678', 'IPS', 'Ilmu Pengetahuan Sosial'],
                    ['87654321', 'MM', 'Multimedia'],
                ],
            ],
            'tahun-ajaran' => [
                'headers' => ['npsn_sekolah', 'tahun', 'semester', 'is_active'],
                'rows' => [
                    ['12345678', '2025/2026', 'ganjil', 'true'],
                    ['12345678', '2025/2026', 'genap', 'false'],
                    ['87654321', '2025/2026', 'ganjil', 'true'],
                ],
            ],
            'kelas' => [
                'headers' => ['npsn_sekolah', 'kode_jurusan', 'nama', 'tingkat', 'tahun', 'semester'],
                'rows' => [
                    ['12345678', 'IPA', 'IPA-1', '10', '2025/2026', 'ganjil'],
                    ['12345678', 'IPA', 'IPA-2', '10', '2025/2026', 'ganjil'],
                    ['87654321', 'MM', 'MM-1', '11', '2025/2026', 'ganjil'],
                ],
            ],
            'users' => [
                'headers' => ['nama', 'email', 'password', 'npsn_sekolah', 'role', 'nik', 'nuptk'],
                'rows' => [
                    ['Budi Santoso', 'budi@sch.id', 'password123', '12345678', 'Guru', '3273010101900001', '1234567890123456'],
                    ['Siti Rahayu', 'siti@sch.id', 'password123', '12345678', 'Kepala Sekolah', '3273010101900002', ''],
                    ['Ahmad Fauzi', 'ahmad@mail.com', 'password123', '', 'Operator', '', ''],
                ],
            ],
            'siswa' => [
                'headers' => [
                    'nisn', 'nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
                    'alamat', 'kode_pos', 'no_hp', 'transportasi', 'jarak_tempuh',
                    'agama', 'kewarganegaraan', 'anak_ke', 'jml_saudara', 'kebutuhan_khusus',
                    'tinggi_badan', 'berat_badan',
                    'nama_ayah', 'nik_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah',
                    'nama_ibu', 'nik_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu',
                    'no_hp_ortu', 'no_kk', 'nama_kepala_keluarga', 'no_kip',
                    'status', 'npsn_sekolah', 'kode_kelas', 'email', 'password',
                ],
                'rows' => [
                    [
                        '1234567890', '3273010102100001', 'Andi Pratama', 'L', 'Jakarta', '2010-01-15',
                        'Jl. Merdeka No. 10', '10110', '081234567890', 'Motor', '3.5',
                        'Islam', 'WNI', '1', '2', '',
                        '160', '55',
                        'Budi Santoso', '3273010101800001', 'SMA', 'Wirausaha', '2.000.000 - 5.000.000',
                        'Siti Aminah', '3273010101850001', 'SMA', 'Ibu Rumah Tangga', '< 500.000',
                        '081298765432', '3273010102100001', 'Budi Santoso', '',
                        'aktif', '12345678', 'IPA-1', 'andi@mail.com', 'password123',
                    ],
                    [
                        '1234567891', '3273010102100002', 'Bunga Citra', 'P', 'Bandung', '2010-07-22',
                        'Jl. Merdeka No. 20', '10120', '081234567891', 'Angkutan Umum', '5.0',
                        'Islam', 'WNI', '2', '1', '',
                        '155', '48',
                        'Ahmad Rizki', '3273010101800002', 'S1', 'PNS', '> 5.000.000',
                        'Dewi Sartika', '3273010101850002', 'S1', 'PNS', '> 5.000.000',
                        '081298765433', '3273010102100002', 'Ahmad Rizki', '',
                        'aktif', '12345678', 'IPA-1', 'bunga@mail.com', 'password123',
                    ],
                    [
                        '1234567892', '3273010102100003', 'Citra Dewi', 'P', 'Surabaya', '2011-03-10',
                        'Jl. Merdeka No. 15', '10115', '081234567892', 'Jalan Kaki', '1.0',
                        'Kristen', 'WNI', '3', '0', '',
                        '150', '45',
                        'Hendra Gunawan', '3273010101800003', 'SMA', 'Karyawan Swasta', '1.000.000 - 2.000.000',
                        'Maria Oentari', '3273010101850003', 'D3', 'Perawat', '2.000.000 - 5.000.000',
                        '081298765434', '3273010102100003', 'Hendra Gunawan', '123456789',
                        'aktif', '87654321', 'MM-1', 'citra@mail.com', 'password123',
                    ],
                ],
            ],
        ];

        if (!isset($samples[$entity])) {
            abort(404, 'Entity not found');
        }

        $data = $samples[$entity];
        $filename = 'sample-' . $entity . '.csv';

        $handle = fopen('php://temp', 'w+');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $data['headers']);
        foreach ($data['rows'] as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
