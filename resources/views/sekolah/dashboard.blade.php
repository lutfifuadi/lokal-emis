@extends('layouts/layoutMaster')

@section('title', 'Dashboard Sekolah EMIS')

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
            @if(auth()->user()->sekolah)
              <span class="badge bg-label-secondary px-3 py-2 fs-7">
                <i class="icon-base ti tabler-school me-1"></i> {{ auth()->user()->sekolah->nama }}
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

<!-- Stats Row -->
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="mb-1 text-muted">Total Siswa</p>
          <h3 class="mb-0 fw-bold">{{ $stats['total_siswa'] }}</h3>
        </div>
        <div class="avatar bg-light-primary rounded p-2">
          <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-user fs-4"></i></span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="mb-1 text-muted">Total Guru</p>
          <h3 class="mb-0 fw-bold">{{ $stats['total_guru'] }}</h3>
        </div>
        <div class="avatar bg-light-success rounded p-2">
          <span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-users fs-4"></i></span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="mb-1 text-muted">Total Sekolah</p>
          <h3 class="mb-0 fw-bold">{{ $stats['total_sekolah'] }}</h3>
        </div>
        <div class="avatar bg-light-info rounded p-2">
          <span class="avatar-initial rounded bg-label-info"><i class="icon-base ti tabler-school fs-4"></i></span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="mb-1 text-muted">Total Kelas</p>
          <h3 class="mb-0 fw-bold">{{ $stats['total_kelas'] }}</h3>
        </div>
        <div class="avatar bg-light-warning rounded p-2">
          <span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-door-enter fs-4"></i></span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-6">
  <!-- Left Side: Role Specific Features -->
  <div class="col-md-8">
    <!-- Operator / Admin Queue Summary -->
    <div class="card border-0 shadow-sm mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold"><i class="icon-base ti tabler-checklist text-primary me-2"></i>Antrian Persetujuan Biodata</h5>
        <span class="badge bg-danger rounded-pill">{{ $pendingApprovalsCount }} Pending</span>
      </div>
      <div class="card-body">
        <p>Terdapat <strong>{{ $pendingApprovalsCount }}</strong> pengajuan perubahan data dari Siswa atau Orang Tua yang memerlukan verifikasi.</p>
        <div class="mt-4">
          <a href="{{ route('sekolah.approval.antrian') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-eye me-1"></i> Buka Antrian Verifikasi
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
          $sekolahDefault = auth()->user()->sekolah ?: \App\Models\Sekolah::first();
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
