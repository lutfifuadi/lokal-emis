@extends('layouts/layoutMaster')

@section('title', 'Setting Mode Sekolah')

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

    --emis-radius-sm: 5px;
    --emis-radius: 5px;
    --emis-radius-lg: 5px;
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

  .form-control:focus, .form-select:focus {
    border-color: var(--emis-navy-mid) !important;
    box-shadow: 0 0 0 3px rgba(15, 31, 61, .15) !important;
  }

  .mode-card {
    cursor: pointer;
    border: 2px solid var(--emis-border);
    border-radius: var(--emis-radius);
    transition: all .25s ease;
    padding: 1.5rem;
  }
  .mode-card:hover {
    border-color: var(--emis-navy-mid);
    box-shadow: 0 4px 16px rgba(15, 31, 61, .08);
  }
  .mode-card.selected {
    border-color: var(--emis-navy);
    background: rgba(15, 31, 61, .03);
  }
  .mode-card input[type="radio"] {
    display: none;
  }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center gap-4 mb-6 p-4 rounded-3"
  style="background:linear-gradient(135deg,var(--emis-navy),var(--emis-navy-mid));">
  <div class="p-3 rounded-2" style="background:rgba(255,255,255,.15);">
    <i class="icon-base ti tabler-building-community text-white fs-2"></i>
  </div>
  <div>
    <h1 class="text-white fw-bold mb-1" style="font-size:1.5rem;">Setting Mode Sekolah</h1>
    <p class="text-white-50 mb-0 small">Atur apakah aplikasi berjalan untuk satu sekolah atau multi sekolah</p>
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
    <form method="POST" action="{{ route('admin.master.setting-sekolah.update') }}">
      @csrf

      <div class="row g-4 mb-5">
        <div class="col-md-6">
          <label class="mode-card d-block text-center p-4 {{ $appMode === 'multi' ? 'selected' : '' }}" data-mode="multi">
            <input type="radio" name="app_mode" value="multi" {{ $appMode === 'multi' ? 'checked' : '' }}>
            <div class="p-3 mb-3 d-inline-flex rounded-circle" style="background:var(--emis-sky-lt);">
              <i class="icon-base ti tabler-building-skyscraper text-primary fs-1"></i>
            </div>
            <h5 class="fw-bold mb-2">Multi Sekolah</h5>
            <p class="small text-muted mb-0 px-3">
              Admin & Dinas dapat melihat dan mengelola data dari <strong>semua sekolah</strong>. 
              Operator & Kepala Sekolah hanya melihat sekolahnya masing-masing.
            </p>
          </label>
        </div>

        <div class="col-md-6">
          <label class="mode-card d-block text-center p-4 {{ $appMode === 'single' ? 'selected' : '' }}" data-mode="single">
            <input type="radio" name="app_mode" value="single" {{ $appMode === 'single' ? 'checked' : '' }}>
            <div class="p-3 mb-3 d-inline-flex rounded-circle" style="background:var(--emis-emerald-lt);">
              <i class="icon-base ti tabler-building text-success fs-1"></i>
            </div>
            <h5 class="fw-bold mb-2">Single Sekolah</h5>
            <p class="small text-muted mb-0 px-3">
              Aplikasi difokuskan untuk <strong>1 sekolah saja</strong>. 
              Semua pengguna (termasuk Admin) hanya melihat data sekolah terpilih.
            </p>
          </label>
        </div>
      </div>

      <div id="default-sekolah-wrapper" class="mb-5 {{ $appMode === 'single' ? '' : 'd-none' }}">
        <label class="form-label fw-semibold" for="default_sekolah_id">Pilih Sekolah Default</label>
        <select class="form-select @error('default_sekolah_id') is-invalid @enderror" id="default_sekolah_id" name="default_sekolah_id">
          <option value="">— Pilih Sekolah —</option>
          @foreach($sekolahs as $s)
            <option value="{{ $s->id }}" {{ (int)$defaultSekolahId === $s->id ? 'selected' : '' }}>
              {{ $s->npsn }} — {{ $s->nama }}
            </option>
          @endforeach
        </select>
        @error('default_sekolah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">Sekolah yang akan digunakan sebagai konteks default saat mode Single Sekolah aktif.</div>
      </div>

      <div id="mode-warning" class="alert alert-warning d-none mb-5" role="alert">
        <i class="icon-base ti tabler-alert-triangle me-2"></i>
        <strong>Perhatian!</strong> Mode Single Sekolah akan membatasi akses Admin & Dinas hanya ke 1 sekolah. 
        Data sekolah lain tidak akan terlihat sampai mode dikembalikan ke Multi Sekolah.
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
  const modeCards = document.querySelectorAll('.mode-card');
  const sekolahWrapper = document.getElementById('default-sekolah-wrapper');
  const modeWarning = document.getElementById('mode-warning');

  function updateMode(mode) {
    modeCards.forEach(c => c.classList.toggle('selected', c.dataset.mode === mode));
    if (mode === 'single') {
      sekolahWrapper.classList.remove('d-none');
      modeWarning.classList.remove('d-none');
    } else {
      sekolahWrapper.classList.add('d-none');
      modeWarning.classList.add('d-none');
    }
  }

  modeCards.forEach(card => {
    card.addEventListener('click', function () {
      const radio = this.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = true;
        updateMode(this.dataset.mode);
      }
    });
  });

  updateMode(document.querySelector('input[name="app_mode"]:checked')?.value || 'multi');

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
