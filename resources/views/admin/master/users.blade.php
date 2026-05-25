@extends('layouts/layoutMaster')

@section('title', 'Master Data Pengguna')

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

  /* ── Button overrides ── */
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

  .btn-outline-secondary {
    background: transparent !important;
    color: var(--emis-navy) !important;
    border: 1px solid var(--emis-border) !important;
    font-size: .8125rem !important;
    font-weight: 600 !important;
    padding: .5rem 1.15rem !important;
    border-radius: var(--emis-radius-sm) !important;
    transition: border-color .2s, background .2s !important;
  }

  .btn-outline-secondary:hover {
    border-color: var(--emis-navy) !important;
    background: rgba(15, 31, 61, .04) !important;
    color: var(--emis-navy) !important;
  }

  /* ── Form focus ── */
  .form-control:focus, .form-select:focus {
    border-color: var(--emis-navy-mid) !important;
    box-shadow: 0 0 0 3px rgba(15, 31, 61, .15) !important;
  }

  /* ── Modal styling ── */
  .modal-content {
    border-radius: var(--emis-radius) !important;
    border: 1px solid var(--emis-border) !important;
  }

  .modal-header {
    background: var(--emis-surface) !important;
    border-bottom: 1px solid var(--emis-border) !important;
    border-top-left-radius: var(--emis-radius) !important;
    border-top-right-radius: var(--emis-radius) !important;
  }

  .modal-footer {
    background: var(--emis-surface) !important;
    border-top: 1px solid var(--emis-border) !important;
    border-bottom-left-radius: var(--emis-radius) !important;
    border-bottom-right-radius: var(--emis-radius) !important;
  }

  /* ── Alerts ── */
  .alert {
    border-radius: var(--emis-radius-sm) !important;
  }
</style>
@endsection

@section('content')
<div class="emis-page-header mb-6">
  <div class="emis-page-header-content">
    <h1>
      <i class="ti tabler-users me-2" style="font-size:1.3rem;vertical-align:middle;opacity:.8;"></i>
      Master Data Pengguna
    </h1>
    <p>Kelola data pengguna dalam sistem EMIS</p>
  </div>
</div>

@if(session('success'))
<div id="flash-alert" class="alert border-0 mb-5 d-flex align-items-center gap-3 shadow-sm"
  style="background:linear-gradient(90deg,#e8f8f0,#f0fdf7); border-left:4px solid #28c76f !important; border-radius:12px;">
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

@if(session('error'))
<div id="flash-alert" class="alert border-0 mb-5 d-flex align-items-center gap-3 shadow-sm"
  style="background:linear-gradient(90deg,#ffeef0,#fff5f5); border-left:4px solid #ff4c51 !important; border-radius:12px;">
  <div class="rounded-circle p-2" style="background:#ff4c5120;">
    <i class="icon-base ti tabler-alert-circle text-danger fs-5"></i>
  </div>
  <div class="flex-grow-1">
    <strong class="text-danger">Gagal!</strong>
    <span class="text-body ms-1">{{ session('error') }}</span>
  </div>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
  <div class="card-body">
    @include('admin.master._import-modal', ['entity' => 'users'])
    @include('admin.master._sync-modal', ['entity' => 'users'])
    @livewire('emis.master-user')
  </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
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
