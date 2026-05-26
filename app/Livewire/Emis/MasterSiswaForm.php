<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterSiswaForm extends Component
{
    public $siswaId;

    // Data Pribadi
    public $sekolah_id, $kelas_id, $nisn, $nik, $nama;
    public $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $agama;
    public $alamat, $kode_pos, $no_hp, $kontak_darurat, $no_hp_ortu;
    public $anak_ke, $jml_saudara;
    public $status = 'aktif';

    // Data Ayah
    public $nama_ayah, $nik_ayah, $pendidikan_ayah, $pekerjaan_ayah, $penghasilan_ayah;

    // Data Ibu
    public $nama_ibu, $nik_ibu, $pendidikan_ibu, $pekerjaan_ibu, $penghasilan_ibu;

    // Data Tambahan
    public $kewarganegaraan = 'WNI', $kebutuhan_khusus, $no_kip, $no_kk, $nama_kepala_keluarga;
    public $transportasi, $jarak_tempuh;
    public $tinggi_badan, $berat_badan;

    // Akun
    public $email, $password;

    public $userId;
    public $isEdit = false;
    public $userSekolahId;

    public function mount($siswaId = null)
    {
        $this->userSekolahId = Auth::user()->sekolah_id;

        if ($siswaId) {
            $this->isEdit = true;
            $siswa = Siswa::with('user')->findOrFail($siswaId);
            $this->siswaId = $siswa->id;
            $this->userId = $siswa->user_id;
            $this->sekolah_id = $siswa->sekolah_id;
            $this->kelas_id = $siswa->kelas_id;
            $this->nisn = $siswa->nisn;
            $this->nik = $siswa->nik;
            $this->nama = $siswa->nama;
            $this->tempat_lahir = $siswa->tempat_lahir;
            $this->tanggal_lahir = $siswa->tanggal_lahir?->format('Y-m-d');
            $this->jenis_kelamin = $siswa->jenis_kelamin;
            $this->agama = $siswa->agama;
            $this->alamat = $siswa->alamat;
            $this->kode_pos = $siswa->kode_pos;
            $this->no_hp = $siswa->no_hp;
            $this->kontak_darurat = $siswa->kontak_darurat;
            $this->no_hp_ortu = $siswa->no_hp_ortu;
            $this->anak_ke = $siswa->anak_ke;
            $this->jml_saudara = $siswa->jml_saudara;
            $this->nama_ayah = $siswa->nama_ayah;
            $this->nik_ayah = $siswa->nik_ayah;
            $this->pendidikan_ayah = $siswa->pendidikan_ayah;
            $this->pekerjaan_ayah = $siswa->pekerjaan_ayah;
            $this->penghasilan_ayah = $siswa->penghasilan_ayah;
            $this->nama_ibu = $siswa->nama_ibu;
            $this->nik_ibu = $siswa->nik_ibu;
            $this->pendidikan_ibu = $siswa->pendidikan_ibu;
            $this->pekerjaan_ibu = $siswa->pekerjaan_ibu;
            $this->penghasilan_ibu = $siswa->penghasilan_ibu;
            $this->kewarganegaraan = $siswa->kewarganegaraan;
            $this->kebutuhan_khusus = $siswa->kebutuhan_khusus;
            $this->no_kip = $siswa->no_kip;
            $this->no_kk = $siswa->no_kk;
            $this->nama_kepala_keluarga = $siswa->nama_kepala_keluarga;
            $this->transportasi = $siswa->transportasi;
            $this->jarak_tempuh = $siswa->jarak_tempuh;
            $this->tinggi_badan = $siswa->tinggi_badan;
            $this->berat_badan = $siswa->berat_badan;
            $this->status = $siswa->status;
            $this->email = $siswa->user->email;
            $this->password = '';
        } else {
            $this->resetInputFields();
        }

        if ($this->userSekolahId) {
            $this->sekolah_id = $this->userSekolahId;
        }
    }

    public function render()
    {
        $sekolahs = Sekolah::all();

        $kelasQuery = Kelas::query();
        if ($this->sekolah_id) {
            $kelasQuery->where('sekolah_id', $this->sekolah_id);
        }
        $kelases = $kelasQuery->get();

        return view('livewire.emis.master-siswa-form', compact('sekolahs', 'kelases'))
            ->layout('layouts/layoutMaster')
            ->title($this->isEdit ? 'Edit Siswa' : 'Tambah Siswa');
    }

    public function resetInputFields()
    {
        $this->nama = '';
        $this->nisn = '';
        $this->nik = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = '';
        $this->jenis_kelamin = '';
        $this->agama = '';
        $this->alamat = '';
        $this->kode_pos = '';
        $this->no_hp = '';
        $this->kontak_darurat = '';
        $this->no_hp_ortu = '';
        $this->anak_ke = '';
        $this->jml_saudara = '';
        $this->nama_ayah = '';
        $this->nik_ayah = '';
        $this->pendidikan_ayah = '';
        $this->pekerjaan_ayah = '';
        $this->penghasilan_ayah = '';
        $this->nama_ibu = '';
        $this->nik_ibu = '';
        $this->pendidikan_ibu = '';
        $this->pekerjaan_ibu = '';
        $this->penghasilan_ibu = '';
        $this->kewarganegaraan = 'WNI';
        $this->kebutuhan_khusus = '';
        $this->no_kip = '';
        $this->no_kk = '';
        $this->nama_kepala_keluarga = '';
        $this->transportasi = '';
        $this->jarak_tempuh = '';
        $this->tinggi_badan = '';
        $this->berat_badan = '';
        $this->status = 'aktif';
        $this->email = '';
        $this->password = '';
        $this->kelas_id = null;
        $this->siswaId = null;
        $this->userId = null;
        $this->isEdit = false;
        if ($this->userSekolahId) {
            $this->sekolah_id = $this->userSekolahId;
        } else {
            $this->sekolah_id = null;
        }
    }

    public function save()
    {
        $validationRules = [
            'sekolah_id' => 'required|exists:sekolah,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'nisn' => 'required|string|max:50|unique:siswas,nisn,' . $this->siswaId,
            'nik' => 'nullable|string|max:50',
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'kode_pos' => 'nullable|string|max:10',
            'no_hp' => 'nullable|string|max:20',
            'kontak_darurat' => 'nullable|string|max:20',
            'no_hp_ortu' => 'nullable|string|max:20',
            'anak_ke' => 'nullable|integer|min:1',
            'jml_saudara' => 'nullable|integer|min:0',
            'nama_ayah' => 'nullable|string|max:255',
            'nik_ayah' => 'nullable|string|max:50',
            'pendidikan_ayah' => 'nullable|string|max:100',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'penghasilan_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'nullable|string|max:255',
            'nik_ibu' => 'nullable|string|max:50',
            'pendidikan_ibu' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'penghasilan_ibu' => 'nullable|string|max:100',
            'kewarganegaraan' => 'nullable|string|max:50',
            'kebutuhan_khusus' => 'nullable|string|max:100',
            'no_kip' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'nama_kepala_keluarga' => 'nullable|string|max:255',
            'transportasi' => 'nullable|string|max:100',
            'jarak_tempuh' => 'nullable|string|max:100',
            'tinggi_badan' => 'nullable|integer|min:1|max:300',
            'berat_badan' => 'nullable|integer|min:1|max:300',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
            'email' => 'required|email|max:255|unique:users,email,' . $this->userId,
        ];

        $validationRules['password'] = 'nullable|string|min:8';

        $this->validate($validationRules);

        DB::transaction(function () {
            $userData = [
                'name' => $this->nama,
                'email' => $this->email,
                'username' => $this->nisn,
                'sekolah_id' => $this->sekolah_id,
            ];
            if ($this->password) {
                $userData['password'] = bcrypt($this->password);
            } elseif (!$this->userId) {
                $userData['password'] = bcrypt($this->nisn);
            }

            $user = User::updateOrCreate(['id' => $this->userId], $userData);
            $user->syncRoles(['Siswa']);

            $siswa = Siswa::updateOrCreate(
                ['id' => $this->siswaId],
                [
                    'user_id' => $user->id,
                    'sekolah_id' => $this->sekolah_id,
                    'kelas_id' => $this->kelas_id,
                    'nisn' => $this->nisn,
                    'nik' => $this->nik,
                    'nama' => $this->nama,
                    'tempat_lahir' => $this->tempat_lahir,
                    'tanggal_lahir' => $this->tanggal_lahir,
                    'jenis_kelamin' => $this->jenis_kelamin,
                    'agama' => $this->agama,
                    'alamat' => $this->alamat,
                    'kode_pos' => $this->kode_pos,
                    'no_hp' => $this->no_hp,
                    'kontak_darurat' => $this->kontak_darurat,
                    'no_hp_ortu' => $this->no_hp_ortu,
                    'anak_ke' => $this->anak_ke ?: null,
                    'jml_saudara' => $this->jml_saudara ?: null,
                    'nama_ayah' => $this->nama_ayah,
                    'nik_ayah' => $this->nik_ayah,
                    'pendidikan_ayah' => $this->pendidikan_ayah,
                    'pekerjaan_ayah' => $this->pekerjaan_ayah,
                    'penghasilan_ayah' => $this->penghasilan_ayah,
                    'nama_ibu' => $this->nama_ibu,
                    'nik_ibu' => $this->nik_ibu,
                    'pendidikan_ibu' => $this->pendidikan_ibu,
                    'pekerjaan_ibu' => $this->pekerjaan_ibu,
                    'penghasilan_ibu' => $this->penghasilan_ibu,
                    'kewarganegaraan' => $this->kewarganegaraan,
                    'kebutuhan_khusus' => $this->kebutuhan_khusus,
                    'no_kip' => $this->no_kip,
                    'no_kk' => $this->no_kk,
                    'nama_kepala_keluarga' => $this->nama_kepala_keluarga,
                    'transportasi' => $this->transportasi,
                    'jarak_tempuh' => $this->jarak_tempuh ?: null,
                    'tinggi_badan' => $this->tinggi_badan ?: null,
                    'berat_badan' => $this->berat_badan ?: null,
                    'status' => $this->status,
                ]
            );
        });

        session()->flash('message', $this->isEdit
            ? 'Siswa berhasil diperbarui.'
            : 'Siswa berhasil ditambahkan.');

        return redirect()->route('admin.master.siswa');
    }

    public function cancel()
    {
        return redirect()->route('admin.master.siswa');
    }
}
