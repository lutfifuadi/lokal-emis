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

  <!-- Filters & Search -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <!-- Status Filter Buttons -->
    <div class="btn-group" role="group" aria-label="Filter Status">
      <button type="button" class="btn btn-outline-primary {{ $statusFilter === 'pending' ? 'active' : '' }}" wire:click="$set('statusFilter', 'pending')">
        <i class="icon-base ti tabler-hourglass-low me-1"></i> Pending
      </button>
      <button type="button" class="btn btn-outline-primary {{ $statusFilter === 'approved' ? 'active' : '' }}" wire:click="$set('statusFilter', 'approved')">
        <i class="icon-base ti tabler-circle-check me-1"></i> Disetujui
      </button>
      <button type="button" class="btn btn-outline-primary {{ $statusFilter === 'rejected' ? 'active' : '' }}" wire:click="$set('statusFilter', 'rejected')">
        <i class="icon-base ti tabler-circle-x me-1"></i> Ditolak
      </button>
    </div>

    <!-- Search Input -->
    <div style="width: 300px;">
      <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
        <input type="text" class="form-control" placeholder="Cari nama siswa or NISN..." wire:model.live="search">
      </div>
    </div>
  </div>

  <!-- Queue Table -->
  <div class="table-responsive">
    <table class="table table-hover table-striped align-middle">
      <thead>
        <tr>
          <th>Siswa</th>
          @if(!$userSekolahId)
            <th>Sekolah</th>
          @endif
          <th>Kolom Data</th>
          <th>Nilai Lama</th>
          <th>Nilai Baru</th>
          <th>Tanggal Diajukan</th>
          @if($statusFilter !== 'pending')
            <th>Ditinjau Oleh</th>
            @if($statusFilter === 'rejected')
              <th>Catatan Penolakan</th>
            @endif
          @else
            <th class="text-center" style="width: 200px;">Aksi</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @forelse($requests as $item)
          <tr>
            <td>
              <div class="d-flex flex-column">
                <span class="fw-bold text-heading">{{ $item->siswa->nama ?? 'N/A' }}</span>
                <small class="text-muted">NISN: {{ $item->siswa->nisn ?? 'N/A' }}</small>
              </div>
            </td>
            @if(!$userSekolahId)
              <td>{{ $item->siswa->sekolah->nama ?? '-' }}</td>
            @endif
            <td>
              @php
                $fields = [
                  'nik' => 'NIK',
                  'nama' => 'Nama Lengkap',
                  'alamat' => 'Alamat',
                  'no_hp' => 'No. HP',
                  'kontak_darurat' => 'Kontak Darurat (Orang Tua)',
                ];
              @endphp
              <span class="badge bg-label-info">{{ $fields[$item->field] ?? $item->field }}</span>
            </td>
            <td>
              <span class="text-muted text-decoration-line-through">{{ $item->old_value ?? '(Kosong)' }}</span>
            </td>
            <td>
              <span class="text-success fw-bold">{{ $item->new_value ?? '(Kosong)' }}</span>
            </td>
            <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
            
            @if($statusFilter !== 'pending')
              <td>
                <div class="d-flex flex-column">
                  <span>{{ \App\Models\User::find($item->reviewed_by)?->name ?? 'System' }}</span>
                  <small class="text-muted">{{ $item->reviewed_at?->format('d M Y, H:i') }}</small>
                </div>
              </td>
              @if($statusFilter === 'rejected')
                <td>
                  <span class="text-danger small"><i class="icon-base ti tabler-x me-1"></i> {{ $item->rejected_reason }}</span>
                </td>
              @endif
            @else
              <td class="text-center">
                <button onclick="confirm('Apakah Anda yakin ingin menyetujui perubahan data ini?') || event.stopImmediatePropagation()" wire:click="approve({{ $item->id }})" class="btn btn-sm btn-success me-1">
                  <i class="icon-base ti tabler-check me-1 fs-6"></i> Setuju
                </button>
                <button wire:click="openModal({{ $item->id }})" class="btn btn-sm btn-danger">
                  <i class="icon-base ti tabler-x me-1 fs-6"></i> Tolak
                </button>
              </td>
            @endif
          </tr>
        @empty
          <tr>
            <td colspan="{{ $userSekolahId ? ($statusFilter === 'rejected' ? 7 : 6) : ($statusFilter === 'rejected' ? 8 : 7) }}" class="text-center py-5 text-muted">
              <i class="icon-base ti tabler-clipboard-check fs-1 d-block mb-2 opacity-50"></i>
              Tidak ada antrian pengajuan data untuk status ini.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $requests->links() }}
  </div>

  <!-- Rejection Modal -->
  @if($isOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">Tolak Usulan Perubahan</h5>
            <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
          </div>
          <form wire:submit.prevent="reject">
            <div class="modal-body">
              <div class="mb-4">
                <label for="rejectionReason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                <textarea class="form-control @error('rejectionReason') is-invalid @enderror" id="rejectionReason" wire:model="rejectionReason" rows="4" placeholder="Masukkan alasan penolakan secara jelas..."></textarea>
                @error('rejectionReason') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">Batal</button>
              <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>
