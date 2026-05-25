<?php

namespace App\Imports;

use App\Models\Jurusan;
use App\Models\Sekolah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class JurusanImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
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

        $jurusan = Jurusan::updateOrCreate(
            ['sekolah_id' => $sekolah->id, 'kode' => $row['kode']],
            ['nama' => $row['nama']]
        );
        if ($jurusan->wasRecentlyCreated) {
            $this->createdCount++;
        } else {
            $this->updatedCount++;
        }
        return $jurusan;
    }

    public function rules(): array
    {
        return [
            'npsn_sekolah' => 'required|exists:sekolah,npsn',
            'kode'         => 'required|string|max:50',
            'nama'         => 'required|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'npsn_sekolah.required' => 'NPSN sekolah harus diisi',
            'npsn_sekolah.exists'   => 'NPSN Sekolah :input tidak ditemukan',
            'kode.required'         => 'Kode jurusan harus diisi',
            'kode.max'              => 'Kode jurusan maksimal 50 karakter',
            'nama.required'         => 'Nama jurusan harus diisi',
            'nama.max'              => 'Nama jurusan maksimal 255 karakter',
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
