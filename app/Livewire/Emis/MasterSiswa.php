<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Kelas;

class MasterSiswa extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $userSekolahId;

    public function mount()
    {
        $this->userSekolahId = activeSekolahId();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Siswa::with(['sekolah', 'kelas', 'user']);

        if ($this->userSekolahId) {
            $query->where('sekolah_id', $this->userSekolahId);
        }

        $siswas = $query->orderBy('nama', 'asc')->where(function($q) {
            $q->where('nama', 'like', '%' . $this->search . '%')
              ->orWhere('nisn', 'like', '%' . $this->search . '%');
        })->paginate(10);

        $sekolahs = Sekolah::all();

        $kelasQuery = Kelas::query();
        if ($this->userSekolahId) {
            $kelasQuery->where('sekolah_id', $this->userSekolahId);
        }
        $kelases = $kelasQuery->get();

        return view('livewire.emis.master-siswa', compact('siswas', 'sekolahs', 'kelases'));
    }

    public function delete($id)
    {
        $siswa = Siswa::findOrFail($id);
        User::find($siswa->user_id)->delete();
        session()->flash('message', 'Siswa berhasil dihapus.');
    }

    public function resetAll()
    {
        if (Siswa::count() === 0) {
            session()->flash('message', 'Tidak ada data siswa untuk dihapus.');
            return;
        }
        $siswas = Siswa::all();
        foreach ($siswas as $siswa) {
            User::find($siswa->user_id)?->delete();
        }
        session()->flash('message', 'Semua data siswa berhasil dihapus.');
    }
}
