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
        <i class="icon-base ti tabler-plus me-1"></i> Tambah Sekolah
      </button>
      <button onclick="return confirm('Yakin hapus semua data sekolah?')" wire:click="resetAll" class="btn btn-outline-danger">
        <i class="icon-base ti tabler-trash me-1"></i> Reset
      </button>
      @php $syncSetting = \App\Models\GoogleSheetSetting::where('entity', 'sekolah')->first(); @endphp
      @if($syncSetting && $syncSetting->is_active)
        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#syncModal-sekolah">
          <i class="icon-base ti tabler-refresh me-1"></i> Sync Google Sheet
        </button>
      @endif
    </div>
    <div style="width: 300px;">
      <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
        <input type="text" class="form-control" placeholder="Cari NPSN atau nama sekolah..." wire:model.live="search">
      </div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-striped align-middle">
      <thead>
        <tr>
          <th>NPSN</th>
          <th>NSM</th>
          <th>Nama Sekolah</th>
          <th>Alamat</th>
          <th>Kontak</th>
          <th>Email</th>
          <th>Website</th>
          <th>Nama Kepala Sekolah</th>
          <th>NIP Kepala Sekolah</th>
          <th>Jenis Sekolah</th>
          <th>Status Sekolah</th>
          <th>Jenjang</th>
          <th class="text-center" style="width: 150px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sekolahs as $sekolah)
          <tr>
            <td class="fw-bold text-heading">{{ $sekolah->npsn }}</td>
            <td>{{ $sekolah->nsm ?: '-' }}</td>
            <td>{{ $sekolah->nama }}</td>
            <td>{{ $sekolah->alamat ?: '-' }}</td>
            <td>{{ $sekolah->kontak ?: '-' }}</td>
            <td>{{ $sekolah->email ?: '-' }}</td>
            <td>{{ $sekolah->website ?: '-' }}</td>
            <td>{{ $sekolah->nama_kepala ?: '-' }}</td>
            <td>{{ $sekolah->nip_kepala ?: '-' }}</td>
            <td>{{ $sekolah->jenis_sekolah ?: '-' }}</td>
            <td>{{ $sekolah->status_sekolah ?: '-' }}</td>
            <td>{{ $sekolah->jenjang ?: '-' }}</td>
            <td class="text-center">
              <button wire:click="edit({{ $sekolah->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1" title="Edit">
                <i class="icon-base ti tabler-edit text-primary fs-5"></i>
              </button>
              <button onclick="confirm('Apakah Anda yakin ingin menghapus sekolah ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $sekolah->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" title="Hapus">
                <i class="icon-base ti tabler-trash text-danger fs-5"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="13" class="text-center py-5 text-muted">
              <i class="icon-base ti tabler-school fs-1 d-block mb-2 opacity-50"></i>
              Tidak ada data sekolah ditemukan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $sekolahs->links() }}
  </div>

  <!-- Bootstrap 5 Custom Modal Backdrop & Dialog -->
  @if($isOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">{{ $isEdit ? 'Edit Sekolah' : 'Tambah Sekolah' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
          </div>
          <form wire:submit.prevent="store">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-4">
                  <label for="npsn" class="form-label">NPSN <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('npsn') is-invalid @enderror" id="npsn" wire:model="npsn" placeholder="Contoh: 20580123">
                  @error('npsn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-4">
                  <label for="nsm" class="form-label">NSM</label>
                  <input type="text" class="form-control @error('nsm') is-invalid @enderror" id="nsm" wire:model="nsm" placeholder="Contoh: 131234567890">
                  @error('nsm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              <div class="mb-4">
                <label for="nama" class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" wire:model="nama" placeholder="Contoh: MAS Abu Darrin">
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-4">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" wire:model="alamat" rows="3" placeholder="Alamat lengkap sekolah..."></textarea>
                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="row">
                <div class="col-md-6 mb-4">
                  <label for="kontak" class="form-label">Kontak / No. Telepon</label>
                  <input type="text" class="form-control @error('kontak') is-invalid @enderror" id="kontak" wire:model="kontak" placeholder="Contoh: 081234567890">
                  @error('kontak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-4">
                  <label for="email" class="form-label">Email Sekolah</label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" placeholder="Contoh: info@sekolah.sch.id">
                  @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              <div class="mb-4">
                <label for="website" class="form-label">Website</label>
                <input type="text" class="form-control @error('website') is-invalid @enderror" id="website" wire:model="website" placeholder="Contoh: https://sekolah.sch.id">
                @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="row">
                <div class="col-md-6 mb-4">
                  <label for="nama_kepala" class="form-label">Nama Kepala Sekolah</label>
                  <input type="text" class="form-control @error('nama_kepala') is-invalid @enderror" id="nama_kepala" wire:model="nama_kepala" placeholder="Nama lengkap kepala sekolah">
                  @error('nama_kepala') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-4">
                  <label for="nip_kepala" class="form-label">NIP Kepala Sekolah</label>
                  <input type="text" class="form-control @error('nip_kepala') is-invalid @enderror" id="nip_kepala" wire:model="nip_kepala" placeholder="NIP kepala sekolah">
                  @error('nip_kepala') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 mb-4">
                  <label for="jenis_sekolah" class="form-label">Jenis Sekolah</label>
                  <input type="text" class="form-control @error('jenis_sekolah') is-invalid @enderror" id="jenis_sekolah" wire:model="jenis_sekolah" placeholder="Contoh: MA, MTs">
                  @error('jenis_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-4">
                  <label for="status_sekolah" class="form-label">Status Sekolah</label>
                  <input type="text" class="form-control @error('status_sekolah') is-invalid @enderror" id="status_sekolah" wire:model="status_sekolah" placeholder="Contoh: Negeri, Swasta">
                  @error('status_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-4">
                  <label for="jenjang" class="form-label">Jenjang</label>
                  <input type="text" class="form-control @error('jenjang') is-invalid @enderror" id="jenjang" wire:model="jenjang" placeholder="Contoh: SMA, SMK, MA">
                  @error('jenjang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">Batal</button>
              <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Sekolah' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>
