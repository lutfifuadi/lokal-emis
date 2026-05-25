<?php

namespace App\Imports;

use App\Models\TahunAjaran;
use App\Models\Sekolah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class TahunAjaranImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
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

        $ta = TahunAjaran::updateOrCreate(
            [
                'sekolah_id' => $sekolah->id,
                'tahun'      => $row['tahun'],
                'semester'   => $row['semester'],
            ],
            ['is_active' => filter_var($row['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN)]
        );
        if ($ta->wasRecentlyCreated) {
            $this->createdCount++;
        } else {
            $this->updatedCount++;
        }
        return $ta;
    }

    public function rules(): array
    {
        return [
            'npsn_sekolah' => 'required|exists:sekolah,npsn',
            'tahun'        => 'required|string|max:20',
            'semester'     => 'required|in:ganjil,genap',
            'is_active'    => 'nullable|in:true,false,1,0,yes,no',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'npsn_sekolah.required' => 'NPSN sekolah harus diisi',
            'npsn_sekolah.exists'   => 'NPSN Sekolah :input tidak ditemukan',
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
