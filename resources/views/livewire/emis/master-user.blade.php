<div>
  @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
      <i class="icon-base ti tabler-circle-check me-2"></i>
      {{ session('message') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
      <i class="icon-base ti tabler-alert-triangle me-2"></i>
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
      <button wire:click="create" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-1"></i> Tambah Pengguna
      </button>
      <button onclick="return confirm('Yakin hapus semua data pengguna?')" wire:click="resetAll" class="btn btn-outline-danger">
        <i class="icon-base ti tabler-trash me-1"></i> Reset
      </button>
    </div>
    <div style="width: 300px;">
      <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
        <input type="text" class="form-control" placeholder="Cari nama, email, atau username..." wire:model.live="search">
      </div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-striped align-middle">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th class="text-center" style="width: 180px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr>
            <td class="fw-bold text-heading">{{ $user->name }}</td>
            <td><code>{{ $user->username ?? '-' }}</code></td>
            <td>{{ $user->email }}</td>
            <td>
              @php
                $roleName = $user->roles->first()?->name ?? '-';
                $badgeClass = 'bg-secondary';
                if ($roleName === 'Super Admin') $badgeClass = 'bg-danger';
                elseif ($roleName === 'Dinas') $badgeClass = 'bg-info';
                elseif ($roleName === 'Operator') $badgeClass = 'bg-primary';
                elseif ($roleName === 'Kepala Sekolah') $badgeClass = 'bg-warning';
                elseif ($roleName === 'Guru') $badgeClass = 'bg-success';
              @endphp
              <span class="badge {{ $badgeClass }}">{{ $roleName }}</span>
            </td>
            <td class="text-center">
              <button wire:click="edit({{ $user->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1" title="Edit">
                <i class="icon-base ti tabler-edit text-primary fs-5"></i>
              </button>
              @if(in_array($currentUserRole, ['Super Admin', 'Dinas', 'Operator']) && auth()->id() !== $user->id && !in_array($user->roles->first()?->name, ['Super Admin', 'Dinas', 'Operator']))
                <button wire:click="impersonate({{ $user->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1"
                  title="Login Sebagai {{ $user->name }}" data-bs-toggle="tooltip" data-bs-placement="top">
                  <i class="icon-base ti tabler-user-shield text-warning fs-5"></i>
                </button>
              @endif
              <button onclick="confirm('Apakah Anda yakin ingin menghapus pengguna ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $user->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" title="Hapus">
                <i class="icon-base ti tabler-trash text-danger fs-5"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="icon-base ti tabler-users fs-1 d-block mb-2 opacity-50"></i>
              Tidak ada data pengguna ditemukan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $users->links() }}
  </div>

  <!-- Modal -->
  @if($isOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">{{ $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
          </div>
          <form wire:submit.prevent="store">
            <div class="modal-body">
              <div class="mb-4">
                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" placeholder="Contoh: Budi Utomo">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-4">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" placeholder="Contoh: budi@gmail.com">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-4">
                <label for="password" class="form-label">Password {!! !$isEdit ? '<span class="text-danger">*</span>' : '<small class="text-muted">(Kosongkan jika tidak diubah)</small>' !!}</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model="password" placeholder="Minimal 8 karakter">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-4">
                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" wire:model.live="role">
                  <option value="">Pilih Role...</option>
                  @foreach($availableRoles as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                  @endforeach
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              @if(in_array($role, ['Operator', 'Guru', 'Kepala Sekolah', 'Siswa', 'Orang Tua']))
                @if(!$userSekolahId)
                  <div class="mb-4">
                    <label for="sekolah_id" class="form-label">Sekolah <span class="text-danger">*</span></label>
                    <select class="form-select @error('sekolah_id') is-invalid @enderror" id="sekolah_id" wire:model="sekolah_id">
                      <option value="">Pilih Sekolah...</option>
                      @foreach($sekolahs as $sch)
                        <option value="{{ $sch->id }}">{{ $sch->nama }}</option>
                      @endforeach
                    </select>
                    @error('sekolah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                @endif
              @endif

              @if($role === 'Guru')
                <div class="border p-3 rounded mb-4 bg-light">
                  <h6 class="fw-bold mb-3"><i class="icon-base ti tabler-id me-1"></i> Data Tambahan Guru</h6>
                  <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" wire:model="nik" placeholder="16 digit NIK">
                    @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div>
                    <label for="nuptk" class="form-label">NUPTK</label>
                    <input type="text" class="form-control @error('nuptk') is-invalid @enderror" id="nuptk" wire:model="nuptk" placeholder="16 digit NUPTK">
                    @error('nuptk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
              @endif
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">Batal</button>
              <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Pengguna' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>
