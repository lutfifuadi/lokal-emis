@php
$entityLabels = [
    'sekolah' => 'Sekolah',
    'jurusan' => 'Jurusan',
    'tahun-ajaran' => 'Tahun Ajaran',
    'kelas' => 'Kelas',
    'users' => 'Pengguna',
    'siswa' => 'Siswa',
];
$label = $entityLabels[$entity] ?? ucfirst($entity);
$modalId = 'syncModal-' . str_replace('_', '-', $entity);
$setting = \App\Models\GoogleSheetSetting::where('entity', $entity)->first();
@endphp

@if($setting && $setting->is_active)
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-bold">
          <i class="icon-base ti tabler-file-spreadsheet me-2"></i>Sinkronisasi Data {{ $label }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info d-flex align-items-center" role="alert">
          <i class="icon-base ti tabler-info-circle me-2 fs-5 flex-shrink-0"></i>
          <div>
            <strong>Spreadsheet:</strong>
            <a href="{{ $setting->spreadsheet_url }}" target="_blank" class="alert-link">Buka Sheet <i class="icon-base ti tabler-external-link"></i></a>
            <br>
            <strong>Sheet:</strong> {{ $setting->sheet_name }} | <strong>Range:</strong> {{ $setting->sheet_range }}
            @if($setting->last_test_ok)
              <br><span class="text-success"><i class="icon-base ti tabler-circle-check"></i> Koneksi terverifikasi</span>
            @endif
          </div>
        </div>

        <div class="row g-3">
          <div class="col-sm-6">
            <div class="border rounded-3 p-3 text-center sync-option" data-direction="import" style="cursor: pointer;">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="syncDirection-{{ $entity }}" id="import-{{ $entity }}" value="import" checked>
                <label class="form-check-label fw-medium" for="import-{{ $entity }}">
                  <i class="icon-base ti tabler-download d-block fs-1 mb-1"></i>
                  Import dari Sheet
                </label>
              </div>
              <small class="text-muted">Ambil data dari Google Sheet dan sinkronkan ke database</small>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="border rounded-3 p-3 text-center sync-option" data-direction="export" style="cursor: pointer;">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="syncDirection-{{ $entity }}" id="export-{{ $entity }}" value="export">
                <label class="form-check-label fw-medium" for="export-{{ $entity }}">
                  <i class="icon-base ti tabler-upload d-block fs-1 mb-1"></i>
                  Export ke Sheet
                </label>
              </div>
              <small class="text-muted">Kirim data dari database ke Google Sheet</small>
            </div>
          </div>
        </div>

        <div id="syncProgress-{{ $entity }}" class="d-none mt-3">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted" id="syncStatusText-{{ $entity }}">Memproses...</span>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" id="syncProgressBar-{{ $entity }}" style="width: 50%"></div>
          </div>
        </div>

        <div id="syncResult-{{ $entity }}" class="d-none mt-3"></div>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-info" id="syncBtn-{{ $entity }}">
          <i class="icon-base ti tabler-refresh me-1"></i> Mulai Sinkronisasi
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('{{ $modalId }}');
  if (!modal) return;

  modal.querySelectorAll('.sync-option').forEach(function(opt) {
    opt.addEventListener('click', function() {
      const radio = this.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });

  const syncBtn = document.getElementById('syncBtn-{{ $entity }}');
  syncBtn.addEventListener('click', function() {
    const direction = document.querySelector('input[name="syncDirection-{{ $entity }}"]:checked').value;
    const progressEl = document.getElementById('syncProgress-{{ $entity }}');
    const resultEl = document.getElementById('syncResult-{{ $entity }}');
    const progressBar = document.getElementById('syncProgressBar-{{ $entity }}');
    const statusText = document.getElementById('syncStatusText-{{ $entity }}');

    progressEl.classList.remove('d-none');
    resultEl.classList.add('d-none');
    resultEl.innerHTML = '';
    syncBtn.disabled = true;
    syncBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyinkronkan...';
    progressBar.style.width = '60%';
    statusText.textContent = direction === 'import' ? 'Mengimport data dari Google Sheet...' : 'Mengexport data ke Google Sheet...';

    fetch('{{ route("admin.master.google-sheet-sync", $entity) }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ direction: direction })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      progressBar.style.width = '100%';
      statusText.textContent = 'Selesai';

      setTimeout(function() {
        resultEl.classList.remove('d-none');

        if (data.success) {
          let html = '<div class="alert alert-success mb-0">';
          html += '<i class="icon-base ti tabler-circle-check me-1"></i> ' + data.message + '<br>';
          if (data.background) {
            html += '<small class="text-muted d-block mt-1"><i class="icon-base ti tabler-clock me-1"></i> Tugas telah ditambahkan ke antrean. Halaman akan dimuat ulang untuk memperbarui info.</small>';
          } else if (data.data) {
            if (data.data.imported !== undefined) {
              html += '<strong>' + data.data.imported + '</strong> baru, <strong>' + data.data.updated + '</strong> diperbarui';
            }
            if (data.data.exported !== undefined) {
              html += '<strong>' + data.data.exported + '</strong> baris data dikirim';
            }
            if (data.data.errors && data.data.errors.length > 0) {
              html += ', <span class="text-warning fw-bold">' + data.data.errors.length + '</span> error';
            }
          }
          html += '</div>';

          if (data.data && data.data.errors && data.data.errors.length > 0) {
            html += '<div class="alert alert-warning mt-2 mb-0"><strong>Detail Error:</strong><ul class="mb-0 mt-1 ps-3">';
            data.data.errors.forEach(function(err) {
              html += '<li>' + err + '</li>';
            });
            html += '</ul></div>';
          }

          resultEl.innerHTML = html;

          setTimeout(function() {
            location.reload();
          }, data.background ? 3500 : 2500);
        } else {
          resultEl.innerHTML = '<div class="alert alert-danger"><i class="icon-base ti tabler-alert-circle me-1"></i> ' + (data.message || 'Gagal sinkronisasi') + '</div>';
        }

        syncBtn.disabled = false;
        syncBtn.innerHTML = '<i class="icon-base ti tabler-refresh me-1"></i> Mulai Sinkronisasi';
      }, 500);
    })
    .catch(function(error) {
      resultEl.classList.remove('d-none');
      resultEl.innerHTML = '<div class="alert alert-danger"><i class="icon-base ti tabler-alert-circle me-1"></i> Gagal: ' + error.message + '</div>';
      syncBtn.disabled = false;
      syncBtn.innerHTML = '<i class="icon-base ti tabler-refresh me-1"></i> Mulai Sinkronisasi';
    });
  });
});
</script>
@endif
