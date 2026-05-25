<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jurusan;
use App\Models\Sekolah;

class MasterJurusan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sekolah_id, $nama, $kode;
    public $jurusanId;
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
        $query = Jurusan::with('sekolah');

        if ($this->userSekolahId) {
            $query->where('sekolah_id', $this->userSekolahId);
        }

        $jurusans = $query->where(function($q) {
            $q->where('nama', 'like', '%' . $this->search . '%')
              ->orWhere('kode', 'like', '%' . $this->search . '%');
        })->paginate(10);

        $sekolahs = Sekolah::all();

        return view('livewire.emis.master-jurusan', compact('jurusans', 'sekolahs'));
    }

    public function resetInputFields()
    {
        $this->nama = '';
        $this->kode = '';
        $this->jurusanId = null;
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
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50',
        ];

        $validatedData = $this->validate($validationRules);

        Jurusan::updateOrCreate(['id' => $this->jurusanId], $validatedData);

        session()->flash('message', $this->isEdit ? 'Jurusan berhasil diperbarui.' : 'Jurusan berhasil ditambahkan.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $this->jurusanId = $id;
        $this->sekolah_id = $jurusan->sekolah_id;
        $this->nama = $jurusan->nama;
        $this->kode = $jurusan->kode;
        $this->isEdit = true;
        
        $this->openModal();
    }

    public function delete($id)
    {
        Jurusan::find($id)->delete();
        session()->flash('message', 'Jurusan berhasil dihapus.');
    }

    public function resetAll()
    {
        if (Jurusan::count() === 0) {
            session()->flash('message', 'Tidak ada data jurusan untuk dihapus.');
            return;
        }
        Jurusan::query()->delete();
        session()->flash('message', 'Semua data jurusan berhasil dihapus.');
    }
}
