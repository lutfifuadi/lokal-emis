<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class KelasImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    private $failures = [];
    private $rowCount = 0;
    private $createdCount = 0;
    private $updatedCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;
        $sekolah = Sekolah::where('npsn', $row['npsn_sekolah'])->first();
        if (!$sekolah) return null;

        $jurusan = Jurusan::where('sekolah_id', $sekolah->id)
            ->where('kode', $row['kode_jurusan'])
            ->first();
        if (!$jurusan) return null;

        $tahunAjaran = TahunAjaran::where('sekolah_id', $sekolah->id)
            ->where('tahun', $row['tahun'])
            ->where('semester', $row['semester'])
            ->first();
        if (!$tahunAjaran) return null;

        $kelas = Kelas::updateOrCreate(
            ['sekolah_id' => $sekolah->id, 'nama' => $row['nama']],
            [
                'jurusan_id'      => $jurusan->id,
                'tingkat'         => $row['tingkat'],
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]
        );
        if ($kelas->wasRecentlyCreated) {
            $this->createdCount++;
        } else {
            $this->updatedCount++;
        }
        return $kelas;
    }

    public function rules(): array
    {
        return [
            'npsn_sekolah' => 'required|exists:sekolah,npsn',
            'kode_jurusan' => 'required|string|max:50',
            'nama'         => 'required|string|max:255',
            'tingkat'      => 'required|integer|min:1|max:13',
            'tahun'        => 'required|string|max:20',
            'semester'     => 'required|in:ganjil,genap',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'npsn_sekolah.required' => 'NPSN sekolah harus diisi',
            'npsn_sekolah.exists'   => 'NPSN Sekolah :input tidak ditemukan',
            'kode_jurusan.required' => 'Kode jurusan harus diisi',
            'nama.required'         => 'Nama kelas harus diisi',
            'tingkat.required'      => 'Tingkat harus diisi',
            'tingkat.integer'       => 'Tingkat harus berupa angka',
            'tingkat.min'           => 'Tingkat minimal 1',
            'tingkat.max'           => 'Tingkat maksimal 13 (SD 1-6, SMP 7-9, SMA 10-12)',
            'tahun.required'        => 'Tahun ajaran harus diisi',
            'semester.required'     => 'Semester harus diisi',
            'semester.in'           => 'Semester harus ganjil atau genap',
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
