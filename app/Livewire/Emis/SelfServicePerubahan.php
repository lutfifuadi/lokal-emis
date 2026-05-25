<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\PerubahanData;
use Illuminate\Support\Facades\Auth;

class SelfServicePerubahan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $siswaId;
    public $field, $old_value, $new_value;
    public $isOpen = false;
    public $errorMessage;

    public function mount()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        // Fallback for Orang Tua role
        if (!$siswa && $user->hasRole('Orang Tua')) {
            $siswa = Siswa::first();
        }

        if ($siswa) {
            $this->siswaId = $siswa->id;
        } else {
            $this->errorMessage = 'Data siswa tidak ditemukan untuk akun Anda.';
        }
    }

    public function updatedField($value)
    {
        if (!$this->siswaId || !$value) {
            $this->old_value = '';
            return;
        }

        $siswa = Siswa::findOrFail($this->siswaId);
        $this->old_value = $siswa->$value ?? '';
    }

    public function render()
    {
        $history = collect();
        if ($this->siswaId) {
            $history = PerubahanData::where('siswa_id', $this->siswaId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('livewire.emis.self-service-perubahan', compact('history'));
    }

    public function resetInputFields()
    {
        $this->field = '';
        $this->old_value = '';
        $this->new_value = '';
    }

    public function openModal()
    {
        if (!$this->siswaId) {
            session()->flash('error', 'Tidak dapat mengajukan perubahan karena data siswa tidak ditemukan.');
            return;
        }
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
        $this->validate([
            'field' => 'required|in:nik,nama,alamat,no_hp,kontak_darurat',
            'new_value' => 'required|string',
        ]);

        // Verify if there is already a pending request for the exact same field
        $existing = PerubahanData::where('siswa_id', $this->siswaId)
            ->where('field', $this->field)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            session()->flash('error', 'Sudah ada usulan perubahan yang berstatus pending untuk kolom ini.');
            $this->closeModal();
            return;
        }

        PerubahanData::create([
            'user_id' => Auth::id(),
            'siswa_id' => $this->siswaId,
            'field' => $this->field,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'status' => 'pending',
        ]);

        session()->flash('message', 'Usulan perubahan data berhasil diajukan dan menunggu persetujuan operator.');

        $this->closeModal();
    }
}
