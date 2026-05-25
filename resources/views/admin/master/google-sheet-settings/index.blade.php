@extends('layouts/layoutMaster')

@section('title', 'Pengaturan Google Sheet — EMIS')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-style')
<style>
  :root {
    --emis-navy: #0f1f3d;
    --emis-navy-mid: #1a3360;
    --emis-emerald: #059669;
    --emis-emerald-lt: #d1fae5;
    --emis-amber: #d97706;
    --emis-amber-lt: #fef3c7;
    --emis-rose: #e11d48;
    --emis-rose-lt: #ffe4e6;
    --emis-sky: #0284c7;
    --emis-sky-lt: #e0f2fe;
    --emis-slate: #64748b;
    --emis-surface: #f8fafc;
    --emis-border: #e2e8f0;
    --emis-white: #ffffff;
    --emis-text-head: #0f172a;
    --emis-text-body: #475569;
    --emis-text-muted: #94a3b8;

    --emis-radius-sm: 5px;
    --emis-radius: 5px;
    --emis-radius-lg: 5px;

    --emis-shadow-sm: 0 1px 3px rgba(15, 31, 61, .06), 0 1px 2px rgba(15, 31, 61, .04);
    --emis-shadow: 0 4px 16px rgba(15, 31, 61, .08), 0 1px 4px rgba(15, 31, 61, .04);
    --emis-shadow-lg: 0 12px 40px rgba(15, 31, 61, .12), 0 4px 12px rgba(15, 31, 61, .06);
  }

  body {
    font-family: 'Quicksand', sans-serif !important;
    background: var(--emis-surface) !important;
  }

  /* ── Override default cards ── */
  .card {
    border: 1px solid var(--emis-border) !important;
    border-radius: var(--emis-radius) !important;
    box-shadow: var(--emis-shadow-sm) !important;
    background: var(--emis-white) !important;
    overflow: hidden;
  }

  .card-header {
    background: transparent !important;
    border-bottom: 1px solid var(--emis-border) !important;
    padding: 1.25rem 1.5rem !important;
  }

  .card-body {
    padding: 1.5rem !important;
  }

  /* ── Page Header ── */
  .emis-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    padding: 1.75rem 2rem;
    background: linear-gradient(135deg, var(--emis-navy) 0%, var(--emis-navy-mid) 100%);
    border-radius: var(--emis-radius-lg);
    position: relative;
    overflow: hidden;
  }

  .emis-page-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }

  .emis-page-header::after {
    content: '';
    position: absolute;
    right: -60px;
    top: -60px;
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(5, 150, 105, .25) 0%, transparent 70%);
    pointer-events: none;
  }

  .emis-page-header-content {
    position: relative;
    z-index: 1;
  }

  .emis-page-header h1 {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--emis-white) !important;
    margin: 0 0 .25rem;
    letter-spacing: -.02em;
  }

  .emis-page-header p {
    color: rgba(255, 255, 255, .65);
    font-size: .875rem;
    margin: 0;
  }

  /* ── Override buttons ── */
  .btn-primary {
    background: var(--emis-navy) !important;
    color: var(--emis-white) !important;
    border: none !important;
    font-size: .8125rem !important;
    font-weight: 600 !important;
    padding: .55rem 1.25rem !important;
    border-radius: var(--emis-radius-sm) !important;
    transition: background .2s, transform .15s !important;
  }

  .btn-primary:hover {
    background: var(--emis-navy-mid) !important;
    transform: translateY(-1px) !important;
    color: var(--emis-white) !important;
  }

  .btn-outline-primary, .btn-outline-secondary {
    background: transparent !important;
    color: var(--emis-navy) !important;
    border: 1px solid var(--emis-border) !important;
    font-size: .8125rem !important;
    font-weight: 600 !important;
    padding: .5rem 1.15rem !important;
    border-radius: var(--emis-radius-sm) !important;
    transition: border-color .2s, background .2s !important;
  }

  .btn-outline-primary:hover, .btn-outline-secondary:hover {
    border-color: var(--emis-navy) !important;
    background: rgba(15, 31, 61, .04) !important;
    color: var(--emis-navy) !important;
  }

  /* ─── Entity Card Grid ─── */
  .entity-card {
    border: 1.5px solid transparent;
    border-radius: var(--emis-radius);
    transition: all .25s ease;
    cursor: default;
  }
  .entity-card:hover { transform: translateY(-3px); box-shadow: var(--emis-shadow) !important; }
  .entity-card.configured { border-color: var(--emis-navy); }
  .entity-card.unconfigured { border-color: var(--emis-border); }

  /* ─── Status Pill ─── */
  .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .72rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: var(--emis-radius-sm);
  }
  .status-pill.active   { background: var(--emis-emerald-lt); color: var(--emis-emerald); }
  .status-pill.inactive { background: var(--emis-surface); color: var(--emis-slate); border: 1px solid var(--emis-border); }
  .status-pill.ok       { background: var(--emis-emerald-lt); color: var(--emis-emerald); }
  .status-pill.fail     { background: var(--emis-rose-lt); color: var(--emis-rose); }
  .status-pill.pending  { background: var(--emis-amber-lt); color: var(--emis-amber); }

  /* ─── Step Guide ─── */
  .step-guide {
    position: relative;
    padding-left: 52px;
  }
  .step-guide::before {
    content: '';
    position: absolute;
    left: 22px;
    top: 40px;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, var(--emis-navy) 60%, transparent);
    opacity: .25;
  }
  .step-number {
    position: absolute;
    left: 0;
    top: 2px;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--emis-navy), var(--emis-navy-mid));
    color: #fff;
    font-weight: 700;
    font-size: .9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(15,31,61,.2);
    flex-shrink: 0;
  }

  /* ─── Action btns ─── */
  .action-btn { transition: all .2s; border-radius: var(--emis-radius-sm) !important; }
  .action-btn:hover { transform: translateY(-1px); }

  /* ─── Copy indicator ─── */
  .copy-toast {
    position: fixed; bottom: 24px; right: 24px;
    background: var(--emis-emerald); color: #fff;
    padding: 10px 20px; border-radius: var(--emis-radius-sm);
    font-size: .85rem; font-weight: 600;
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
    opacity: 0; transform: translateY(10px);
    transition: all .3s; z-index: 9999;
    pointer-events: none;
  }
  .copy-toast.show { opacity: 1; transform: translateY(0); }
</style>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════ --}}
{{-- HERO BANNER                                  --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="emis-page-header mb-6">
  <div class="emis-page-header-content w-100">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1>
          <i class="ti tabler-file-spreadsheet me-2" style="font-size:1.3rem;vertical-align:middle;opacity:.8;"></i>
          Google Sheet Integration
        </h1>
        <p>Sinkronisasi master data EMIS ↔ Google Sheets secara otomatis</p>
      </div>
      <div class="d-xl-flex align-items-center">
        @if(count($settings) < count($entities))
        <a href="{{ route('admin.master.google-sheet-settings.create') }}" class="btn btn-light fw-semibold px-4 py-2 shadow-sm" style="border-radius: var(--emis-radius-sm) !important; color: var(--emis-navy) !important; background: #ffffff !important; border: 1px solid var(--emis-border) !important;">
          <i class="icon-base ti tabler-plus me-2"></i>Tambah Konfigurasi
        </a>
        @endif
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2 mt-3">
      <span class="badge px-3 py-2" style="background:rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); border-radius: var(--emis-radius-sm); font-size:.75rem;">
        <i class="icon-base ti tabler-arrows-exchange-2 me-1"></i> Import & Export 2 Arah
      </span>
      <span class="badge px-3 py-2" style="background:rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); border-radius: var(--emis-radius-sm); font-size:.75rem;">
        <i class="icon-base ti tabler-shield-lock me-1"></i> Service Account Auth
      </span>
      <span class="badge px-3 py-2" style="background:rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); border-radius: var(--emis-radius-sm); font-size:.75rem;">
        <i class="icon-base ti tabler-database me-1"></i> 6 Entity Master Data
      </span>
    </div>
  </div>
</div>

{{-- Flash message --}}
@if(session('success'))
<div id="flash-alert" class="alert border-0 mb-5 d-flex align-items-center gap-3 shadow-sm"
  style="background:linear-gradient(90deg,#e8f8f0,#f0fdf7); border-left: 4px solid #28c76f !important; border-radius:var(--emis-radius-sm) !important;">
  <div class="rounded-circle p-2" style="background:#28c76f20;">
    <i class="icon-base ti tabler-circle-check text-success fs-5"></i>
  </div>
  <div class="flex-grow-1">
    <strong class="text-success">Berhasil!</strong>
    <span class="text-body ms-1">{{ session('success') }}</span>
  </div>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ═══════════════════════════════════════════ --}}
{{-- ENTITY CARD GRID                            --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-4 mb-6">
  @foreach($entities as $key => $label)
    @php $setting = $settings[$key] ?? null; @endphp
    <div class="col-sm-6 col-xl-4">
      <div class="card entity-card h-100 {{ $setting ? 'configured' : 'unconfigured' }}">
        <div class="card-body p-4">

          {{-- Header --}}
          <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="d-flex align-items-center gap-3">
              <div class="p-2 {{ $setting ? 'bg-label-primary' : 'bg-label-secondary' }}" style="border-radius: 5px;">
                @php
                  $icons = [
                    'sekolah'    => 'tabler-building-bank',
                    'jurusan'    => 'tabler-git-branch',
                    'tahun_ajaran' => 'tabler-calendar',
                    'kelas'      => 'tabler-door-enter',
                    'users'      => 'tabler-users',
                    'siswa'      => 'tabler-user-check',
                  ];
                @endphp
                <i class="icon-base ti {{ $icons[$key] ?? 'tabler-table' }} {{ $setting ? 'text-primary' : 'text-secondary' }}" style="font-size:1.3rem;"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">{{ $label }}</h6>
                <small class="text-muted font-monospace">{{ $key }}</small>
              </div>
            </div>
            <div>
              @if($setting)
                @if($setting->is_active)
                  <span class="status-pill active"><span class="rounded-circle bg-success d-inline-block" style="width:6px;height:6px;"></span> Aktif</span>
                @else
                  <span class="status-pill inactive"><span class="rounded-circle bg-secondary d-inline-block" style="width:6px;height:6px;"></span> Nonaktif</span>
                @endif
              @else
                <span class="status-pill inactive">Belum dikonfigurasi</span>
              @endif
            </div>
          </div>

          {{-- Info rows --}}
          @if($setting)
            <div class="d-flex flex-column gap-2 mb-4">
              <div class="d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-link text-muted icon-sm flex-shrink-0"></i>
                <a href="{{ $setting->spreadsheet_url }}" target="_blank" class="text-truncate small text-primary" style="max-width:220px;">
                  {{ str($setting->spreadsheet_url)->limit(38) }}
                </a>
              </div>
              <div class="d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-table text-muted icon-sm flex-shrink-0"></i>
                <span class="small text-body-secondary">
                  <span class="badge bg-label-info fw-normal">{{ $setting->sheet_name }}</span>
                  <span class="ms-1 font-monospace">{{ $setting->sheet_range }}</span>
                </span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-shield text-muted icon-sm flex-shrink-0"></i>
                @if($setting->credentials_json)
                  <span class="small text-success fw-medium"><i class="icon-base ti tabler-circle-check icon-sm me-1"></i>Credentials tersedia</span>
                @else
                  <span class="small text-danger fw-medium"><i class="icon-base ti tabler-circle-x icon-sm me-1"></i>Credentials belum diisi</span>
                @endif
              </div>
              <div class="d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-clock text-muted icon-sm flex-shrink-0"></i>
                @if($setting->last_sync_at)
                  <span class="small text-body-secondary">Sync: {{ $setting->last_sync_at->format('d/m/Y H:i') }}</span>
                @else
                  <span class="small text-muted fst-italic">Belum pernah sync</span>
                @endif
              </div>
            </div>

            {{-- Connection test badge --}}
            <div class="mb-3">
              <div id="conn-pill-{{ $setting->id }}"
                class="status-pill {{ $setting->last_test_ok ? 'ok' : ($setting->last_test_at ? 'fail' : 'pending') }} w-100 justify-content-center">
                @if($setting->last_test_ok)
                  <i class="icon-base ti tabler-plug-connected icon-sm"></i> Terkoneksi
                  @if($setting->last_test_at) &mdash; {{ $setting->last_test_at->diffForHumans() }} @endif
                @elseif($setting->last_test_at)
                  <i class="icon-base ti tabler-plug-off icon-sm"></i> Koneksi gagal
                @else
                  <i class="icon-base ti tabler-help-circle icon-sm"></i> Belum diuji
                @endif
              </div>
              {{-- Hasil uji koneksi (diisi oleh JS) --}}
              <div id="conn-result-{{ $setting->id }}" class="mt-2" style="display:none;"></div>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2">
              <button type="button"
                class="btn btn-outline-primary btn-sm action-btn flex-grow-1 test-connection-btn"
                data-id="{{ $setting->id }}"
                id="test-btn-{{ $setting->id }}">
                <i class="icon-base ti tabler-plug-connected icon-sm me-1"></i>Uji Koneksi
              </button>
              <a href="{{ route('admin.master.google-sheet-settings.edit', $setting->id) }}"
                class="btn btn-outline-secondary btn-sm action-btn"
                data-bs-toggle="tooltip" title="Edit Konfigurasi">
                <i class="icon-base ti tabler-pencil"></i>
              </a>
              <form action="{{ route('admin.master.google-sheet-settings.destroy', $setting->id) }}" method="POST" class="d-inline delete-form">
                @csrf @method('DELETE')
                <button type="submit"
                  class="btn btn-outline-danger btn-sm action-btn"
                  data-bs-toggle="tooltip" title="Hapus Konfigurasi">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </div>

          @else
            {{-- Empty State --}}
            <div class="d-flex flex-column align-items-center justify-content-center py-4 text-center">
              <div class="mb-3 opacity-30">
                <i class="icon-base ti tabler-file-spreadsheet" style="font-size:3rem;"></i>
              </div>
              <p class="text-muted small mb-4">Belum terhubung ke Google Sheet</p>
              <a href="{{ route('admin.master.google-sheet-settings.create') }}?entity={{ $key }}"
                class="btn btn-sm btn-primary action-btn px-4">
                <i class="icon-base ti tabler-plus me-1"></i> Hubungkan
              </a>
            </div>
          @endif

        </div>
      </div>
    </div>
  @endforeach
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- PANDUAN PENGGUNAAN                           --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="card">
  <div class="card-header border-bottom d-flex align-items-center gap-3">
    <div class="p-2 bg-label-warning" style="border-radius: 5px;">
      <i class="icon-base ti tabler-book-2 text-warning icon-md"></i>
    </div>
    <div>
      <h5 class="mb-0 fw-bold">Panduan Pengaturan</h5>
      <p class="mb-0 text-body-secondary small">Ikuti 6 langkah berikut untuk menghubungkan EMIS dengan Google Sheets</p>
    </div>
  </div>
  <div class="card-body p-5">
    <div class="row g-5">

      @php
        $steps = [
          ['num'=>1, 'icon'=>'tabler-brand-google', 'color'=>'primary', 'title'=>'Buat Service Account Google',
           'desc'=>'Buka <a href="https://console.cloud.google.com" target="_blank" class="fw-semibold">Google Cloud Console</a>, buat project baru, lalu navigasi ke <strong>IAM & Admin → Service Accounts</strong> dan buat service account baru.'],
          ['num'=>2, 'icon'=>'tabler-api', 'color'=>'success', 'title'=>'Aktifkan Google Sheets API',
           'desc'=>'Di menu <strong>APIs & Services → Library</strong>, cari <strong>Google Sheets API</strong> dan klik <strong>Enable</strong> untuk mengaktifkannya di project Anda.'],
          ['num'=>3, 'icon'=>'tabler-key', 'color'=>'warning', 'title'=>'Download Credentials JSON',
           'desc'=>'Buka Service Account yang dibuat → tab <strong>Keys</strong> → <strong>Add Key → Create new key → JSON</strong>. Simpan file <code>.json</code> yang diunduh.'],
          ['num'=>4, 'icon'=>'tabler-share', 'color'=>'info', 'title'=>'Share Spreadsheet ke Service Account',
           'desc'=>'Buka Google Sheet Anda → klik tombol <strong>Share</strong> → masukkan email service account (<code>nama@project.iam.gserviceaccount.com</code>) dengan akses <strong>Editor</strong>.'],
          ['num'=>5, 'icon'=>'tabler-settings-2', 'color'=>'danger', 'title'=>'Tambahkan Konfigurasi & Paste JSON',
           'desc'=>'Klik <strong>Hubungkan</strong> di entity yang diinginkan, isi URL spreadsheet, nama sheet, range, lalu <strong>paste seluruh isi file JSON</strong> credentials ke kolom yang tersedia.'],
          ['num'=>6, 'icon'=>'tabler-refresh', 'color'=>'secondary', 'title'=>'Uji Koneksi & Mulai Sync',
           'desc'=>'Klik tombol <strong>Uji Koneksi</strong>. Jika berhasil ✅, aktifkan konfigurasi dan gunakan tombol <strong>Sync Google Sheet</strong> di halaman master data masing-masing.'],
        ];
      @endphp

      @foreach($steps as $i => $step)
        <div class="col-md-6">
          <div class="step-guide mb-0 pb-0" style="position:relative; padding-left:58px;">
            <span class="step-number" style="background:linear-gradient(135deg,var(--bs-{{ $step['color'] }}),color-mix(in srgb,var(--bs-{{ $step['color'] }}) 70%,white));">{{ $step['num'] }}</span>
            <div style="padding-top:2px;">
              <div class="d-flex align-items-center gap-2 mb-1">
                <i class="icon-base ti {{ $step['icon'] }} text-{{ $step['color'] }} icon-sm"></i>
                <h6 class="mb-0 fw-bold">{{ $step['title'] }}</h6>
              </div>
              <p class="text-body-secondary small mb-0 lh-base">{!! $step['desc'] !!}</p>
            </div>
          </div>
        </div>
      @endforeach

    </div>

    {{-- Quick tips --}}
    <div class="mt-5 p-4 rounded-3" style="background:linear-gradient(90deg,rgba(115,103,240,.06),rgba(115,103,240,.02)); border: 1px dashed rgba(115,103,240,.3);">
      <h6 class="fw-bold mb-3 text-primary"><i class="icon-base ti tabler-bulb me-2"></i>Tips Cepat</h6>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="d-flex gap-2 align-items-start">
            <i class="icon-base ti tabler-circle-dot text-primary icon-sm mt-1 flex-shrink-0"></i>
            <small class="text-body-secondary">Range <code>A:Z</code> mencakup semua kolom. Gunakan <code>A:H</code> jika hanya 8 kolom pertama.</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="d-flex gap-2 align-items-start">
            <i class="icon-base ti tabler-circle-dot text-primary icon-sm mt-1 flex-shrink-0"></i>
            <small class="text-body-secondary">Baris pertama sheet otomatis dibaca sebagai <strong>header kolom</strong>. Pastikan nama header sesuai dengan kolom database.</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="d-flex gap-2 align-items-start">
            <i class="icon-base ti tabler-circle-dot text-primary icon-sm mt-1 flex-shrink-0"></i>
            <small class="text-body-secondary">Satu entity hanya memiliki <strong>satu konfigurasi</strong>. Edit konfigurasi yang ada jika ingin mengganti spreadsheet.</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Toast notification --}}
<div class="copy-toast" id="copyToast">✓ Berhasil disalin!</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ─── Init tooltips ───
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el, { trigger: 'hover' });
  });

  // ─── Delete confirm ───
  document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({
        title: 'Hapus Konfigurasi?',
        text: 'Tindakan ini tidak dapat dibatalkan. Sync yang berjalan akan dihentikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="ti tabler-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ff4c51',
        cancelButtonColor: '#8c8d97',
        customClass: { popup: 'rounded-3' }
      }).then(result => {
        if (result.isConfirmed) this.submit();
      }.bind(this));
    });
  });

  // ─── Helper: safe alert (fallback jika Swal tidak terdefinisi) ───
  function showAlert(icon, title, message) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({ icon, title, text: message, confirmButtonColor: icon === 'success' ? '#28c76f' : '#ff4c51', customClass: { popup: 'rounded-3' } });
    } else {
      console.error('[GS Alert]', title, message);
      alert((icon === 'error' ? '❌ ' : '✅ ') + title + '\n\n' + message);
    }
  }

  // ─── Test connection ───
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
  const gsBaseUrl = window.location.origin + '/admin/master/google-sheet-settings';

  document.querySelectorAll('.test-connection-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      if (!id) { showAlert('error', 'Error', 'ID konfigurasi tidak ditemukan.'); return; }

      const btnEl    = this;
      const origHtml = btnEl.innerHTML;
      const pill     = document.getElementById('conn-pill-'   + id);
      const resultBox = document.getElementById('conn-result-' + id);

      // ── Loading state ──
      btnEl.disabled = true;
      btnEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menguji...';
      if (pill) {
        pill.className = 'status-pill pending w-100 justify-content-center';
        pill.innerHTML = '<span class="spinner-border me-1" style="width:10px;height:10px;border-width:2px;"></span> Menguji koneksi...';
      }
      if (resultBox) resultBox.style.display = 'none';

      fetch(gsBaseUrl + '/' + id + '/test-connection', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        }
      })
      .then(function (res) {
        if (!res.ok) {
          return res.json().then(function (d) { throw new Error(d.message || 'Server error ' + res.status); });
        }
        return res.json();
      })
      .then(function (data) {
        if (data.success) {
          // ── Update pill ──
          if (pill) {
            pill.className = 'status-pill ok w-100 justify-content-center';
            pill.innerHTML = '<i class="icon-base ti tabler-plug-connected icon-sm"></i>&nbsp;Terkoneksi &mdash; baru saja';
          }
          // ── Build result panel ──
          let headersHtml = '';
          if (data.headers && data.headers.length > 0) {
            headersHtml = '<div class="mt-2 pt-2" style="border-top:1px solid rgba(40,199,111,.25);">'
              + '<small class="text-success fw-semibold d-block mb-1">'
              + '<i class="icon-base ti tabler-columns icon-sm me-1"></i>'
              + 'Header kolom terdeteksi (' + data.headers.length + '):'
              + '</small>'
              + data.headers.map(function (h) {
                  return '<span class="badge bg-label-success me-1 mb-1 fw-normal">' + h + '</span>';
                }).join('')
              + '</div>';
          }
          if (resultBox) {
            resultBox.innerHTML =
              '<div class="p-3 rounded-3" style="background:linear-gradient(135deg,rgba(40,199,111,.09),rgba(40,199,111,.03));border:1px solid rgba(40,199,111,.3);">'
              + '<div class="d-flex align-items-center gap-2 mb-1">'
              + '<div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px;background:rgba(40,199,111,.15);">'
              + '<i class="icon-base ti tabler-circle-check text-success" style="font-size:.9rem;"></i></div>'
              + '<strong class="text-success">Koneksi Berhasil!</strong></div>'
              + '<p class="mb-0 small text-body-secondary lh-sm">' + (data.message || '') + '</p>'
              + headersHtml
              + '</div>';
            resultBox.style.display = 'block';
          }
        } else {
          // ── Update pill ──
          if (pill) {
            pill.className = 'status-pill fail w-100 justify-content-center';
            pill.innerHTML = '<i class="icon-base ti tabler-plug-off icon-sm"></i>&nbsp;Koneksi gagal';
          }
          if (resultBox) {
            resultBox.innerHTML =
              '<div class="p-3 rounded-3" style="background:linear-gradient(135deg,rgba(255,76,81,.09),rgba(255,76,81,.03));border:1px solid rgba(255,76,81,.3);">'
              + '<div class="d-flex align-items-center gap-2 mb-1">'
              + '<div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px;background:rgba(255,76,81,.15);">'
              + '<i class="icon-base ti tabler-circle-x text-danger" style="font-size:.9rem;"></i></div>'
              + '<strong class="text-danger">Koneksi Gagal</strong></div>'
              + '<p class="mb-0 small text-body-secondary lh-sm">' + (data.message || 'Tidak dapat terhubung ke Google Sheet.') + '</p>'
              + '</div>';
            resultBox.style.display = 'block';
          }
        }
      })
      .catch(function (err) {
        console.error('[GS TestConnection]', err);
        if (pill) {
          pill.className = 'status-pill fail w-100 justify-content-center';
          pill.innerHTML = '<i class="icon-base ti tabler-alert-circle icon-sm"></i>&nbsp;Error';
        }
        if (resultBox) {
          resultBox.innerHTML =
            '<div class="p-3 rounded-3" style="background:rgba(255,76,81,.06);border:1px solid rgba(255,76,81,.25);">'
            + '<div class="d-flex align-items-center gap-2">'
            + '<i class="icon-base ti tabler-alert-triangle text-danger" style="font-size:.9rem;"></i>'
            + '<small class="text-danger">' + (err.message || 'Terjadi kesalahan saat menguji koneksi.') + '</small>'
            + '</div></div>';
          resultBox.style.display = 'block';
        }
      })
      .finally(function () {
        btnEl.disabled = false;
        btnEl.innerHTML = origHtml;
      });
    });
  });

  // ─── Auto dismiss flash ───
  const flashAlert = document.getElementById('flash-alert');
  if (flashAlert) {
    setTimeout(() => {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(flashAlert);
      bsAlert.close();
    }, 4000);
  }

});
</script>
@endsection
