@extends('layouts/layoutMaster')

@section('title', isset($setting) ? 'Edit Konfigurasi Google Sheet' : 'Tambah Konfigurasi Google Sheet')

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

  /* ─── Form card styling ─── */
  .form-card { border-radius: var(--emis-radius); overflow: hidden; }
  .form-card .card-header {
    background: linear-gradient(135deg, var(--emis-navy) 0%, var(--emis-navy-mid) 100%) !important;
    padding: 1.75rem 2rem !important;
    position: relative;
    overflow: hidden;
  }
  .form-card .card-header::before {
    content:'';
    position:absolute; inset:0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.07'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }

  /* ─── Section labels ─── */
  .section-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 2rem 0 1.25rem;
  }
  .section-divider .section-icon {
    width: 36px; height: 36px;
    border-radius: var(--emis-radius-sm);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .section-divider .section-line {
    flex-grow: 1;
    height: 1px;
    background: linear-gradient(to right, rgba(15,31,61,.3), transparent);
  }

  /* ─── Credentials textarea ─── */
  #credentials_json {
    font-family: 'Courier New', monospace;
    font-size: .8rem;
    line-height: 1.6;
    background: #1e1e2e;
    color: #cdd6f4;
    border-color: #373753;
    border-radius: var(--emis-radius-sm);
    resize: vertical;
  }
  #credentials_json:focus {
    background: #1e1e2e;
    color: #cdd6f4;
    border-color: var(--emis-navy-mid);
    box-shadow: 0 0 0 3px rgba(15,31,61,.25);
  }
  #credentials_json::placeholder { color: #6c7086; }

  /* ─── JSON validate indicator ─── */
  .json-indicator {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .75rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: var(--emis-radius-sm);
    transition: all .2s;
  }
  .json-indicator.valid   { background: var(--emis-emerald-lt); color: var(--emis-emerald); }
  .json-indicator.invalid { background: var(--emis-rose-lt); color: var(--emis-rose); }
  .json-indicator.empty   { background: var(--emis-surface); color: var(--emis-slate); border: 1px solid var(--emis-border); }

  /* ─── Active toggle ─── */
  .form-check-input { cursor: pointer; }
  .active-toggle-wrap {
    background: var(--bs-body-bg);
    border: 1.5px solid var(--bs-border-color);
    border-radius: var(--emis-radius-sm);
    padding: 1rem 1.25rem;
    transition: border-color .2s;
  }
  .active-toggle-wrap:has(input:checked) { border-color: var(--emis-emerald); background: var(--emis-emerald-lt); }

  /* ─── Input focus ─── */
  .form-control:focus, .form-select:focus {
    border-color: var(--emis-navy-mid);
    box-shadow: 0 0 0 3px rgba(15,31,61,.18);
  }

  /* ─── Paste btn ─── */
  .paste-btn {
    position: absolute;
    top: 10px; right: 10px;
    z-index: 5;
  }
</style>
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="d-flex align-items-center gap-2 mb-4 text-body-secondary">
  <a href="{{ route('admin.master.google-sheet-settings.index') }}" class="text-decoration-none text-body-secondary">
    <i class="icon-base ti tabler-file-spreadsheet icon-sm me-1"></i>Google Sheet
  </a>
  <i class="icon-base ti tabler-chevron-right icon-sm opacity-50"></i>
  <span class="text-body fw-semibold">{{ isset($setting) ? 'Edit — ' . ($entities[$setting->entity] ?? $setting->entity) : 'Tambah Konfigurasi Baru' }}</span>
</div>

<div class="row g-5">

  {{-- ════════════════════════════════ --}}
  {{-- MAIN FORM                        --}}
  {{-- ════════════════════════════════ --}}
  <div class="col-xl-8">
    <div class="card form-card">

      {{-- Gradient header --}}
      <div class="card-header position-relative z-1">
        <div class="d-flex align-items-center gap-3">
          <div class="p-2" style="background:rgba(255,255,255,.2); border-radius: 5px;">
            <i class="icon-base ti tabler-file-spreadsheet text-white" style="font-size:1.5rem;"></i>
          </div>
          <div>
            <h5 class="text-white mb-0 fw-bold">
              {{ isset($setting) ? 'Edit Konfigurasi' : 'Konfigurasi Baru' }}
            </h5>
            <p class="text-white opacity-75 mb-0 small">
              {{ isset($setting) ? 'Perbarui pengaturan sinkronisasi untuk entity ini' : 'Hubungkan entity EMIS ke Google Sheets' }}
            </p>
          </div>
        </div>
      </div>

      <div class="card-body p-4 p-xl-5">
        <form
          id="gs-form"
          action="{{ isset($setting) ? route('admin.master.google-sheet-settings.update', $setting->id) : route('admin.master.google-sheet-settings.store') }}"
          method="POST">
          @csrf
          @if(isset($setting)) @method('PUT') @endif

          {{-- ── SECTION 1: Entity & Status ── --}}
          <div class="section-divider">
            <div class="section-icon bg-label-primary">
              <i class="icon-base ti tabler-database text-primary icon-sm"></i>
            </div>
            <h6 class="mb-0 fw-bold text-heading">Entity & Status</h6>
            <div class="section-line"></div>
          </div>

          <div class="row g-4">
            <div class="col-md-7">
              <label class="form-label fw-semibold" for="entity">
                Entity Master Data <span class="text-danger">*</span>
              </label>
              @if(isset($setting))
                <div class="d-flex align-items-center gap-2">
                  <input type="text" class="form-control bg-body-secondary" value="{{ $entities[$setting->entity] ?? $setting->entity }}" disabled>
                  <input type="hidden" name="entity" value="{{ $setting->entity }}">
                </div>
                <div class="form-text">Entity tidak dapat diubah setelah dikonfigurasi.</div>
              @else
                <select class="form-select @error('entity') is-invalid @enderror" id="entity" name="entity" required>
                  <option value="">— Pilih Entity —</option>
                  @foreach($availableEntities as $key => $label)
                    <option value="{{ $key }}" {{ old('entity', request('entity')) == $key ? 'selected' : '' }}>
                      {{ $label }}
                    </option>
                  @endforeach
                </select>
                @error('entity') <div class="invalid-feedback">{{ $message }}</div> @enderror
              @endif
            </div>

            <div class="col-md-5">
              <label class="form-label fw-semibold">Status Sinkronisasi</label>
              <div class="active-toggle-wrap">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <div class="fw-semibold small text-heading">Aktifkan Sync</div>
                    <div class="text-muted" style="font-size:.75rem;">Izinkan sinkronisasi data</div>
                  </div>
                  <div class="form-check form-switch mb-0">
                    <input type="checkbox"
                      class="form-check-input"
                      id="is_active"
                      name="is_active"
                      value="1"
                      role="switch"
                      {{ old('is_active', $setting->is_active ?? false) ? 'checked' : '' }}
                      style="width:2.5rem; height:1.3rem;">
                    <label class="form-check-label visually-hidden" for="is_active">Aktif</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ── SECTION 2: Spreadsheet Info ── --}}
          <div class="section-divider">
            <div class="section-icon bg-label-success">
              <i class="icon-base ti tabler-table text-success icon-sm"></i>
            </div>
            <h6 class="mb-0 fw-bold text-heading">Informasi Spreadsheet</h6>
            <div class="section-line"></div>
          </div>

          <div class="row g-4">
            <div class="col-12">
              <label class="form-label fw-semibold" for="spreadsheet_url">
                URL Spreadsheet <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-body-secondary">
                  <i class="icon-base ti tabler-link text-muted icon-sm"></i>
                </span>
                <input
                  type="url"
                  class="form-control @error('spreadsheet_url') is-invalid @enderror"
                  id="spreadsheet_url"
                  name="spreadsheet_url"
                  value="{{ old('spreadsheet_url', $setting->spreadsheet_url ?? '') }}"
                  placeholder="https://docs.google.com/spreadsheets/d/..."
                  required>
                @error('spreadsheet_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-text">URL lengkap dari Google Sheet Anda. Bisa diperoleh dari address bar browser saat membuka sheet.</div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold" for="sheet_name">
                Nama Sheet Tab <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-body-secondary">
                  <i class="icon-base ti tabler-file-spreadsheet text-muted icon-sm"></i>
                </span>
                <input
                  type="text"
                  class="form-control @error('sheet_name') is-invalid @enderror"
                  id="sheet_name"
                  name="sheet_name"
                  value="{{ old('sheet_name', $setting->sheet_name ?? 'Sheet1') }}"
                  placeholder="Sheet1"
                  required>
                @error('sheet_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-text">Nama tab di bagian bawah spreadsheet (misal: <code>Data Siswa</code>).</div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold" for="sheet_range">
                Range Kolom <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-body-secondary">
                  <i class="icon-base ti tabler-layout-columns text-muted icon-sm"></i>
                </span>
                <input
                  type="text"
                  class="form-control @error('sheet_range') is-invalid @enderror"
                  id="sheet_range"
                  name="sheet_range"
                  value="{{ old('sheet_range', $setting->sheet_range ?? 'A:Z') }}"
                  placeholder="A:Z"
                  required>
                @error('sheet_range') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-text">Gunakan <code class="px-1">A:Z</code> untuk semua kolom, atau <code class="px-1">A:H</code> untuk 8 kolom pertama.</div>
            </div>
          </div>

          {{-- ── SECTION 3: Credentials ── --}}
          <div class="section-divider">
            <div class="section-icon bg-label-danger">
              <i class="icon-base ti tabler-shield-lock text-danger icon-sm"></i>
            </div>
            <h6 class="mb-0 fw-bold text-heading">Credentials JSON</h6>
            <div class="section-line"></div>
          </div>

          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <label class="form-label fw-semibold mb-0" for="credentials_json">
                Service Account JSON
                <span class="text-danger">{{ isset($setting) ? '' : '*' }}</span>
                @if(isset($setting)) <small class="text-muted fw-normal ms-1">(kosongkan jika tidak ingin diubah)</small> @endif
              </label>
              <div id="json-indicator" class="json-indicator empty">
                <i class="icon-base ti tabler-code icon-sm"></i>
                <span>Menunggu input</span>
              </div>
            </div>

            <div class="position-relative">
              <textarea
                class="form-control @error('credentials_json') is-invalid @enderror"
                id="credentials_json"
                name="credentials_json"
                rows="10"
                placeholder='{
  "type": "service_account",
  "project_id": "your-project",
  "private_key_id": "...",
  "private_key": "-----BEGIN RSA PRIVATE KEY-----\n...",
  "client_email": "your-sa@project.iam.gserviceaccount.com",
  ...
}'>{{ old('credentials_json', $setting->credentials_json ?? '') }}</textarea>
              @error('credentials_json') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mt-2 p-3 rounded-2" style="background: rgba(115,103,240,.06); border: 1px dashed rgba(115,103,240,.3);">
              <div class="d-flex align-items-start gap-2">
                <i class="icon-base ti tabler-info-circle text-primary icon-sm mt-1 flex-shrink-0"></i>
                <small class="text-body-secondary lh-base">
                  Download file <code>credentials.json</code> dari <strong>Google Cloud Console → Service Accounts → Keys → Add Key → JSON</strong>.
                  Buka file tersebut dengan text editor, lalu <strong>copy-paste seluruh isinya</strong> ke kolom di atas.
                </small>
              </div>
            </div>
          </div>

          {{-- ── SECTION 4: Column Mapping ── --}}
          <div class="section-divider">
            <div class="section-icon bg-label-info">
              <i class="icon-base ti tabler-columns-3 text-info icon-sm"></i>
            </div>
            <h6 class="mb-0 fw-bold text-heading">Mapping Kolom</h6>
            <div class="section-line"></div>
          </div>

          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <label class="form-label fw-semibold mb-0" for="mapping_config_json">
                Database Field → Header Sheet
              </label>
              <button type="button" class="btn btn-sm btn-outline-info" id="btn-default-mapping">
                <i class="icon-base ti tabler-refresh me-1"></i>Generate Default
              </button>
            </div>

            <textarea
              class="form-control font-monospace @error('mapping_config') is-invalid @enderror"
              id="mapping_config_json"
              name="mapping_config_json"
              rows="10"
              placeholder='{"nisn": "NISN", "nama": "Nama Lengkap", ...}'
              style="font-size:.8rem; line-height:1.6;"
            >{{ old('mapping_config_json', isset($setting) && $setting->mapping_config ? json_encode($setting->mapping_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (isset($defaultMapping) && !empty($defaultMapping) ? json_encode($defaultMapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '')) }}</textarea>
            @error('mapping_config') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">
              Format: <code>{"field_database": "Header di Google Sheet"}</code>. Kiri = nama kolom database, kanan = header baris pertama sheet.
            </div>
          </div>

          {{-- ── FORM ACTIONS ── --}}
          <div class="d-flex align-items-center gap-3 mt-5 pt-4" style="border-top: 1.5px solid var(--bs-border-color);">
            <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" id="submit-btn">
              <i class="icon-base ti tabler-device-floppy me-2"></i>
              {{ isset($setting) ? 'Simpan Perubahan' : 'Simpan Konfigurasi' }}
            </button>
            <a href="{{ route('admin.master.google-sheet-settings.index') }}" class="btn btn-outline-secondary px-4 py-2">
              <i class="icon-base ti tabler-arrow-left me-2"></i>Kembali
            </a>
          </div>

        </form>
      </div>
    </div>
  </div>

  {{-- ════════════════════════════════ --}}
  {{-- SIDEBAR: TIPS & HELP            --}}
  {{-- ════════════════════════════════ --}}
  <div class="col-xl-4">

    {{-- Help Card --}}
    <div class="card mb-4">
      <div class="card-header border-bottom d-flex align-items-center gap-2">
        <div class="p-1_5 bg-label-info" style="border-radius: 5px;">
          <i class="icon-base ti tabler-help-circle text-info icon-sm"></i>
        </div>
        <h6 class="mb-0 fw-bold">Cara Mendapatkan Credentials</h6>
      </div>
      <div class="card-body p-3">
        <ol class="ps-3 mb-0 d-flex flex-column gap-3">
          <li class="small text-body-secondary lh-base">
            Buka <a href="https://console.cloud.google.com" target="_blank" class="fw-semibold">console.cloud.google.com</a>
          </li>
          <li class="small text-body-secondary lh-base">
            Pilih project → <strong>IAM & Admin</strong> → <strong>Service Accounts</strong>
          </li>
          <li class="small text-body-secondary lh-base">
            Klik nama service account → tab <strong>Keys</strong>
          </li>
          <li class="small text-body-secondary lh-base">
            Klik <strong>Add Key → Create new key → JSON</strong>
          </li>
          <li class="small text-body-secondary lh-base">
            File JSON terunduh otomatis. Buka & copy-paste isinya ke form ini.
          </li>
        </ol>
      </div>
    </div>

    {{-- Share Reminder --}}
    <div class="card mb-4" style="background: linear-gradient(135deg,#fff8e1,#fffde7);">
      <div class="card-body p-4">
        <div class="d-flex align-items-center gap-2 mb-2">
          <i class="icon-base ti tabler-alert-triangle text-warning icon-md"></i>
          <h6 class="mb-0 fw-bold text-warning">Jangan Lupa!</h6>
        </div>
        <p class="small text-body-secondary mb-2 lh-base">
          Share spreadsheet ke email service account dengan akses <strong>Editor</strong>:
        </p>
        <div class="p-2 rounded-2 bg-white">
          <code class="small text-body">nama@project.iam.gserviceaccount.com</code>
        </div>
        <p class="small text-muted mt-2 mb-0">Email ini ada di dalam file JSON, pada field <code>client_email</code>.</p>
      </div>
    </div>

    {{-- Format table --}}
    <div class="card" id="sidebar-mapping">
      <div class="card-header border-bottom d-flex align-items-center gap-2">
        <div class="p-1_5 bg-label-success" style="border-radius: 5px;">
          <i class="icon-base ti tabler-table text-success icon-sm"></i>
        </div>
        <h6 class="mb-0 fw-bold">Default Mapping</h6>
      </div>
      <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
        @php
          $entityLabel = isset($setting) ? ($entities[$setting->entity] ?? $setting->entity) : (request('entity') ? ($entities[request('entity')] ?? request('entity')) : 'Entity');
          $defMapping = isset($defaultMapping) && !empty($defaultMapping)
            ? $defaultMapping
            : \App\Models\GoogleSheetSetting::defaultMapping('siswa');
        @endphp
        <p class="small text-body-secondary mb-2">Mapping default field database → header Google Sheet untuk entity <strong>{{ $entityLabel }}</strong>:</p>
        @if(!empty($defMapping))
        <table class="table table-sm small mb-0">
          <thead>
            <tr>
              <th class="text-nowrap">Field DB</th>
              <th class="text-nowrap">Header Sheet</th>
            </tr>
          </thead>
          <tbody>
            @foreach($defMapping as $field => $header)
              <tr>
                <td><code class="fw-normal" style="font-size:.65rem;">{{ $field }}</code></td>
                <td><span class="badge bg-label-info fw-normal" style="font-size:.65rem;">{{ $header }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <p class="small text-muted fst-italic mb-0">Belum ada mapping default untuk entity ini. Silakan isi mapping secara manual atau klik <strong>Generate Default</strong>.</p>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ─── JSON Validator (Credentials) ───
  const ta = document.getElementById('credentials_json');
  const indicator = document.getElementById('json-indicator');

  function validateJSON() {
    const val = ta.value.trim();
    if (!val) {
      indicator.className = 'json-indicator empty';
      indicator.innerHTML = '<i class="icon-base ti tabler-code icon-sm"></i><span>Menunggu input</span>';
      return;
    }
    try {
      const parsed = JSON.parse(val);
      if (parsed.type === 'service_account' && parsed.client_email) {
        indicator.className = 'json-indicator valid';
        indicator.innerHTML = '<i class="icon-base ti tabler-circle-check icon-sm"></i><span>JSON valid ✓</span>';
      } else {
        indicator.className = 'json-indicator valid';
        indicator.innerHTML = '<i class="icon-base ti tabler-check icon-sm"></i><span>JSON valid</span>';
      }
    } catch (e) {
      indicator.className = 'json-indicator invalid';
      indicator.innerHTML = '<i class="icon-base ti tabler-circle-x icon-sm"></i><span>JSON tidak valid</span>';
    }
  }

  if (ta) {
    ta.addEventListener('input', validateJSON);
    validateJSON(); // run on load
  }

  // ─── Mapping Config JSON validation ───
  const mappingTextarea = document.getElementById('mapping_config_json');

  function validateMappingJSON() {
    if (!mappingTextarea) return;
    const val = mappingTextarea.value.trim();
    if (!val) return;
    try {
      JSON.parse(val);
    } catch (e) {
      mappingTextarea.setCustomValidity('JSON tidak valid');
    }
  }

  if (mappingTextarea) {
    mappingTextarea.addEventListener('input', validateMappingJSON);
  }

  // ─── Generate Default Mapping ───
  const btnDefault = document.getElementById('btn-default-mapping');
  const entitySelect = document.getElementById('entity');

  const defaultMappings = {
    @foreach(\App\Models\GoogleSheetSetting::entities() as $key => $label)
      '{{ $key }}': {!! json_encode(\App\Models\GoogleSheetSetting::defaultMapping($key), JSON_UNESCAPED_UNICODE) !!},
    @endforeach
  };

  function applyDefaultMapping(entity) {
    const mapping = defaultMappings[entity] || {};
    if (Object.keys(mapping).length > 0) {
      mappingTextarea.value = JSON.stringify(mapping, null, 2);
    }
  }

  if (btnDefault && mappingTextarea) {
    btnDefault.addEventListener('click', function () {
      const entity = entitySelect ? entitySelect.value : 'siswa';
      applyDefaultMapping(entity || 'siswa');
    });
  }

  // ─── When entity changes, auto-update mapping ───
  if (entitySelect) {
    entitySelect.addEventListener('change', function () {
      applyDefaultMapping(this.value);
    });
  }

  // ─── Submit loading state ───
  const form = document.getElementById('gs-form');
  const submitBtn = document.getElementById('submit-btn');
  if (form && submitBtn) {
    form.addEventListener('submit', function () {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });
  }

});
</script>
@endsection
