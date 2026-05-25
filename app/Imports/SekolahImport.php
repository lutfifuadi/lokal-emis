<?php

namespace App\Imports;

use App\Models\Sekolah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class SekolahImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    private $failures = [];
    private $rowCount = 0;
    private $createdCount = 0;
    private $updatedCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;
        $sekolah = Sekolah::updateOrCreate(
            ['npsn' => $row['npsn']],
            [
                'nama'   => $row['nama'],
                'alamat' => $row['alamat'] ?? null,
                'kontak' => $row['kontak'] ?? null,
                'email'  => $row['email'] ?? null,
            ]
        );
        if ($sekolah->wasRecentlyCreated) {
            $this->createdCount++;
        } else {
            $this->updatedCount++;
        }
        return $sekolah;
    }

    public function rules(): array
    {
        return [
            'npsn'   => 'required|numeric|digits:8',
            'nama'   => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string|max:20',
            'email'  => 'nullable|email|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'npsn.required'  => 'NPSN harus diisi',
            'npsn.numeric'   => 'NPSN harus berupa angka',
            'npsn.digits'    => 'NPSN harus 8 digit',
            'nama.required'  => 'Nama sekolah harus diisi',
            'nama.max'       => 'Nama sekolah maksimal 255 karakter',
            'email.email'    => 'Format email tidak valid',
            'kontak.max'     => 'Kontak maksimal 20 karakter',
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
