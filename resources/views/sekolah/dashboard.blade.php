@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard Sekolah — EMIS')

@section('page-style')
  @include('admin._emis-styles')
@endsection

@section('content')

  {{-- ============================================ --}}
  {{-- PAGE HEADER                                 --}}
  {{-- ============================================ --}}
  <div class="emis-page-header emis-fade-up mb-6">
    <div class="emis-page-header-content">
      <h1><i class="ti tabler-layout-dashboard me-2" style="font-size:1.3rem;vertical-align:middle;opacity:.8;"></i>Dashboard Sekolah</h1>
      <p>Selamat datang kembali, {{ auth()->user()->name }} — Ringkasan data madrasah hari ini</p>
    </div>
    <div class="d-flex gap-2 align-items-center" style="position:relative;z-index:1;">
      <span class="emis-chip stat-glass"><i class="ti tabler-shield"></i> {{ auth()->user()->roles->pluck('name')->implode(', ') }}</span>
      @if(auth()->user()->sekolah)
        <span class="emis-chip stat-glass"><i class="ti tabler-school"></i> {{ auth()->user()->sekolah->nama }}</span>
      @endif
    </div>
  </div>

  {{-- ============================================ --}}
  {{-- ROW 1: Stat Cards                           --}}
  {{-- ============================================ --}}
  <p class="emis-section-label emis-fade-up delay-1">Metrik Utama</p>

  <div class="row g-4 mb-5">

    {{-- Total Siswa --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card emis-stat-card stat-card-navy h-100 emis-fade-up delay-2">
        <div class="card-body" style="padding: 1.5rem !important; position:relative; z-index:1;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="emis-stat-icon">
              <i class="ti tabler-user-check"></i>
            </div>
            <span class="emis-chip stat-glass">Siswa</span>
          </div>
          <div class="emis-stat-number">{{ number_format($stats['total_siswa']) }}</div>
          <div class="emis-stat-label">Total Siswa</div>
          <div class="emis-stat-sub">Terdaftar aktif di sistem</div>
        </div>
      </div>
    </div>

    {{-- Total Guru --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card emis-stat-card stat-card-emerald h-100 emis-fade-up delay-3">
        <div class="card-body" style="padding: 1.5rem !important; position:relative; z-index:1;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="emis-stat-icon">
              <i class="ti tabler-users"></i>
            </div>
            <span class="emis-chip stat-glass">Pengajar</span>
          </div>
          <div class="emis-stat-number">{{ number_format($stats['total_guru']) }}</div>
          <div class="emis-stat-label">Total Guru</div>
          <div class="emis-stat-sub">Tenaga pengajar aktif</div>
        </div>
      </div>
    </div>

    {{-- Total Sekolah --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card emis-stat-card stat-card-sky h-100 emis-fade-up delay-4">
        <div class="card-body" style="padding: 1.5rem !important; position:relative; z-index:1;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="emis-stat-icon">
              <i class="ti tabler-building-bank"></i>
            </div>
            <span class="emis-chip stat-glass">Sekolah</span>
          </div>
          <div class="emis-stat-number">{{ number_format($stats['total_sekolah']) }}</div>
          <div class="emis-stat-label">Total Sekolah</div>
          <div class="emis-stat-sub">Madrasah terdaftar</div>
        </div>
      </div>
    </div>

    {{-- Total Kelas --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card emis-stat-card stat-card-rose h-100 emis-fade-up delay-5">
        <div class="card-body" style="padding: 1.5rem !important; position:relative; z-index:1;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="emis-stat-icon">
              <i class="ti tabler-door-enter"></i>
            </div>
            <span class="emis-chip stat-glass">Kelas</span>
          </div>
          <div class="emis-stat-number">{{ number_format($stats['total_kelas']) }}</div>
          <div class="emis-stat-label">Total Kelas</div>
          <div class="emis-stat-sub">Rombongan belajar aktif</div>
        </div>
      </div>
    </div>

  </div>

  {{-- ============================================ --}}
  {{-- ROW 2: Content + School Detail              --}}
  {{-- ============================================ --}}
  <div class="row g-4 mb-5">

    {{-- Left: Approval Queue --}}
    <div class="col-md-8">
      <div class="card h-100 emis-fade-up delay-6">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="emis-card-title"><i class="ti tabler-checklist text-primary me-2"></i>Antrian Persetujuan Biodata</h5>
            <p class="emis-card-sub">Pengajuan perubahan data yang perlu diverifikasi</p>
          </div>
          <span class="emis-chip stat-glass" style="background:rgba(225,29,72,.2);border-color:rgba(225,29,72,.3);">
            <i class="ti tabler-clock"></i> {{ $pendingApprovalsCount }} Pending
          </span>
        </div>
        <div class="card-body">
          <p>Terdapat <strong>{{ $pendingApprovalsCount }}</strong> pengajuan perubahan data dari Siswa atau Orang Tua yang memerlukan verifikasi.</p>
          <div class="mt-4">
            <a href="{{ route('sekolah.approval.antrian') }}" class="btn-emis-primary text-decoration-none">
              <i class="ti tabler-eye"></i> Buka Antrian Verifikasi
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Right: School Quick Info --}}
    <div class="col-md-4">
      @php
        $sekolahDefault = auth()->user()->sekolah ?: \App\Models\Sekolah::first();
      @endphp
      <div class="card h-100 emis-fade-up delay-7">
        <div class="card-header">
          <h5 class="emis-card-title"><i class="ti tabler-school me-2"></i>Detail Madrasah</h5>
          <p class="emis-card-sub">Informasi madrasah utama</p>
        </div>
        <div class="card-body">
          @if($sekolahDefault)
            <div class="text-center mb-3">
              <div class="school-badge-icon">
                <i class="ti tabler-building-fortress"></i>
              </div>
              <h6 style="font-size:.9375rem;font-weight:700;color:var(--emis-text-head);margin-bottom:.4rem;">{{ $sekolahDefault->nama }}</h6>
              <span style="font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--emis-slate);background:var(--emis-surface);border:1px solid var(--emis-border);padding:.25rem .75rem;border-radius:100px;">NPSN: {{ $sekolahDefault->npsn }}</span>
            </div>
            <div class="school-info-row">
              <div class="school-info-icon"><i class="ti tabler-map-pin"></i></div>
              <div>
                <div class="school-info-label">Alamat</div>
                <div class="school-info-val">{{ $sekolahDefault->alamat }}</div>
              </div>
            </div>
            <div class="school-info-row">
              <div class="school-info-icon"><i class="ti tabler-phone"></i></div>
              <div>
                <div class="school-info-label">Kontak</div>
                <div class="school-info-val">{{ $sekolahDefault->kontak }}</div>
              </div>
            </div>
            <div class="school-info-row">
              <div class="school-info-icon"><i class="ti tabler-mail"></i></div>
              <div>
                <div class="school-info-label">Email</div>
                <div class="school-info-val">{{ $sekolahDefault->email }}</div>
              </div>
            </div>
          @else
            <div class="emis-empty">
              <i class="ti tabler-building-off"></i>
              <p>Data madrasah belum tersedia.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>

  {{-- ============================================ --}}
  {{-- ROW 3: Informasi & Pengumuman               --}}
  {{-- ============================================ --}}
  <p class="emis-section-label emis-fade-up delay-7">Informasi & Pengumuman</p>

  <div class="row g-4 emis-fade-up delay-8">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <ul class="list-unstyled mb-0">
            <li class="d-flex mb-4">
              <div class="school-info-icon me-3">
                <i class="ti tabler-news"></i>
              </div>
              <div>
                <h6 class="fw-semibold mb-1" style="color:var(--emis-text-head);">Pembaruan Sistem Aplikasi EMIS Lokal v2.0</h6>
                <p class="mb-0" style="font-size:.8125rem;color:var(--emis-text-body);">Integrasi database remote, integrasi role-based sidebar, dan fitur pengajuan mandiri biodata siswa telah aktif.</p>
                <small style="color:var(--emis-text-muted);">22 Mei 2026</small>
              </div>
            </li>
            <li class="d-flex">
              <div class="school-info-icon me-3">
                <i class="ti tabler-alert-triangle"></i>
              </div>
              <div>
                <h6 class="fw-semibold mb-1" style="color:var(--emis-text-head);">Sinkronisasi Data Tahun Ajaran 2025/2026</h6>
                <p class="mb-0" style="font-size:.8125rem;color:var(--emis-text-body);">Pastikan data kelas, jurusan, dan siswa pada Semester Ganjil 2025/2026 telah sesuai sebelum laporan dinas ditarik.</p>
                <small style="color:var(--emis-text-muted);">20 Mei 2026</small>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

@endsection
