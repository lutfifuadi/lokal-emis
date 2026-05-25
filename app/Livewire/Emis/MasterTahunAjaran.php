<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TahunAjaran;
use App\Models\Sekolah;

class MasterTahunAjaran extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sekolah_id, $tahun, $semester, $is_active = false;
    public $tahunAjaranId;
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
        $query = TahunAjaran::with('sekolah');

        if ($this->userSekolahId) {
            $query->where('sekolah_id', $this->userSekolahId);
        }

        $tahunAjarans = $query->where(function($q) {
            $q->where('tahun', 'like', '%' . $this->search . '%')
              ->orWhere('semester', 'like', '%' . $this->search . '%');
        })->orderBy('tahun', 'desc')->paginate(10);

        $sekolahs = Sekolah::all();

        return view('livewire.emis.master-tahun-ajaran', compact('tahunAjarans', 'sekolahs'));
    }

    public function resetInputFields()
    {
        $this->tahun = '';
        $this->semester = 'Ganjil';
        $this->is_active = false;
        $this->tahunAjaranId = null;
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
            'tahun' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'boolean',
        ];

        $validatedData = $this->validate($validationRules);

        // If this tahun ajaran is set to active, deactivate all other tahun ajaran for the same school
        if ($this->is_active) {
            TahunAjaran::where('sekolah_id', $this->sekolah_id)->update(['is_active' => false]);
        }

        TahunAjaran::updateOrCreate(
            ['id' => $this->tahunAjaranId],
            [
                'sekolah_id' => $this->sekolah_id,
                'tahun' => $this->tahun,
                'semester' => $this->semester,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('message', $this->isEdit ? 'Tahun Ajaran berhasil diperbarui.' : 'Tahun Ajaran berhasil ditambahkan.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $ta = TahunAjaran::findOrFail($id);
        $this->tahunAjaranId = $id;
        $this->sekolah_id = $ta->sekolah_id;
        $this->tahun = $ta->tahun;
        $this->semester = $ta->semester;
        $this->is_active = (bool)$ta->is_active;
        $this->isEdit = true;
        
        $this->openModal();
    }

    public function toggleActive($id)
    {
        $ta = TahunAjaran::findOrFail($id);
        
        // Deactivate all others for same school
        TahunAjaran::where('sekolah_id', $ta->sekolah_id)->update(['is_active' => false]);
        
        $ta->is_active = !$ta->is_active;
        $ta->save();
        
        session()->flash('message', 'Status aktif Tahun Ajaran berhasil diubah.');
    }

    public function delete($id)
    {
        TahunAjaran::find($id)->delete();
        session()->flash('message', 'Tahun Ajaran berhasil dihapus.');
    }

    public function resetAll()
    {
        if (TahunAjaran::count() === 0) {
            session()->flash('message', 'Tidak ada data tahun ajaran untuk dihapus.');
            return;
        }
        TahunAjaran::query()->delete();
        session()->flash('message', 'Semua data tahun ajaran berhasil dihapus.');
    }
}
