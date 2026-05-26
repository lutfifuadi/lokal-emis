@extends('layouts/layoutMaster')

@section('title', 'Setting Maintenance Mode')

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
    --emis-radius: 5px;
  }

  body {
    font-family: 'Quicksand', sans-serif !important;
    background: var(--emis-surface) !important;
  }

  .card {
    border: 1px solid var(--emis-border) !important;
    border-radius: var(--emis-radius) !important;
    box-shadow: 0 1px 3px rgba(15, 31, 61, .06) !important;
    background: #fff !important;
  }

  .form-switch .form-check-input {
    width: 3.5rem;
    height: 1.75rem;
    cursor: pointer;
  }

  .form-switch .form-check-input:checked {
    background-color: var(--emis-emerald);
    border-color: var(--emis-emerald);
  }

  .status-badge {
    font-size: .85rem;
    padding: .35rem 1rem;
    border-radius: 50px;
    font-weight: 600;
  }

  .status-badge.on {
    background: var(--emis-rose-lt);
    color: var(--emis-rose);
  }

  .status-badge.off {
    background: var(--emis-emerald-lt);
    color: var(--emis-emerald);
  }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center gap-4 mb-6 p-4 rounded-3"
  style="background:linear-gradient(135deg,var(--emis-navy),var(--emis-navy-mid));">
  <div class="p-3 rounded-2" style="background:rgba(255,255,255,.15);">
    <i class="icon-base ti tabler-tools text-white fs-2"></i>
  </div>
  <div>
    <h1 class="text-white fw-bold mb-1" style="font-size:1.5rem;">Setting Maintenance Mode</h1>
    <p class="text-white-50 mb-0 small">Aktifkan atau nonaktifkan mode pemeliharaan aplikasi</p>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
  <i class="icon-base ti tabler-circle-check me-2"></i> {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
  <div class="card-body p-5">
    <form method="POST" action="{{ route('admin.master.setting-maintenance.update') }}">
      @csrf

      <div class="d-flex align-items-center justify-content-between mb-5 p-4 rounded-3"
        style="background:var(--emis-surface);border:1px solid var(--emis-border);">
        <div>
          <h5 class="fw-bold mb-1">Status Maintenance</h5>
          <p class="text-muted mb-0 small">
            Saat ini mode maintenance dalam status
            <span class="status-badge {{ $maintenanceMode === 'on' ? 'on' : 'off' }}">
              <i class="icon-base ti {{ $maintenanceMode === 'on' ? 'tabler-alert-triangle' : 'tabler-circle-check' }} me-1"></i>
              {{ $maintenanceMode === 'on' ? 'AKTIF' : 'NONAKTIF' }}
            </span>
          </p>
        </div>
        <div class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" id="maintenance_toggle"
            name="maintenance_mode" value="on"
            {{ $maintenanceMode === 'on' ? 'checked' : '' }}>
        </div>
      </div>

      <div id="maintenance-warning" class="alert alert-warning mb-5 {{ $maintenanceMode === 'on' ? '' : 'd-none' }}" role="alert">
        <div class="d-flex align-items-start gap-3">
          <i class="icon-base ti tabler-alert-triangle fs-4 mt-1"></i>
          <div>
            <strong>Perhatian!</strong> Saat mode pemeliharaan aktif:
            <ul class="mb-0 mt-2 ps-3">
              <li>Hanya pengguna dengan role <strong>Super Admin</strong> yang dapat mengakses aplikasi</li>
              <li>Pengguna lain akan dialihkan ke halaman pemeliharaan</li>
              <li>Halaman login tetap dapat diakses</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="pt-4" style="border-top:1px solid var(--emis-border);">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
          <i class="icon-base ti tabler-device-floppy me-2"></i> Simpan Pengaturan
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('maintenance_toggle');
  const warning = document.getElementById('maintenance-warning');

  toggle.addEventListener('change', function () {
    warning.classList.toggle('d-none', !this.checked);
  });

  const flashAlert = document.querySelector('.alert-success');
  if (flashAlert) {
    setTimeout(() => {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(flashAlert);
      bsAlert.close();
    }, 4000);
  }
});
</script>
@endsection