<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sekolah;

class MasterSekolah extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $npsn, $nsm, $nama, $alamat, $kontak, $email, $website, $nama_kepala, $nip_kepala, $jenis_sekolah, $status_sekolah, $jenjang;
    public $sekolahId;
    public $isEdit = false;
    public $isOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sekolahs = Sekolah::where(function($query) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('npsn', 'like', '%' . $this->search . '%')
                  ->orWhere('nsm', 'like', '%' . $this->search . '%');
        })->paginate(10);

        return view('livewire.emis.master-sekolah', compact('sekolahs'));
    }

    public function resetInputFields()
    {
        $this->npsn = '';
        $this->nsm = '';
        $this->nama = '';
        $this->alamat = '';
        $this->kontak = '';
        $this->email = '';
        $this->website = '';
        $this->nama_kepala = '';
        $this->nip_kepala = '';
        $this->jenis_sekolah = '';
        $this->status_sekolah = '';
        $this->jenjang = '';
        $this->sekolahId = null;
        $this->isEdit = false;
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
            'npsn' => 'required|numeric|digits:8|unique:sekolah,npsn' . ($this->isEdit ? ',' . $this->sekolahId : ''),
            'nsm' => 'nullable|string|max:255',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'nama_kepala' => 'nullable|string|max:255',
            'nip_kepala' => 'nullable|string|max:255',
            'jenis_sekolah' => 'nullable|string|max:255',
            'status_sekolah' => 'nullable|string|max:255',
            'jenjang' => 'nullable|string|max:255',
        ];

        $validatedData = $this->validate($validationRules);

        Sekolah::updateOrCreate(['id' => $this->sekolahId], $validatedData);

        session()->flash('message', $this->isEdit ? 'Sekolah berhasil diperbarui.' : 'Sekolah berhasil ditambahkan.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $this->sekolahId = $id;
        $this->npsn = $sekolah->npsn;
        $this->nsm = $sekolah->nsm;
        $this->nama = $sekolah->nama;
        $this->alamat = $sekolah->alamat;
        $this->kontak = $sekolah->kontak;
        $this->email = $sekolah->email;
        $this->website = $sekolah->website;
        $this->nama_kepala = $sekolah->nama_kepala;
        $this->nip_kepala = $sekolah->nip_kepala;
        $this->jenis_sekolah = $sekolah->jenis_sekolah;
        $this->status_sekolah = $sekolah->status_sekolah;
        $this->jenjang = $sekolah->jenjang;
        $this->isEdit = true;
        
        $this->openModal();
    }

    public function delete($id)
    {
        Sekolah::find($id)->delete();
        session()->flash('message', 'Sekolah berhasil dihapus.');
    }

    public function resetAll()
    {
        if (Sekolah::count() === 0) {
            session()->flash('message', 'Tidak ada data sekolah untuk dihapus.');
            return;
        }
        Sekolah::query()->delete();
        session()->flash('message', 'Semua data sekolah berhasil dihapus.');
    }
}
