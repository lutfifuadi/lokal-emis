<div>
  @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
      <i class="icon-base ti tabler-circle-check me-2"></i>
      {{ session('message') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div class="d-flex flex-wrap align-items-center gap-2">
      <a href="{{ route('admin.master.siswa.tambah') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-1"></i> Tambah Siswa
      </a>
      <button onclick="return confirm('Yakin hapus semua data siswa?')" wire:click="resetAll" class="btn btn-outline-danger">
        <i class="icon-base ti tabler-trash me-1"></i> Reset
      </button>
      <a href="{{ route('admin.master.import.sample', 'siswa') }}" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-file-download me-1"></i> Download Sample
      </a>
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal-siswa">
        <i class="icon-base ti tabler-file-import me-1"></i> Import
      </button>
      @php $syncSetting = \App\Models\GoogleSheetSetting::where('entity', 'siswa')->first(); @endphp
      @if($syncSetting && $syncSetting->is_active)
        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#syncModal-siswa">
          <i class="icon-base ti tabler-refresh me-1"></i> Sync Google Sheet
        </button>
      @endif
    </div>
    <div style="width: 280px;">
      <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
        <input type="text" class="form-control" placeholder="Cari nama atau NISN..." wire:model.live="search">
      </div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-striped align-middle">
      <thead>
        <tr style="background: linear-gradient(135deg, #7367f0, #9e95f5);">
          <th class="text-white">Nama</th>
          <th class="text-white">NISN</th>
          <th class="text-white">Jenis Kelamin</th>
          <th class="text-white">Ttl</th>
          <th class="text-white">Status</th>
          <th class="text-center text-white" style="width: 150px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($siswas as $siswa)
          <tr>
            <td class="fw-bold text-heading">{{ $siswa->nama }}</td>
            <td><small class="text-muted">{{ $siswa->nisn }}</small></td>
            <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
            <td>
              @if($siswa->tempat_lahir || $siswa->tanggal_lahir)
                {{ $siswa->tempat_lahir ? $siswa->tempat_lahir . ', ' : '' }}{{ $siswa->tanggal_lahir?->locale('id')->isoFormat('DD MMMM Y') ?? '' }}
              @else
                -
              @endif
            </td>
            <td>
              @php
                $statusClass = 'bg-success';
                if ($siswa->status === 'lulus') $statusClass = 'bg-info';
                elseif ($siswa->status === 'pindah') $statusClass = 'bg-warning';
                elseif ($siswa->status === 'keluar') $statusClass = 'bg-danger';
              @endphp
              <span class="badge {{ $statusClass }}">{{ ucfirst($siswa->status) }}</span>
            </td>
            <td class="text-center">
              <a href="{{ route('admin.master.siswa.edit', $siswa->id) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1" title="Edit">
                <i class="icon-base ti tabler-edit text-primary fs-5"></i>
              </a>
              <button onclick="confirm('Apakah Anda yakin ingin menghapus siswa ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $siswa->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" title="Hapus">
                <i class="icon-base ti tabler-trash text-danger fs-5"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="icon-base ti tabler-users-group fs-1 d-block mb-2 opacity-50"></i>
              Tidak ada data siswa ditemukan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $siswas->links() }}
  </div>
</div>
