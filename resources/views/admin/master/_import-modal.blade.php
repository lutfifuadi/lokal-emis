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
$modalId = 'importModal-' . str_replace('_', '-', $entity);
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-bold">
          <i class="icon-base ti tabler-file-import me-2"></i>Import Data {{ $label }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="importForm-{{ $entity }}" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="icon-base ti tabler-info-circle me-2 fs-5 flex-shrink-0"></i>
            <div>
              Format file: <strong>.xlsx, .xls, .csv</strong> | Maksimal: <strong>5 MB</strong>
              <br>
              <a href="{{ route('admin.master.import.sample', $entity) }}" class="alert-link">Download sample CSV</a> untuk melihat format yang benar.
            </div>
          </div>

          <div class="mb-3">
            <label for="file-{{ $entity }}" class="form-label">Pilih File <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="file-{{ $entity }}" name="file" accept=".xlsx,.xls,.csv" required>
          </div>

          <div id="importProgress-{{ $entity }}" class="d-none mt-3">
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted">Mengimport data...</span>
              <span class="text-muted" id="importStatus-{{ $entity }}">0%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="progressBar-{{ $entity }}" style="width: 0%"></div>
            </div>
          </div>

          <div id="importResult-{{ $entity }}" class="d-none mt-3"></div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success" id="importBtn-{{ $entity }}">
            <i class="icon-base ti tabler-upload me-1"></i> Import
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('importForm-{{ $entity }}');
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const fileInput = document.getElementById('file-{{ $entity }}');
    if (!fileInput.files || !fileInput.files[0]) return;

    const formData = new FormData();
    formData.append('entity', '{{ $entity }}');
    formData.append('file', fileInput.files[0]);

    const progressEl = document.getElementById('importProgress-{{ $entity }}');
    const resultEl = document.getElementById('importResult-{{ $entity }}');
    const progressBar = document.getElementById('progressBar-{{ $entity }}');
    const statusEl = document.getElementById('importStatus-{{ $entity }}');
    const btn = document.getElementById('importBtn-{{ $entity }}');

    progressEl.classList.remove('d-none');
    resultEl.classList.add('d-none');
    resultEl.innerHTML = '';
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengimport...';
    progressBar.style.width = '50%';
    statusEl.textContent = 'Mengupload...';

    fetch('{{ route("admin.master.import") }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      progressBar.style.width = '100%';
      statusEl.textContent = 'Selesai';

      setTimeout(() => {
        resultEl.classList.remove('d-none');

        if (data.success) {
          let html = '<div class="alert alert-success">';
          html += '<i class="icon-base ti tabler-circle-check me-1"></i> ' + data.message + '<br>';
          html += '<strong>' + (data.created_count || 0) + '</strong> baru, <strong>' + (data.updated_count || 0) + '</strong> diperbarui';
          if (data.error_count > 0) {
            html += ', <span class="text-warning fw-bold">' + data.error_count + '</span> gagal';
          }
          html += '</div>';

          if (data.errors && data.errors.length > 0) {
            html += '<div class="alert alert-warning mt-2 mb-0"><strong>Detail Error:</strong><ul class="mb-0 mt-1 ps-3">';
            data.errors.forEach(function(err) {
              html += '<li>Baris ' + err.row + ' - ' + (err.field || '') + ': ' + (err.errors ? err.errors.join(', ') : '') + '</li>';
            });
            html += '</ul></div>';
          }

          resultEl.innerHTML = html;

          setTimeout(function() {
            location.reload();
          }, 2500);
        } else {
          resultEl.innerHTML = '<div class="alert alert-danger"><i class="icon-base ti tabler-alert-circle me-1"></i> ' + (data.message || 'Gagal mengimport data') + '</div>';
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="icon-base ti tabler-upload me-1"></i> Import';
      }, 500);
    })
    .catch(function(error) {
      resultEl.classList.remove('d-none');
      resultEl.innerHTML = '<div class="alert alert-danger"><i class="icon-base ti tabler-alert-circle me-1"></i> Gagal: ' + error.message + '</div>';
      btn.disabled = false;
      btn.innerHTML = '<i class="icon-base ti tabler-upload me-1"></i> Import';
    });
  });
});
</script>
