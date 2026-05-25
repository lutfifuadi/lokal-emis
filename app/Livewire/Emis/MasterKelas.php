<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use App\Models\Sekolah;

class MasterKelas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sekolah_id, $jurusan_id, $nama, $tingkat, $tahun_ajaran_id;
    public $kelasId;
    public $isEdit = false;
    public $isOpen = false;
    public $userSekolahId;

    public function mount()
    {
        $this->userSekolahId = activeSekolahId();
        if ($this->userSekolahId) {
            $this->sekolah_id = $this->userSekolahId;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Kelas::with(['sekolah', 'jurusan', 'tahunAjaran']);

        if ($this->userSekolahId) {
            $query->where('sekolah_id', $this->userSekolahId);
        }

        $kelas = $query->where(function($q) {
            $q->where('nama', 'like', '%' . $this->search . '%')
              ->orWhere('tingkat', 'like', '%' . $this->search . '%');
        })->paginate(10);

        // Fetch options filtered by selected school (or all if Super Admin has not selected a school yet)
        $sekolahs = Sekolah::all();
        
        $jurusansQuery = Jurusan::query();
        $tahunAjaransQuery = TahunAjaran::query();

        if ($this->sekolah_id) {
            $jurusansQuery->where('sekolah_id', $this->sekolah_id);
            $tahunAjaransQuery->where('sekolah_id', $this->sekolah_id);
        }

        $jurusans = $jurusansQuery->get();
        $tahunAjarans = $tahunAjaransQuery->get();

        return view('livewire.emis.master-kelas', compact('kelas', 'sekolahs', 'jurusans', 'tahunAjarans'));
    }

    public function resetInputFields()
    {
        $this->nama = '';
        $this->tingkat = '';
        $this->jurusan_id = null;
        $this->tahun_ajaran_id = null;
        $this->kelasId = null;
        $this->isEdit = false;
        if ($this->userSekolahId) {
            $this->sekolah_id = $this->userSekolahId;
        } else {
            $this->sekolah_id = null;
        }
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $validationRules = [
            'sekolah_id' => 'required|exists:sekolah,id',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'nama' => 'required|string|max:255',
            'tingkat' => 'required|integer|min:1|max:20',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
        ];

        $validatedData = $this->validate($validationRules);

        Kelas::updateOrCreate(['id' => $this->kelasId], $validatedData);

        session()->flash('message', $this->isEdit ? 'Kelas berhasil diperbarui.' : 'Kelas berhasil ditambahkan.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $kls = Kelas::findOrFail($id);
        $this->kelasId = $id;
        $this->sekolah_id = $kls->sekolah_id;
        $this->jurusan_id = $kls->jurusan_id;
        $this->nama = $kls->nama;
        $this->tingkat = $kls->tingkat;
        $this->tahun_ajaran_id = $kls->tahun_ajaran_id;
        $this->isEdit = true;
        
        $this->openModal();
    }

    public function delete($id)
    {
        Kelas::find($id)->delete();
        session()->flash('message', 'Kelas berhasil dihapus.');
    }

    public function resetAll()
    {
        if (Kelas::count() === 0) {
            session()->flash('message', 'Tidak ada data kelas untuk dihapus.');
            return;
        }
        Kelas::query()->delete();
        session()->flash('message', 'Semua data kelas berhasil dihapus.');
    }
}
