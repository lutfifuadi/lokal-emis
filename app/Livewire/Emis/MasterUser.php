<?php

namespace App\Livewire\Emis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Guru;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class MasterUser extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $name, $email, $password, $sekolah_id, $role;
    public $nik, $nuptk; // For Guru role
    public $userId;
    public $isEdit = false;
    public $isOpen = false;
    public $userSekolahId;
    public $currentUserRole;

    public function mount()
    {
        $this->userSekolahId = activeSekolahId();
        $this->currentUserRole = Auth::user()->roles->first()?->name;
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
        $query = User::with(['sekolah', 'roles']);

        // Filter users based on logged-in user school
        if ($this->userSekolahId) {
            $query->where('sekolah_id', $this->userSekolahId);
        }

        // Limit to administrative/staff roles
        $query->whereHas('roles', function($q) {
            $q->whereIn('name', ['Super Admin', 'Dinas', 'Operator', 'Kepala Sekolah', 'Guru', 'Siswa', 'Orang Tua']);
        });

        $users = $query->where(function($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('email', 'like', '%' . $this->search . '%')
              ->orWhere('username', 'like', '%' . $this->search . '%');
        })->paginate(10);

        $sekolahs = Sekolah::all();

        // Get available roles to assign
        $availableRoles = ['Operator', 'Guru', 'Kepala Sekolah', 'Siswa', 'Orang Tua'];
        if ($this->currentUserRole === 'Super Admin') {
            $availableRoles[] = 'Dinas';
            $availableRoles[] = 'Super Admin';
        }

        return view('livewire.emis.master-user', compact('users', 'sekolahs', 'availableRoles'));
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->nik = '';
        $this->nuptk = '';
        $this->userId = null;
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->userId,
            'role' => 'required|string|in:Super Admin,Dinas,Operator,Kepala Sekolah,Guru,Siswa,Orang Tua',
            'sekolah_id' => 'required_if:role,Operator,Kepala Sekolah,Guru,Siswa,Orang Tua|nullable|exists:sekolah,id',
        ];

        if ($this->role === 'Guru') {
            $validationRules['nik'] = 'nullable|string|max:255';
            $validationRules['nuptk'] = 'nullable|string|max:255';
        }

        if ($this->isEdit) {
            $validationRules['password'] = 'nullable|string|min:8';
        } else {
            $validationRules['password'] = 'required|string|min:8';
        }

        $validatedData = $this->validate($validationRules);

        // Prepare user data
        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'sekolah_id' => in_array($this->role, ['Dinas', 'Super Admin']) ? null : $this->sekolah_id,
        ];
        if ($this->password) {
            $userData['password'] = bcrypt($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $userData);
        $user->syncRoles([$this->role]);

        if ($this->role === 'Guru') {
            Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'sekolah_id' => $this->sekolah_id,
                    'nik' => $this->nik,
                    'nuptk' => $this->nuptk,
                    'nama' => $this->name,
                ]
            );
        } else {
            // Delete Guru record if it exists
            Guru::where('user_id', $user->id)->delete();
        }

        session()->flash('message', $this->isEdit ? 'Pengguna berhasil diperbarui.' : 'Pengguna berhasil ditambahkan.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->sekolah_id = $user->sekolah_id;
        $this->role = $user->roles->first()?->name;
        $this->password = '';

        if ($this->role === 'Guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            $this->nik = $guru?->nik;
            $this->nuptk = $guru?->nuptk;
        } else {
            $this->nik = '';
            $this->nuptk = '';
        }

        $this->isEdit = true;
        $this->openModal();
    }

    public function delete($id)
    {
        // Don't allow self deletion
        if (Auth::id() == $id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        User::find($id)->delete();
        session()->flash('message', 'Pengguna berhasil dihapus.');
    }

    public function resetAll()
    {
        $query = User::query();

        if ($this->userSekolahId) {
            $query->where('sekolah_id', $this->userSekolahId);
        }

        $count = $query->where('id', '!=', Auth::id())->count();
        if ($count === 0) {
            session()->flash('message', 'Tidak ada data pengguna untuk dihapus.');
            return;
        }

        $query->where('id', '!=', Auth::id())->delete();
        session()->flash('message', 'Semua data pengguna berhasil dihapus.');
    }

    public function impersonate($id)
    {
        // Tidak bisa impersonate diri sendiri
        if (Auth::id() == $id) {
            session()->flash('error', 'Anda tidak dapat login sebagai diri sendiri.');
            return;
        }

        $targetUser = User::findOrFail($id);
        $targetRole = $targetUser->roles->first()?->name;

        // Hanya boleh impersonate role yang lebih rendah (bukan admin level)
        $protectedRoles = ['Super Admin', 'Dinas', 'Operator'];
        if (in_array($targetRole, $protectedRoles)) {
            session()->flash('error', 'Anda tidak memiliki izin untuk login sebagai pengguna dengan role tersebut.');
            return;
        }

        // Simpan ID admin asli ke session
        session(['impersonated_by' => Auth::id()]);

        // Login sebagai user target
        Auth::loginUsingId($id);

        // Redirect ke dashboard (akan diarahkan sesuai role)
        return redirect('/');
    }
}
