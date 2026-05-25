<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PerubahanData;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalAntrian extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $statusFilter = 'pending';
    public $rejectionReason = '';
    public $selectedRequestId;
    public $isOpen = false;
    public $userSekolahId;

    public function mount()
    {
        $this->userSekolahId = activeSekolahId();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PerubahanData::with(['siswa.sekolah', 'user']);

        if ($this->userSekolahId) {
            $query->whereHas('siswa', function($q) {
                $q->where('sekolah_id', $this->userSekolahId);
            });
        }

        $query->where('status', $this->statusFilter);

        if ($this->search) {
            $query->whereHas('siswa', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.emis.approval-antrian', compact('requests'));
    }

    public function openModal($id)
    {
        $this->selectedRequestId = $id;
        $this->rejectionReason = '';
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->selectedRequestId = null;
        $this->rejectionReason = '';
        $this->resetErrorBag();
    }

    public function approve($id)
    {
        $request = PerubahanData::findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'Pengajuan ini sudah ditindaklanjuti.');
            return;
        }

        DB::transaction(function() use ($request) {
            // Update request status
            $request->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Update student record
            $siswa = Siswa::findOrFail($request->siswa_id);
            $siswa->update([
                $request->field => $request->new_value
            ]);

            // Sync user record name if updated
            if ($request->field === 'nama') {
                $siswa->user->update([
                    'name' => $request->new_value
                ]);
            }
        });

        session()->flash('message', 'Usulan perubahan data berhasil disetujui dan data siswa telah diperbarui.');
        $this->closeModal();
    }

    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|max:500',
        ]);

        $request = PerubahanData::findOrFail($this->selectedRequestId);

        if ($request->status !== 'pending') {
            session()->flash('error', 'Pengajuan ini sudah ditindaklanjuti.');
            $this->closeModal();
            return;
        }

        $request->update([
            'status' => 'rejected',
            'rejected_reason' => $this->rejectionReason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        session()->flash('message', 'Usulan perubahan data telah ditolak.');
        $this->closeModal();
    }
}
