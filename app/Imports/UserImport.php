<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Guru;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class UserImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    private $failures = [];
    private $rowCount = 0;
    private $createdCount = 0;
    private $updatedCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;
        $sekolahId = null;
        if (!empty($row['npsn_sekolah'])) {
            $sekolah = Sekolah::where('npsn', $row['npsn_sekolah'])->first();
            if (!$sekolah) return null;
            $sekolahId = $sekolah->id;
        }

        $isNew = false;

        $user = User::where('email', $row['email'])->first();
        if ($user) {
            $user->update([
                'name'       => $row['nama'],
                'sekolah_id' => in_array($row['role'], ['Dinas', 'Super Admin']) ? null : $sekolahId,
            ]);
            if (!empty($row['password'])) {
                $user->update(['password' => Hash::make($row['password'])]);
            }
            $this->updatedCount++;
        } else {
            $user = User::create([
                'name'       => $row['nama'],
                'email'      => $row['email'],
                'password'   => Hash::make($row['password'] ?? 'password123'),
                'sekolah_id' => in_array($row['role'], ['Dinas', 'Super Admin']) ? null : $sekolahId,
            ]);
            $isNew = true;
            $this->createdCount++;
        }

        $user->syncRoles([$row['role']]);

        if ($row['role'] === 'Guru') {
            Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'sekolah_id' => $sekolahId,
                    'nik'        => $row['nik'] ?? null,
                    'nuptk'      => $row['nuptk'] ?? null,
                    'nama'       => $row['nama'],
                ]
            );
        } else {
            Guru::where('user_id', $user->id)->delete();
        }

        return $user;
    }

    public function rules(): array
    {
        return [
            'nama'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'password'     => 'nullable|string|min:8',
            'npsn_sekolah' => 'nullable|exists:sekolah,npsn',
            'role'         => 'required|in:Super Admin,Dinas,Operator,Kepala Sekolah,Guru,Siswa,Orang Tua',
            'nik'          => 'nullable|string|max:50',
            'nuptk'        => 'nullable|string|max:50',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required'         => 'Nama harus diisi',
            'email.required'        => 'Email harus diisi',
            'email.email'           => 'Format email tidak valid',
            'password.min'          => 'Password minimal 8 karakter',
            'npsn_sekolah.exists'   => 'NPSN Sekolah :input tidak ditemukan',
            'role.required'         => 'Role harus diisi',
            'role.in'               => 'Role :input tidak valid',
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
