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
                'user_id'             => $user->id,
                'sekolah_id'          => $sekolah->id,
                'kelas_id'            => $kelasId,
                'nik'                 => $row['nik'] ?? null,
                'nama'                => $row['nama'],
                'alamat'              => $row['alamat'] ?? null,
                'no_hp'               => $row['no_hp'] ?? null,
                'kontak_darurat'      => $row['kontak_darurat'] ?? null,
                'status'              => $row['status'] ?? 'aktif',
                'tempat_lahir'        => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'       => $tanggalLahir,
                'jenis_kelamin'       => $row['jenis_kelamin'] ?? null,
                'agama'               => $row['agama'] ?? null,
                'kewarganegaraan'     => $row['kewarganegaraan'] ?? null,
                'kode_pos'            => $row['kode_pos'] ?? null,
                'anak_ke'             => !empty($row['anak_ke']) ? (int) $row['anak_ke'] : null,
                'jml_saudara'         => !empty($row['jml_saudara']) ? (int) $row['jml_saudara'] : null,
                'kebutuhan_khusus'    => $row['kebutuhan_khusus'] ?? null,
                'transportasi'        => $row['transportasi'] ?? null,
                'jarak_tempuh'        => !empty($row['jarak_tempuh']) ? (float) $row['jarak_tempuh'] : null,
                'tinggi_badan'        => !empty($row['tinggi_badan']) ? (float) $row['tinggi_badan'] : null,
                'berat_badan'         => !empty($row['berat_badan']) ? (float) $row['berat_badan'] : null,
                'nama_ayah'           => $row['nama_ayah'] ?? null,
                'nik_ayah'            => $row['nik_ayah'] ?? null,
                'pendidikan_ayah'     => $row['pendidikan_ayah'] ?? null,
                'pekerjaan_ayah'      => $row['pekerjaan_ayah'] ?? null,
                'penghasilan_ayah'    => $row['penghasilan_ayah'] ?? null,
                'nama_ibu'            => $row['nama_ibu'] ?? null,
                'nik_ibu'             => $row['nik_ibu'] ?? null,
                'pendidikan_ibu'      => $row['pendidikan_ibu'] ?? null,
                'pekerjaan_ibu'       => $row['pekerjaan_ibu'] ?? null,
                'penghasilan_ibu'     => $row['penghasilan_ibu'] ?? null,
                'no_hp_ortu'          => $row['no_hp_ortu'] ?? null,
                'no_kk'               => $row['no_kk'] ?? null,
                'nama_kepala_keluarga' => $row['nama_kepala_keluarga'] ?? null,
                'no_kip'              => $row['no_kip'] ?? null,
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
            'nisn'               => 'required|string|max:50',
            'nik'                => 'nullable|string|max:50',
            'nama'               => 'required|string|max:255',
            'alamat'             => 'nullable|string',
            'no_hp'              => 'nullable|string|max:20',
            'kontak_darurat'     => 'nullable|string|max:20',
            'status'             => 'nullable|in:aktif,lulus,pindah,keluar',
            'npsn_sekolah'       => 'required|exists:sekolah,npsn',
            'kode_kelas'         => 'nullable|string|max:255',
            'email'              => 'required|email|max:255',
            'password'           => 'nullable|string|min:8',
            'tempat_lahir'       => 'nullable|string|max:255',
            'tanggal_lahir'      => 'nullable',
            'jenis_kelamin'      => 'nullable|in:L,P',
            'agama'              => 'nullable|string|max:50',
            'kewarganegaraan'    => 'nullable|string|max:100',
            'kode_pos'           => 'nullable|string|max:10',
            'anak_ke'            => 'nullable|integer|min:1',
            'jml_saudara'        => 'nullable|integer|min:0',
            'kebutuhan_khusus'   => 'nullable|string|max:100',
            'transportasi'       => 'nullable|string|max:100',
            'jarak_tempuh'       => 'nullable|numeric|min:0',
            'tinggi_badan'       => 'nullable|numeric|min:0',
            'berat_badan'        => 'nullable|numeric|min:0',
            'nama_ayah'          => 'nullable|string|max:255',
            'nik_ayah'           => 'nullable|string|max:50',
            'pendidikan_ayah'    => 'nullable|string|max:50',
            'pekerjaan_ayah'     => 'nullable|string|max:100',
            'penghasilan_ayah'   => 'nullable|string|max:50',
            'nama_ibu'           => 'nullable|string|max:255',
            'nik_ibu'            => 'nullable|string|max:50',
            'pendidikan_ibu'     => 'nullable|string|max:50',
            'pekerjaan_ibu'      => 'nullable|string|max:100',
            'penghasilan_ibu'    => 'nullable|string|max:50',
            'no_hp_ortu'         => 'nullable|string|max:20',
            'no_kk'              => 'nullable|string|max:30',
            'nama_kepala_keluarga' => 'nullable|string|max:255',
            'no_kip'             => 'nullable|string|max:30',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nisn.required'                   => 'NISN harus diisi',
            'nama.required'                   => 'Nama harus diisi',
            'npsn_sekolah.required'           => 'NPSN sekolah harus diisi',
            'npsn_sekolah.exists'             => 'NPSN Sekolah :input tidak ditemukan',
            'email.required'                  => 'Email harus diisi',
            'email.email'                     => 'Format email tidak valid',
            'status.in'                       => 'Status harus: aktif, lulus, pindah, atau keluar',
            'jenis_kelamin.in'                => 'Jenis Kelamin harus L atau P',
            'anak_ke.integer'                 => 'Anak ke harus berupa angka',
            'jml_saudara.integer'             => 'Jumlah saudara harus berupa angka',
            'jarak_tempuh.numeric'            => 'Jarak tempuh harus berupa angka',
            'tinggi_badan.numeric'            => 'Tinggi badan harus berupa angka',
            'berat_badan.numeric'             => 'Berat badan harus berupa angka',
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
