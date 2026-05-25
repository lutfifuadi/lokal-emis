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
        <i class="icon-base ti tabler-plus me-1"></i> Tambah Tahun Ajaran
      </button>
      <button onclick="return confirm('Yakin hapus semua data tahun ajaran?')" wire:click="resetAll" class="btn btn-outline-danger">
        <i class="icon-base ti tabler-trash me-1"></i> Reset
      </button>
    </div>
    <div style="width: 300px;">
      <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
        <input type="text" class="form-control" placeholder="Cari tahun ajaran (contoh: 2025/2026)..." wire:model.live="search">
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
          <th>Tahun Ajaran</th>
          <th>Semester</th>
          <th>Status</th>
          <th class="text-center" style="width: 200px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tahunAjarans as $ta)
          <tr>
            @if(!$userSekolahId)
              <td>{{ $ta->sekolah->nama }}</td>
            @endif
            <td class="fw-bold text-heading">{{ $ta->tahun }}</td>
            <td>{{ $ta->semester }}</td>
            <td>
              @if($ta->is_active)
                <span class="badge bg-label-success">Aktif</span>
              @else
                <span class="badge bg-label-secondary">Tidak Aktif</span>
              @endif
            </td>
            <td class="text-center">
              <button wire:click="toggleActive({{ $ta->id }})" class="btn btn-sm {{ $ta->is_active ? 'btn-label-warning' : 'btn-label-success' }} me-2">
                {{ $ta->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
              </button>
              <button wire:click="edit({{ $ta->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1" title="Edit">
                <i class="icon-base ti tabler-edit text-primary fs-5"></i>
              </button>
              <button onclick="confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $ta->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" title="Hapus">
                <i class="icon-base ti tabler-trash text-danger fs-5"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ $userSekolahId ? 4 : 5 }}" class="text-center py-5 text-muted">
              <i class="icon-base ti tabler-calendar-event fs-1 d-block mb-2 opacity-50"></i>
              Tidak ada data tahun ajaran ditemukan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $tahunAjarans->links() }}
  </div>

  <!-- Modal -->
  @if($isOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">{{ $isEdit ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}</h5>
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
                <label for="tahun" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('tahun') is-invalid @enderror" id="tahun" wire:model="tahun" placeholder="Format: YYYY/YYYY (Contoh: 2025/2026)">
                @error('tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-4">
                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                <select class="form-select @error('semester') is-invalid @enderror" id="semester" wire:model="semester">
                  <option value="Ganjil">Ganjil</option>
                  <option value="Genap">Genap</option>
                </select>
                @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-4">
                <div class="form-check">
                  <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" wire:model="is_active">
                  <label class="form-check-label" for="is_active">
                    Set sebagai Tahun Ajaran Aktif (akan menonaktifkan tahun ajaran aktif lainnya di sekolah ini)
                  </label>
                  @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">Batal</button>
              <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Tahun Ajaran' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>
