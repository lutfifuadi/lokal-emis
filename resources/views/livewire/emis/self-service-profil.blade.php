<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5">
    <!-- User Card -->
    <div class="card mb-4 border-0 shadow-sm">
      <div class="card-body">
        <div class="user-avatar-section">
          <div class="d-flex align-items-center flex-column">
            <div class="avatar avatar-xl bg-primary-subtle text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
              <i class="icon-base ti tabler-user fs-1"></i>
            </div>
            <div class="user-info text-center">
              <h4 class="mb-2 fw-bold">{{ $profileData['nama'] ?? 'N/A' }}</h4>
              <span class="badge bg-label-secondary text-uppercase">{{ Auth::user()->roles->first()?->name }}</span>
            </div>
          </div>
        </div>
        
        <h5 class="pb-2 border-bottom mb-4 mt-4 fw-bold">Detail Akun</h5>
        <div class="info-container">
          <ul class="list-unstyled">
            <li class="mb-3 d-flex align-items-center">
              <i class="icon-base ti tabler-mail me-2 text-primary"></i>
              <span class="fw-bold me-2">Email:</span>
              <span>{{ $profileData['email'] ?? '-' }}</span>
            </li>
            <li class="mb-3 d-flex align-items-center">
              <i class="icon-base ti tabler-building me-2 text-primary"></i>
              <span class="fw-bold me-2">Sekolah:</span>
              <span>{{ $profileData['sekolah'] ?? '-' }}</span>
            </li>
            @if($profileType === 'siswa')
              <li class="mb-3 d-flex align-items-center">
                <i class="icon-base ti tabler-circle-check me-2 text-success"></i>
                <span class="fw-bold me-2">Status:</span>
                <span class="badge bg-label-success text-uppercase">{{ $profileData['status'] ?? '-' }}</span>
              </li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-8 col-lg-7 col-md-7">
    <!-- Detail Card -->
    <div class="card mb-4 border-0 shadow-sm">
      <h5 class="card-header fw-bold border-bottom py-3">
        <i class="icon-base ti tabler-id-badge me-2 text-primary"></i> Biodata Lengkap
      </h5>
      <div class="card-body pt-4">
        @if($profileType === 'siswa')
          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">Nama Lengkap</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['nama'] }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">NISN</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['nisn'] }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">NIK</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['nik'] }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">Kelas</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['kelas'] }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">No. HP</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['no_hp'] }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">Kontak Darurat (Orang Tua)</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['kontak_darurat'] }}</p>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label text-muted small uppercase">Alamat Lengkap</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['alamat'] }}</p>
            </div>
          </div>
          <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="icon-base ti tabler-info-circle me-2 fs-4"></i>
            <div>
              Ingin mengubah data di atas? Silakan ajukan usulan perubahan data di menu <strong>Usulan Perubahan</strong>.
            </div>
          </div>
        @elseif($profileType === 'guru')
          <div class="row mb-4">
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">Nama Lengkap</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['nama'] }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">NIK</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['nik'] }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">NUPTK</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['nuptk'] }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-muted small uppercase">Sekolah</label>
              <p class="fw-bold fs-5 text-heading">{{ $profileData['sekolah'] }}</p>
            </div>
          </div>
        @else
          <div class="text-center py-5 text-muted">
            <i class="icon-base ti tabler-mood-empty fs-1 mb-2"></i>
            <p>Profil tidak ditemukan.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
