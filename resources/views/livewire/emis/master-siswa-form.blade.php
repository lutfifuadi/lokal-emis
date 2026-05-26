<div>
  @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
      <i class="icon-base ti tabler-circle-check me-2"></i>
      {{ session('message') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="d-flex align-items-center gap-2 mb-4 text-body-secondary">
    <a href="{{ route('admin.master.siswa') }}" class="text-decoration-none text-body-secondary">
      <i class="icon-base ti tabler-user-check icon-sm me-1"></i>Master Data / Siswa
    </a>
    <i class="icon-base ti tabler-chevron-right icon-sm opacity-50"></i>
    <span class="text-body fw-semibold">{{ $isEdit ? 'Edit' : 'Tambah' }}</span>
  </div>

  <div class="card">
    <div class="card-body">
      <form wire:submit.prevent="save">
        <ul class="nav nav-tabs mb-4" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-pribadi-tab" data-bs-toggle="tab" data-bs-target="#tab-pribadi" type="button" role="tab">
              <i class="icon-base ti tabler-user me-1"></i>Data Pribadi
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-ortu-tab" data-bs-toggle="tab" data-bs-target="#tab-ortu" type="button" role="tab">
              <i class="icon-base ti tabler-users me-1"></i>Data Orang Tua
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-tambahan-tab" data-bs-toggle="tab" data-bs-target="#tab-tambahan" type="button" role="tab">
              <i class="icon-base ti tabler-file-info me-1"></i>Data Tambahan
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-akademik-tab" data-bs-toggle="tab" data-bs-target="#tab-akademik" type="button" role="tab">
              <i class="icon-base ti tabler-school me-1"></i>Akademik & Akun
            </button>
          </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane fade show active" id="tab-pribadi" role="tabpanel">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" wire:model="nama" placeholder="Sesuai Akta Kelahiran">
                  @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" wire:model="nisn" placeholder="Nomor Induk Siswa Nasional">
                  @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="nik" class="form-label">NIK</label>
                  <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" wire:model="nik" placeholder="Nomor Induk Kependudukan">
                  @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" wire:model="tempat_lahir">
                    @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" wire:model="tanggal_lahir">
                    @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" wire:model="jenis_kelamin">
                      <option value="">Pilih...</option>
                      <option value="L">Laki-laki</option>
                      <option value="P">Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="agama" class="form-label">Agama</label>
                    <select class="form-select @error('agama') is-invalid @enderror" id="agama" wire:model="agama">
                      <option value="">Pilih...</option>
                      <option>Islam</option>
                      <option>Kristen</option>
                      <option>Katolik</option>
                      <option>Hindu</option>
                      <option>Buddha</option>
                      <option>Konghucu</option>
                    </select>
                    @error('agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
                <div class="mb-3">
                  <label for="kewarganegaraan" class="form-label">Kewarganegaraan</label>
                  <input type="text" class="form-control @error('kewarganegaraan') is-invalid @enderror" id="kewarganegaraan" wire:model="kewarganegaraan">
                  @error('kewarganegaraan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="alamat" class="form-label">Alamat</label>
                  <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" wire:model="alamat" rows="2" placeholder="Alamat lengkap sesuai KK"></textarea>
                  @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="kode_pos" class="form-label">Kode Pos</label>
                    <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" id="kode_pos" wire:model="kode_pos">
                    @error('kode_pos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="transportasi" class="form-label">Transportasi ke Sekolah</label>
                    <select class="form-select @error('transportasi') is-invalid @enderror" id="transportasi" wire:model="transportasi">
                      <option value="">Pilih...</option>
                      <option>Jalan Kaki</option>
                      <option>Sepeda</option>
                      <option>Motor</option>
                      <option>Mobil</option>
                      <option>Angkutan Umum</option>
                      <option>Antar Jemput</option>
                      <option>Lainnya</option>
                    </select>
                    @error('transportasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="jarak_tempuh" class="form-label">Jarak Tempuh</label>
                    <input type="text" class="form-control @error('jarak_tempuh') is-invalid @enderror" id="jarak_tempuh" wire:model="jarak_tempuh" placeholder="cth: KURANG DARI 5 KM atau 3.5">
                    @error('jarak_tempuh') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label">No. HP Siswa</label>
                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" wire:model="no_hp">
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label for="anak_ke" class="form-label">Anak Ke-</label>
                    <input type="number" min="1" class="form-control @error('anak_ke') is-invalid @enderror" id="anak_ke" wire:model="anak_ke">
                    @error('anak_ke') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-md-4 mb-3">
                    <label for="jml_saudara" class="form-label">Jumlah Saudara</label>
                    <input type="number" min="0" class="form-control @error('jml_saudara') is-invalid @enderror" id="jml_saudara" wire:model="jml_saudara">
                    @error('jml_saudara') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-md-4 mb-3">
                    <label for="kebutuhan_khusus" class="form-label">Kebutuhan Khusus</label>
                    <input type="text" class="form-control @error('kebutuhan_khusus') is-invalid @enderror" id="kebutuhan_khusus" wire:model="kebutuhan_khusus">
                    @error('kebutuhan_khusus') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="tinggi_badan" class="form-label">Tinggi Badan (cm)</label>
                    <input type="number" class="form-control @error('tinggi_badan') is-invalid @enderror" id="tinggi_badan" wire:model="tinggi_badan">
                    @error('tinggi_badan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
                    <input type="number" class="form-control @error('berat_badan') is-invalid @enderror" id="berat_badan" wire:model="berat_badan">
                    @error('berat_badan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="tab-ortu" role="tabpanel">
            <div class="row">
              <div class="col-md-6 border-end">
                <h6 class="fw-bold mb-3 text-primary"><i class="icon-base ti tabler-man me-1"></i> Data Ayah Kandung</h6>
                <div class="mb-3">
                  <label for="nama_ayah" class="form-label">Nama Ayah</label>
                  <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror" id="nama_ayah" wire:model="nama_ayah">
                  @error('nama_ayah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="nik_ayah" class="form-label">NIK Ayah</label>
                  <input type="text" class="form-control @error('nik_ayah') is-invalid @enderror" id="nik_ayah" wire:model="nik_ayah">
                  @error('nik_ayah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="pendidikan_ayah" class="form-label">Pendidikan Terakhir</label>
                  <select class="form-select @error('pendidikan_ayah') is-invalid @enderror" id="pendidikan_ayah" wire:model="pendidikan_ayah">
                    <option value="">Pilih...</option>
                    <option>Tidak Sekolah</option>
                    <option>SD/MI</option>
                    <option>SMP/MTs</option>
                    <option>SMA/MA</option>
                    <option>D1</option><option>D2</option><option>D3</option>
                    <option>S1</option><option>S2</option><option>S3</option>
                  </select>
                  @error('pendidikan_ayah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="pekerjaan_ayah" class="form-label">Pekerjaan</label>
                  <input type="text" class="form-control @error('pekerjaan_ayah') is-invalid @enderror" id="pekerjaan_ayah" wire:model="pekerjaan_ayah">
                  @error('pekerjaan_ayah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="penghasilan_ayah" class="form-label">Penghasilan</label>
                  <select class="form-select @error('penghasilan_ayah') is-invalid @enderror" id="penghasilan_ayah" wire:model="penghasilan_ayah">
                    <option value="">Pilih...</option>
                    <option>< 500.000</option>
                    <option>500.000 - 1.000.000</option>
                    <option>1.000.000 - 2.000.000</option>
                    <option>2.000.000 - 5.000.000</option>
                    <option>> 5.000.000</option>
                  </select>
                  @error('penghasilan_ayah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
              <div class="col-md-6">
                <h6 class="fw-bold mb-3 text-danger"><i class="icon-base ti tabler-woman me-1"></i> Data Ibu Kandung</h6>
                <div class="mb-3">
                  <label for="nama_ibu" class="form-label">Nama Ibu</label>
                  <input type="text" class="form-control @error('nama_ibu') is-invalid @enderror" id="nama_ibu" wire:model="nama_ibu">
                  @error('nama_ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="nik_ibu" class="form-label">NIK Ibu</label>
                  <input type="text" class="form-control @error('nik_ibu') is-invalid @enderror" id="nik_ibu" wire:model="nik_ibu">
                  @error('nik_ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="pendidikan_ibu" class="form-label">Pendidikan Terakhir</label>
                  <select class="form-select @error('pendidikan_ibu') is-invalid @enderror" id="pendidikan_ibu" wire:model="pendidikan_ibu">
                    <option value="">Pilih...</option>
                    <option>Tidak Sekolah</option>
                    <option>SD/MI</option>
                    <option>SMP/MTs</option>
                    <option>SMA/MA</option>
                    <option>D1</option><option>D2</option><option>D3</option>
                    <option>S1</option><option>S2</option><option>S3</option>
                  </select>
                  @error('pendidikan_ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="pekerjaan_ibu" class="form-label">Pekerjaan</label>
                  <input type="text" class="form-control @error('pekerjaan_ibu') is-invalid @enderror" id="pekerjaan_ibu" wire:model="pekerjaan_ibu">
                  @error('pekerjaan_ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="penghasilan_ibu" class="form-label">Penghasilan</label>
                  <select class="form-select @error('penghasilan_ibu') is-invalid @enderror" id="penghasilan_ibu" wire:model="penghasilan_ibu">
                    <option value="">Pilih...</option>
                    <option>< 500.000</option>
                    <option>500.000 - 1.000.000</option>
                    <option>1.000.000 - 2.000.000</option>
                    <option>2.000.000 - 5.000.000</option>
                    <option>> 5.000.000</option>
                  </select>
                  @error('penghasilan_ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <hr>
                <div class="mb-3">
                  <label for="no_hp_ortu" class="form-label">No. HP Orang Tua</label>
                  <input type="text" class="form-control @error('no_hp_ortu') is-invalid @enderror" id="no_hp_ortu" wire:model="no_hp_ortu" placeholder="Nomor yang bisa dihubungi">
                  @error('no_hp_ortu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="tab-tambahan" role="tabpanel">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="no_kk" class="form-label">Nomor Kartu Keluarga</label>
                  <input type="text" class="form-control @error('no_kk') is-invalid @enderror" id="no_kk" wire:model="no_kk">
                  @error('no_kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="nama_kepala_keluarga" class="form-label">Nama Kepala Keluarga</label>
                  <input type="text" class="form-control @error('nama_kepala_keluarga') is-invalid @enderror" id="nama_kepala_keluarga" wire:model="nama_kepala_keluarga">
                  @error('nama_kepala_keluarga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <label for="no_kip" class="form-label">Nomor KIP</label>
                  <input type="text" class="form-control @error('no_kip') is-invalid @enderror" id="no_kip" wire:model="no_kip" placeholder="Kartu Indonesia Pintar">
                  @error('no_kip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="kontak_darurat" class="form-label">Kontak Darurat</label>
                  <input type="text" class="form-control @error('kontak_darurat') is-invalid @enderror" id="kontak_darurat" wire:model="kontak_darurat">
                  @error('kontak_darurat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="tab-akademik" role="tabpanel">
            <div class="row">
              <div class="col-md-6">
                <h6 class="fw-bold mb-3"><i class="icon-base ti tabler-school me-1"></i> Data Akademik</h6>

                @if(!$userSekolahId)
                  <div class="mb-3">
                    <label for="sekolah_id" class="form-label">Sekolah <span class="text-danger">*</span></label>
                    <select class="form-select @error('sekolah_id') is-invalid @enderror" id="sekolah_id" wire:model.live="sekolah_id">
                      <option value="">Pilih Sekolah...</option>
                      @foreach($sekolahs as $sch)
                        <option value="{{ $sch->id }}">{{ $sch->nama }}</option>
                      @endforeach
                    </select>
                    @error('sekolah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                @endif

                <div class="mb-3">
                  <label for="kelas_id" class="form-label">Kelas</label>
                  <select class="form-select @error('kelas_id') is-invalid @enderror" id="kelas_id" wire:model="kelas_id">
                    <option value="">Pilih Kelas...</option>
                    @foreach($kelases as $kls)
                      <option value="{{ $kls->id }}">{{ $kls->nama }} (Tingkat {{ $kls->tingkat }})</option>
                    @endforeach
                  </select>
                  @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                  <label for="status" class="form-label">Status Siswa <span class="text-danger">*</span></label>
                  <select class="form-select @error('status') is-invalid @enderror" id="status" wire:model="status">
                    <option value="aktif">Aktif</option>
                    <option value="lulus">Lulus</option>
                    <option value="pindah">Pindah</option>
                    <option value="keluar">Keluar</option>
                  </select>
                  @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
              <div class="col-md-6">
                <h6 class="fw-bold mb-3"><i class="icon-base ti tabler-lock me-1"></i> Akun Login</h6>

                <div class="mb-3">
                  <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" placeholder="Contoh: siswa@emis.local">
                  @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                  <label for="password" class="form-label">Password {!! !$isEdit ? '<span class="text-danger">*</span>' : '<small class="text-muted">(Kosongkan jika tidak diubah)</small>' !!}</label>
                  <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model="password" placeholder="Minimal 8 karakter">
                  @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
          <button type="button" class="btn btn-outline-secondary" wire:click="cancel">
            <i class="icon-base ti tabler-x me-1"></i> Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-device-floppy me-1"></i>
            {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Siswa' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
