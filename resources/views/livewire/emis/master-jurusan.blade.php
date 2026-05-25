<div>
  @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
      <i class="icon-base ti tabler-circle-check me-2"></i>
      {{ session('message') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
      <button wire:click="create" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-1"></i> Tambah Jurusan
      </button>
      <button onclick="return confirm('Yakin hapus semua data jurusan?')" wire:click="resetAll" class="btn btn-outline-danger">
        <i class="icon-base ti tabler-trash me-1"></i> Reset
      </button>
    </div>
    <div style="width: 300px;">
      <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
        <input type="text" class="form-control" placeholder="Cari jurusan atau kode..." wire:model.live="search">
      </div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-striped align-middle">
      <thead>
        <tr>
          @if(!$userSekolahId)
            <th>Sekolah</th>
          @endif
          <th>Kode Jurusan</th>
          <th>Nama Jurusan</th>
          <th class="text-center" style="width: 150px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($jurusans as $jurusan)
          <tr>
            @if(!$userSekolahId)
              <td>{{ $jurusan->sekolah->nama }}</td>
            @endif
            <td class="fw-bold text-heading">{{ $jurusan->kode }}</td>
            <td>{{ $jurusan->nama }}</td>
            <td class="text-center">
              <button wire:click="edit({{ $jurusan->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1" title="Edit">
                <i class="icon-base ti tabler-edit text-primary fs-5"></i>
              </button>
              <button onclick="confirm('Apakah Anda yakin ingin menghapus jurusan ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $jurusan->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" title="Hapus">
                <i class="icon-base ti tabler-trash text-danger fs-5"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ $userSekolahId ? 3 : 4 }}" class="text-center py-5 text-muted">
              <i class="icon-base ti tabler-git-branch fs-1 d-block mb-2 opacity-50"></i>
              Tidak ada data jurusan ditemukan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $jurusans->links() }}
  </div>

  <!-- Modal -->
  @if($isOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">{{ $isEdit ? 'Edit Jurusan' : 'Tambah Jurusan' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
          </div>
          <form wire:submit.prevent="store">
            <div class="modal-body">
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

              <div class="mb-4">
                <label for="kode" class="form-label">Kode Jurusan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode" wire:model="kode" placeholder="Contoh: RPL">
                @error('kode') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-4">
                <label for="nama" class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" wire:model="nama" placeholder="Contoh: Rekayasa Perangkat Lunak">
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">Batal</button>
              <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Jurusan' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>
