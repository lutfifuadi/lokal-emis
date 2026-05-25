<div>
  @if($errorMessage)
    <div class="alert alert-danger shadow-sm" role="alert">
      <i class="icon-base ti tabler-alert-triangle me-2"></i>
      {{ $errorMessage }}
    </div>
  @else
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
      <button wire:click="create" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-1"></i> Ajukan Perubahan Data
      </button>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-striped align-middle">
        <thead>
          <tr>
            <th>Kolom Data</th>
            <th>Nilai Lama</th>
            <th>Nilai Baru</th>
            <th>Status</th>
            <th>Tanggal Pengajuan</th>
            <th>Ditinjau Oleh</th>
            <th>Catatan Penolakan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($history as $item)
            <tr>
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
                <span class="fw-bold text-heading">{{ $fields[$item->field] ?? $item->field }}</span>
              </td>
              <td>
                <span class="text-muted text-decoration-line-through">{{ $item->old_value ?? '(Kosong)' }}</span>
              </td>
              <td>
                <span class="text-success fw-bold">{{ $item->new_value ?? '(Kosong)' }}</span>
              </td>
              <td>
                @php
                  $statusClass = 'bg-warning';
                  if ($item->status === 'approved') $statusClass = 'bg-success';
                  elseif ($item->status === 'rejected') $statusClass = 'bg-danger';
                @endphp
                <span class="badge {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
              </td>
              <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
              <td>
                @if($item->reviewed_by)
                  {{ \App\Models\User::find($item->reviewed_by)?->name }}
                  <small class="d-block text-muted">{{ $item->reviewed_at?->format('d M Y, H:i') }}</small>
                @else
                  <span class="text-muted small">-</span>
                @endif
              </td>
              <td>
                @if($item->status === 'rejected' && $item->rejected_reason)
                  <span class="text-danger small"><i class="icon-base ti tabler-x me-1"></i> {{ $item->rejected_reason }}</span>
                @else
                  <span class="text-muted small">-</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="icon-base ti tabler-history fs-1 d-block mb-2 opacity-50"></i>
                Belum ada riwayat usulan perubahan data.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $history->links() }}
    </div>

    <!-- Modal -->
    @if($isOpen)
      <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
              <h5 class="modal-title fw-bold">Ajukan Perubahan Data</h5>
              <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="store">
              <div class="modal-body">
                <div class="mb-4">
                  <label for="field" class="form-label">Kolom Data yang Ingin Diubah <span class="text-danger">*</span></label>
                  <select class="form-select @error('field') is-invalid @enderror" id="field" wire:model.live="field">
                    <option value="">Pilih Kolom...</option>
                    <option value="nama">Nama Lengkap</option>
                    <option value="nik">NIK</option>
                    <option value="alamat">Alamat Lengkap</option>
                    <option value="no_hp">No. HP</option>
                    <option value="kontak_darurat">Kontak Darurat (Orang Tua)</option>
                  </select>
                  @error('field') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                  <label class="form-label text-muted">Nilai Saat Ini</label>
                  <input type="text" class="form-control bg-light" value="{{ $old_value }}" readonly placeholder="(Kosong)">
                </div>

                <div class="mb-4">
                  <label for="new_value" class="form-label">Nilai Baru <span class="text-danger">*</span></label>
                  @if($field === 'alamat')
                    <textarea class="form-control @error('new_value') is-invalid @enderror" id="new_value" wire:model="new_value" rows="3" placeholder="Masukkan alamat lengkap baru..."></textarea>
                  @else
                    <input type="text" class="form-control @error('new_value') is-invalid @enderror" id="new_value" wire:model="new_value" placeholder="Masukkan nilai baru...">
                  @endif
                  @error('new_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">Batal</button>
                <button type="submit" class="btn btn-primary">Ajukan Perubahan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endif
  @endif
</div>
