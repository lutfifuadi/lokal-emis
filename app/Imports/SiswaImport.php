<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    private $failures = [];
    private $rowCount = 0;
    private $createdCount = 0;
    private $updatedCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;
        return DB::transaction(function () use ($row) {
            $sekolah = Sekolah::where('npsn', $row['npsn_sekolah'])->first();
            if (!$sekolah) return null;

            $kelasId = null;
            if (!empty($row['kode_kelas'])) {
                $kelas = Kelas::where('sekolah_id', $sekolah->id)
                    ->where('nama', $row['kode_kelas'])
                    ->first();
                $kelasId = $kelas?->id;
            }

            $user = User::where('email', $row['email'])->orWhere('username', $row['nisn'])->first();
            if ($user) {
                $user->update([
                    'name'       => $row['nama'],
                    'username'   => $row['nisn'],
                    'sekolah_id' => $sekolah->id,
                ]);
                if (!empty($row['password'])) {
                    $user->update(['password' => Hash::make($row['password'])]);
                }
                $user->syncRoles(['Siswa']);
            } else {
                $user = User::create([
                    'name'       => $row['nama'],
                    'email'      => $row['email'],
                    'username'   => $row['nisn'],
                    'password'   => Hash::make($row['password'] ?? $row['nisn']),
                    'sekolah_id' => $sekolah->id,
                ]);
                $user->syncRoles(['Siswa']);
            }

            $tanggalLahir = null;
            if (!empty($row['tanggal_lahir'])) {
                try {
                    if (is_numeric($row['tanggal_lahir'])) {
                        $tanggalLahir = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir']));
                    } else {
                        $tanggalLahir = Carbon::parse($row['tanggal_lahir']);
                    }
                } catch (\Exception $e) {
                    $tanggalLahir = null;
                }
            }

            $data = [
                'user_id'                 => $user->id,
                'sekolah_id'              => $sekolah->id,
                'kelas_id'                => $kelasId,
                'nik'                     => $row['nik'] ?? null,
                'nama'                    => $row['nama'],
                'email'                   => $row['email'] ?? null,
                'alamat'                  => $row['alamat'] ?? null,
                'no_hp'                   => $row['no_hp'] ?? null,
                'status'                  => $row['status'] ?? 'aktif',
                'tempat_lahir'            => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'           => $tanggalLahir,
                'bulan_lahir'             => !empty($row['bulan_lahir']) ? (int) $row['bulan_lahir'] : null,
                'tahun_lahir'             => !empty($row['tahun_lahir']) ? (int) $row['tahun_lahir'] : null,
                'jenis_kelamin'           => $row['jenis_kelamin'] ?? null,
                'agama'                   => $row['agama'] ?? null,
                'kewarganegaraan'         => $row['kewarganegaraan'] ?? null,
                'kebutuhan_khusus'        => $row['kebutuhan_khusus'] ?? null,
                'kesulitan'               => $row['kesulitan'] ?? null,
                'kebutuhan_alat_bantu'    => $row['kebutuhan_alat_bantu'] ?? null,
                'kebutuhan_pendamping'    => $row['kebutuhan_pendamping'] ?? null,
                'kebutuhan_penyesuaian'   => $row['kebutuhan_penyesuaian'] ?? null,
                'kebutuhan_disabilitas'   => $row['kebutuhan_disabilitas'] ?? null,
                'kode_pos'                => $row['kode_pos'] ?? null,
                'anak_ke'                 => !empty($row['anak_ke']) ? (int) $row['anak_ke'] : null,
                'jml_saudara'             => !empty($row['jml_saudara']) ? (int) $row['jml_saudara'] : null,
                'transportasi'            => $row['transportasi'] ?? null,
                'jarak_tempuh'            => !empty($row['jarak_tempuh']) ? (string) $row['jarak_tempuh'] : null,
                'nama_ayah'               => $row['nama_ayah'] ?? null,
                'nik_ayah'                => $row['nik_ayah'] ?? null,
                'tempat_lahir_ayah'       => $row['tempat_lahir_ayah'] ?? null,
                'tanggal_lahir_ayah'      => $row['tanggal_lahir_ayah'] ?? null,
                'pendidikan_ayah'         => $row['pendidikan_ayah'] ?? null,
                'pekerjaan_ayah'          => $row['pekerjaan_ayah'] ?? null,
                'penghasilan_ayah'        => $row['penghasilan_ayah'] ?? null,
                'kewarganegaraan_ayah'    => $row['kewarganegaraan_ayah'] ?? null,
                'status_ayah'             => $row['status_ayah'] ?? null,
                'alamat_ayah'             => $row['alamat_ayah'] ?? null,
                'rt_ayah'                 => $row['rt_ayah'] ?? null,
                'rw_ayah'                 => $row['rw_ayah'] ?? null,
                'desa_ayah'               => $row['desa_ayah'] ?? null,
                'kecamatan_ayah'          => $row['kecamatan_ayah'] ?? null,
                'kabupaten_ayah'          => $row['kabupaten_ayah'] ?? null,
                'provinsi_ayah'           => $row['provinsi_ayah'] ?? null,
                'kode_pos_ayah'           => $row['kode_pos_ayah'] ?? null,
                'nama_ibu'                => $row['nama_ibu'] ?? null,
                'nik_ibu'                 => $row['nik_ibu'] ?? null,
                'tempat_lahir_ibu'        => $row['tempat_lahir_ibu'] ?? null,
                'tanggal_lahir_ibu'       => $row['tanggal_lahir_ibu'] ?? null,
                'pendidikan_ibu'          => $row['pendidikan_ibu'] ?? null,
                'pekerjaan_ibu'           => $row['pekerjaan_ibu'] ?? null,
                'penghasilan_ibu'         => $row['penghasilan_ibu'] ?? null,
                'kewarganegaraan_ibu'     => $row['kewarganegaraan_ibu'] ?? null,
                'status_ibu'              => $row['status_ibu'] ?? null,
                'alamat_ibu'              => $row['alamat_ibu'] ?? null,
                'rt_ibu'                  => $row['rt_ibu'] ?? null,
                'rw_ibu'                  => $row['rw_ibu'] ?? null,
                'desa_ibu'                => $row['desa_ibu'] ?? null,
                'kecamatan_ibu'           => $row['kecamatan_ibu'] ?? null,
                'kabupaten_ibu'           => $row['kabupaten_ibu'] ?? null,
                'provinsi_ibu'            => $row['provinsi_ibu'] ?? null,
                'kode_pos_ibu'            => $row['kode_pos_ibu'] ?? null,
                'nama_wali'               => $row['nama_wali'] ?? null,
                'nik_wali'                => $row['nik_wali'] ?? null,
                'no_hp_wali'              => $row['no_hp_wali'] ?? null,
                'pendidikan_wali'         => $row['pendidikan_wali'] ?? null,
                'pekerjaan_wali'          => $row['pekerjaan_wali'] ?? null,
                'penghasilan_wali'        => $row['penghasilan_wali'] ?? null,
                'kewarganegaraan_wali'    => $row['kewarganegaraan_wali'] ?? null,
                'status_wali'             => $row['status_wali'] ?? null,
                'tanggal_lahir_wali'      => $row['tanggal_lahir_wali'] ?? null,
                'alamat_wali'             => $row['alamat_wali'] ?? null,
                'rt_wali'                 => $row['rt_wali'] ?? null,
                'rw_wali'                 => $row['rw_wali'] ?? null,
                'desa_wali'               => $row['desa_wali'] ?? null,
                'kecamatan_wali'          => $row['kecamatan_wali'] ?? null,
                'kabupaten_wali'          => $row['kabupaten_wali'] ?? null,
                'provinsi_wali'           => $row['provinsi_wali'] ?? null,
                'kode_pos_wali'           => $row['kode_pos_wali'] ?? null,
                'no_kk'                   => $row['no_kk'] ?? null,
                'nama_kepala_keluarga'    => $row['nama_kepala_keluarga'] ?? null,
                'no_kip'                  => $row['no_kip'] ?? null,
                'pernah_tk'               => $row['pernah_tk'] ?? null,
                'pernah_paud'             => $row['pernah_paud'] ?? null,
                'aktivitas_belajar'       => $row['aktivitas_belajar'] ?? null,
                'cita_cita'               => $row['cita_cita'] ?? null,
                'hobi'                    => $row['hobi'] ?? null,
                'riwayat_penyakit'        => $row['riwayat_penyakit'] ?? null,
                'imunisasi_hepatitis_b'   => $row['imunisasi_hepatitis_b'] ?? null,
                'imunisasi_bcg'           => $row['imunisasi_bcg'] ?? null,
                'imunisasi_polio'         => $row['imunisasi_polio'] ?? null,
                'imunisasi_dpt'           => $row['imunisasi_dpt'] ?? null,
                'imunisasi_campak'        => $row['imunisasi_campak'] ?? null,
                'imunisasi_hib'           => $row['imunisasi_hib'] ?? null,
                'imunisasi_covid19'       => $row['imunisasi_covid19'] ?? null,
                'prestasi'                => $row['prestasi'] ?? null,
                'beasiswa'                => $row['beasiswa'] ?? null,
            ];

            $siswa = Siswa::where('nisn', $row['nisn'])->first();
            if ($siswa) {
                $siswa->update($data);
                $this->updatedCount++;
            } else {
                $data['nisn'] = $row['nisn'];
                $siswa = Siswa::create($data);
                $this->createdCount++;
            }

            return $siswa;
        });
    }

    public function rules(): array
    {
        return [
            'nisn'                  => 'required|string|max:50',
            'nik'                   => 'nullable|string|max:50',
            'nama'                  => 'required|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'alamat'                => 'nullable|string',
            'no_hp'                 => 'nullable|string|max:20',
            'status'                => 'nullable|in:aktif,lulus,pindah,keluar',
            'npsn_sekolah'          => 'required|exists:sekolah,npsn',
            'kode_kelas'            => 'nullable|string|max:255',
            'password'              => 'nullable|string|min:8',
            'tempat_lahir'          => 'nullable|string|max:255',
            'tanggal_lahir'         => 'nullable',
            'bulan_lahir'           => 'nullable|integer|min:1|max:12',
            'tahun_lahir'           => 'nullable|integer|min:1900|max:9999',
            'jenis_kelamin'         => 'nullable|in:L,P',
            'agama'                 => 'nullable|string|max:50',
            'kewarganegaraan'       => 'nullable|string|max:100',
            'kebutuhan_khusus'      => 'nullable|string|max:100',
            'kesulitan'             => 'nullable|string',
            'kebutuhan_alat_bantu'  => 'nullable|string',
            'kebutuhan_pendamping'  => 'nullable|string',
            'kebutuhan_penyesuaian' => 'nullable|string',
            'kebutuhan_disabilitas' => 'nullable|string',
            'kode_pos'              => 'nullable|string|max:10',
            'anak_ke'               => 'nullable|integer|min:1',
            'jml_saudara'           => 'nullable|integer|min:0',
            'transportasi'          => 'nullable|string|max:100',
            'jarak_tempuh'          => 'nullable|string|max:100',
            'nama_ayah'             => 'nullable|string|max:255',
            'nik_ayah'              => 'nullable|string|max:50',
            'tempat_lahir_ayah'     => 'nullable|string|max:255',
            'tanggal_lahir_ayah'    => 'nullable',
            'pendidikan_ayah'       => 'nullable|string|max:50',
            'pekerjaan_ayah'        => 'nullable|string|max:100',
            'penghasilan_ayah'      => 'nullable|string|max:50',
            'kewarganegaraan_ayah'  => 'nullable|string|max:20',
            'status_ayah'           => 'nullable|string|max:50',
            'alamat_ayah'           => 'nullable|string',
            'rt_ayah'               => 'nullable|string|max:5',
            'rw_ayah'               => 'nullable|string|max:5',
            'desa_ayah'             => 'nullable|string|max:255',
            'kecamatan_ayah'        => 'nullable|string|max:255',
            'kabupaten_ayah'        => 'nullable|string|max:255',
            'provinsi_ayah'         => 'nullable|string|max:255',
            'kode_pos_ayah'         => 'nullable|string|max:10',
            'nama_ibu'              => 'nullable|string|max:255',
            'nik_ibu'               => 'nullable|string|max:50',
            'tempat_lahir_ibu'      => 'nullable|string|max:255',
            'tanggal_lahir_ibu'     => 'nullable',
            'pendidikan_ibu'        => 'nullable|string|max:50',
            'pekerjaan_ibu'         => 'nullable|string|max:100',
            'penghasilan_ibu'       => 'nullable|string|max:50',
            'kewarganegaraan_ibu'   => 'nullable|string|max:20',
            'status_ibu'            => 'nullable|string|max:50',
            'alamat_ibu'            => 'nullable|string',
            'rt_ibu'                => 'nullable|string|max:5',
            'rw_ibu'                => 'nullable|string|max:5',
            'desa_ibu'              => 'nullable|string|max:255',
            'kecamatan_ibu'         => 'nullable|string|max:255',
            'kabupaten_ibu'         => 'nullable|string|max:255',
            'provinsi_ibu'          => 'nullable|string|max:255',
            'kode_pos_ibu'          => 'nullable|string|max:10',
            'nama_wali'             => 'nullable|string|max:255',
            'nik_wali'              => 'nullable|string|max:50',
            'no_hp_wali'            => 'nullable|string|max:20',
            'pendidikan_wali'       => 'nullable|string|max:50',
            'pekerjaan_wali'        => 'nullable|string|max:100',
            'penghasilan_wali'      => 'nullable|string|max:50',
            'kewarganegaraan_wali'  => 'nullable|string|max:20',
            'status_wali'           => 'nullable|string|max:50',
            'tanggal_lahir_wali'    => 'nullable',
            'alamat_wali'           => 'nullable|string',
            'rt_wali'               => 'nullable|string|max:5',
            'rw_wali'               => 'nullable|string|max:5',
            'desa_wali'             => 'nullable|string|max:255',
            'kecamatan_wali'        => 'nullable|string|max:255',
            'kabupaten_wali'        => 'nullable|string|max:255',
            'provinsi_wali'         => 'nullable|string|max:255',
            'kode_pos_wali'         => 'nullable|string|max:10',
            'no_kk'                 => 'nullable|string|max:30',
            'nama_kepala_keluarga'  => 'nullable|string|max:255',
            'no_kip'                => 'nullable|string|max:30',
            'pernah_tk'             => 'nullable|string|max:10',
            'pernah_paud'           => 'nullable|string|max:10',
            'aktivitas_belajar'     => 'nullable|string',
            'cita_cita'             => 'nullable|string|max:255',
            'hobi'                  => 'nullable|string|max:255',
            'riwayat_penyakit'      => 'nullable|string',
            'imunisasi_hepatitis_b' => 'nullable|string|max:20',
            'imunisasi_bcg'         => 'nullable|string|max:20',
            'imunisasi_polio'       => 'nullable|string|max:20',
            'imunisasi_dpt'         => 'nullable|string|max:20',
            'imunisasi_campak'      => 'nullable|string|max:20',
            'imunisasi_hib'         => 'nullable|string|max:20',
            'imunisasi_covid19'     => 'nullable|string|max:20',
            'prestasi'              => 'nullable|string',
            'beasiswa'              => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nisn.required'                   => 'NISN harus diisi',
            'nama.required'                   => 'Nama harus diisi',
            'npsn_sekolah.required'           => 'NPSN sekolah harus diisi',
            'npsn_sekolah.exists'             => 'NPSN Sekolah :input tidak ditemukan',
            'email.email'                     => 'Format email tidak valid',
            'status.in'                       => 'Status harus: aktif, lulus, pindah, atau keluar',
            'jenis_kelamin.in'                => 'Jenis Kelamin harus L atau P',
            'anak_ke.integer'                 => 'Anak ke harus berupa angka',
            'jml_saudara.integer'             => 'Jumlah saudara harus berupa angka',
            'bulan_lahir.integer'             => 'Bulan lahir harus berupa angka',
            'bulan_lahir.min'                 => 'Bulan lahir minimal 1',
            'bulan_lahir.max'                 => 'Bulan lahir maksimal 12',
            'tahun_lahir.integer'             => 'Tahun lahir harus berupa angka',
            'jarak_tempuh.max'                => 'Jarak tempuh maksimal 100 karakter',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row'    => $failure->row(),
                'field'  => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }
}
