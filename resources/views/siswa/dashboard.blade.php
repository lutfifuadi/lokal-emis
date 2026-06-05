@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard Siswa — EMIS')

@section('page-style')
  @include('admin._emis-styles')
@endsection

@section('content')

  {{-- ============================================ --}}
  {{-- PAGE HEADER                                 --}}
  {{-- ============================================ --}}
  <div class="emis-page-header emis-fade-up mb-6">
    <div class="emis-page-header-content">
      <h1><i class="ti tabler-layout-dashboard me-2" style="font-size:1.3rem;vertical-align:middle;opacity:.8;"></i>Dashboard Siswa</h1>
      <p>Selamat datang kembali, {{ auth()->user()->name }} — Kelola biodata Anda di sini</p>
    </div>
    <div class="d-flex gap-2 align-items-center" style="position:relative;z-index:1;">
      <span class="emis-chip stat-glass"><i class="ti tabler-shield"></i> {{ auth()->user()->roles->pluck('name')->implode(', ') }}</span>
      @if($mySiswaProfile && $mySiswaProfile->sekolah)
        <span class="emis-chip stat-glass"><i class="ti tabler-school"></i> {{ $mySiswaProfile->sekolah->nama }}</span>
      @endif
    </div>
  </div>

  {{-- ============================================ --}}
  {{-- ROW 1: Status Pengajuan + School Detail     --}}
  {{-- ============================================ --}}
  <div class="row g-4 mb-5">

    {{-- Left: Self-Service Status --}}
    <div class="col-md-8">
      <div class="card h-100 emis-fade-up delay-2">
        <div class="card-header">
          <h5 class="emis-card-title"><i class="ti tabler-id-badge me-2"></i>Status Pengajuan Perubahan Data</h5>
          <p class="emis-card-sub">Ringkasan pengajuan biodata Anda</p>
        </div>
        <div class="card-body">
          @if($myPendingRequests->isEmpty())
            <div class="d-flex align-items-center gap-3 p-3" style="background:var(--emis-emerald-lt);border-radius:var(--emis-radius-sm);">
              <i class="ti tabler-circle-check" style="font-size:1.5rem;color:var(--emis-emerald);"></i>
              <div>
                <p class="mb-0" style="font-weight:600;color:var(--emis-emerald);">Tidak ada pengajuan perubahan data yang sedang aktif.</p>
                <small style="color:var(--emis-text-muted);">Biodata Anda saat ini sinkron dengan database EMIS.</small>
              </div>
            </div>
          @else
            <div class="table-responsive">
              <table class="emis-table">
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
                      <td><span class="fw-medium text-capitalize" style="color:var(--emis-text-head);">{{ str_replace('_', ' ', $req->field) }}</span></td>
                      <td style="color:var(--emis-text-muted);">{{ $req->old_value ?: '-' }}</td>
                      <td style="color:var(--emis-emerald);font-weight:600;">{{ $req->new_value }}</td>
                      <td>
                        <span class="emis-chip" style="background:var(--emis-amber-lt);color:var(--emis-amber);border-color:#fde68a;">
                          <i class="ti tabler-clock"></i>{{ $req->status }}
                        </span>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif

          <div class="mt-4 d-flex gap-2">
            <a href="{{ route('siswa.profil') }}" class="btn-emis-outline text-decoration-none">
              <i class="ti tabler-user"></i> Lihat Biodata Saya
            </a>
            <a href="{{ route('siswa.perubahan') }}" class="btn-emis-primary text-decoration-none">
              <i class="ti tabler-edit"></i> Ajukan Perubahan Biodata
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Right: School Quick Info --}}
    <div class="col-md-4">
      @php
        $sekolahDefault = null;
        if ($mySiswaProfile) {
            $sekolahDefault = $mySiswaProfile->sekolah;
        }
        if (!$sekolahDefault) {
            $sekolahDefault = \App\Models\Sekolah::first();
        }
      @endphp
      <div class="card h-100 emis-fade-up delay-3">
        <div class="card-header">
          <h5 class="emis-card-title"><i class="ti tabler-school me-2"></i>Detail Madrasah</h5>
          <p class="emis-card-sub">Informasi madrasah Anda</p>
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
  {{-- ROW 2: Informasi & Pengumuman               --}}
  {{-- ============================================ --}}
  <p class="emis-section-label emis-fade-up delay-4">Informasi & Pengumuman</p>

  <div class="row g-4 emis-fade-up delay-5">
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
