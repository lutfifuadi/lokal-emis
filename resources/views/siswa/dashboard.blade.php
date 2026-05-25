@extends('layouts/layoutMaster')

@section('title', 'Dashboard Siswa EMIS')

@section('content')
<div class="row g-6 mb-6">
  <!-- Welcome Card -->
  <div class="col-md-12">
    <div class="card bg-label-primary border-0 shadow-sm">
      <div class="card-body d-flex align-items-center justify-content-between p-6">
        <div>
          <h4 class="text-primary mb-1">Selamat datang kembali, {{ auth()->user()->name }}! 🎉</h4>
          <p class="mb-2">Aplikasi Lokal EMIS siap membantu Anda mengelola data pendidikan dengan cepat dan akurat.</p>
          <div class="d-flex gap-2 align-items-center mt-3">
            <span class="badge bg-primary px-3 py-2 fs-7">Role: {{ auth()->user()->roles->pluck('name')->implode(', ') }}</span>
            @if($mySiswaProfile && $mySiswaProfile->sekolah)
              <span class="badge bg-label-secondary px-3 py-2 fs-7">
                <i class="icon-base ti tabler-school me-1"></i> {{ $mySiswaProfile->sekolah->nama }}
              </span>
            @endif
          </div>
        </div>
        <div class="d-none d-md-block pe-4">
          <i class="icon-base ti tabler-device-dashboard text-primary opacity-50" style="font-size: 4rem !important;"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-6">
  <!-- Left Side: Role Specific Features -->
  <div class="col-md-8">
    <!-- Self-Service Panel -->
    <div class="card border-0 shadow-sm mb-6">
      <div class="card-header">
        <h5 class="mb-0 fw-semibold"><i class="icon-base ti tabler-id-badge text-primary me-2"></i>Status Pengajuan Perubahan Data Anda</h5>
      </div>
      <div class="card-body">
        @if($myPendingRequests->isEmpty())
          <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="icon-base ti tabler-circle-check me-2 fs-4"></i>
            <div>
              Tidak ada pengajuan perubahan data yang sedang aktif. Biodata Anda saat ini sinkron dengan database EMIS.
            </div>
          </div>
        @else
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead>
                <tr>
                  <th>Field / Data</th>
                  <th>Nilai Lama</th>
                  <th>Nilai Baru</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($myPendingRequests as $req)
                  <tr>
                    <td class="fw-medium text-capitalize">{{ str_replace('_', ' ', $req->field) }}</td>
                    <td class="text-muted">{{ $req->old_value ?: '-' }}</td>
                    <td class="text-primary fw-semibold">{{ $req->new_value }}</td>
                    <td>
                      <span class="badge bg-label-warning text-capitalize">
                        <i class="icon-base ti tabler-clock me-1"></i>{{ $req->status }}
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        <div class="mt-4 d-flex gap-2">
          <a href="{{ route('siswa.profil') }}" class="btn btn-outline-primary">
            <i class="icon-base ti tabler-user me-1"></i> Lihat Biodata Saya
          </a>
          <a href="{{ route('siswa.perubahan') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-edit me-1"></i> Ajukan Perubahan Biodata
          </a>
        </div>
      </div>
    </div>

    <!-- Information Card -->
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h5 class="mb-0 fw-semibold"><i class="icon-base ti tabler-info-circle text-primary me-2"></i>Informasi & Pengumuman</h5>
      </div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          <li class="d-flex mb-4">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-info"><i class="icon-base ti tabler-news"></i></span>
            </div>
            <div>
              <h6 class="mb-1 fw-semibold text-heading">Pembaruan Sistem Aplikasi EMIS Lokal v2.0</h6>
              <p class="mb-0 text-muted">Integrasi database remote, integrasi role-based sidebar, dan fitur pengajuan mandiri biodata siswa telah aktif.</p>
              <small class="text-body-secondary">22 Mei 2026</small>
            </div>
          </li>
          <li class="d-flex">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-alert-triangle"></i></span>
            </div>
            <div>
              <h6 class="mb-1 fw-semibold text-heading">Sinkronisasi Data Tahun Ajaran 2025/2026</h6>
              <p class="mb-0 text-muted">Pastikan data kelas, jurusan, dan siswa pada Semester Ganjil 2025/2026 telah sesuai sebelum laporan dinas ditarik.</p>
              <small class="text-body-secondary">20 Mei 2026</small>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Right Side: School Quick Info -->
  <div class="col-md-4">
    <div class="card border-0 shadow-sm mb-6">
      <div class="card-header">
        <h5 class="mb-0 fw-semibold"><i class="icon-base ti tabler-school text-primary me-2"></i>Detail Madrasah</h5>
      </div>
      <div class="card-body">
        @php
          $sekolahDefault = null;
          if ($mySiswaProfile) {
              $sekolahDefault = $mySiswaProfile->sekolah;
          }
          if (!$sekolahDefault) {
              $sekolahDefault = \App\Models\Sekolah::first();
          }
        @endphp
        @if($sekolahDefault)
          <div class="d-flex flex-column align-items-center text-center mb-4">
            <div class="p-3 bg-label-primary rounded-circle mb-3">
              <i class="icon-base ti tabler-building-fortress text-primary" style="font-size: 2.5rem;"></i>
            </div>
            <h5 class="mb-1 fw-bold text-heading">{{ $sekolahDefault->nama }}</h5>
            <span class="badge bg-label-primary">NPSN: {{ $sekolahDefault->npsn }}</span>
          </div>

          <hr class="my-4">

          <div class="d-flex mb-3">
            <div class="me-3"><i class="icon-base ti tabler-map-pin text-primary"></i></div>
            <div>
              <h6 class="mb-0 text-heading">Alamat</h6>
              <small class="text-muted">{{ $sekolahDefault->alamat }}</small>
            </div>
          </div>

          <div class="d-flex mb-3">
            <div class="me-3"><i class="icon-base ti tabler-phone text-primary"></i></div>
            <div>
              <h6 class="mb-0 text-heading">Kontak</h6>
              <small class="text-muted">{{ $sekolahDefault->kontak }}</small>
            </div>
          </div>

          <div class="d-flex">
            <div class="me-3"><i class="icon-base ti tabler-mail text-primary"></i></div>
            <div>
              <h6 class="mb-0 text-heading">Email</h6>
              <small class="text-muted">{{ $sekolahDefault->email }}</small>
            </div>
          </div>
        @else
          <p class="text-muted text-center">Data madrasah belum tersedia.</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
