@extends('layouts.app')
@section('title','Data Balita')

@section('content')
@php
    $today = now()->format('Y-m-d');
@endphp
<div class="container-fluid mt-2">
  <div class="card">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="card-title fw-semibold m-0">Data Balita</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
          <i class="ti ti-plus me-1"></i> Tambah Data
        </button>
      </div>

      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Terjadi kesalahan.</strong>
          <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Nama Balita</th>
              <th>Jenis Kelamin</th>
              <th>Tanggal Lahir</th>
              <th>Usia (bln)</th>
              <th>Berat (kg)</th>
              <th>Tinggi (cm)</th>
              <th>Status Gizi</th>
              <th>Tanggal Posyandu</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($balitas as $balita)
              <tr>
                <td>{{ $balitas->firstItem() + $loop->index }}</td>
                <td>{{ $balita->nama }}</td>
                <td>{{ $balita->jenis_kelamin }}</td>
                <td>{{ optional($balita->tanggal_lahir)->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $balita->usia_bulan ?? '-' }}</td>
                <td>{{ $balita->berat ? number_format($balita->berat, 1) : '-' }}</td>
                <td>{{ $balita->tinggi ? number_format($balita->tinggi, 1) : '-' }}</td>
                <td>
                  @php
                    $badgeClass = 'secondary';
                    if ($balita->status_gizi === 'Gizi Baik') $badgeClass = 'success';
                    elseif ($balita->status_gizi === 'Gizi Kurang') $badgeClass = 'warning';
                    elseif ($balita->status_gizi === 'Gizi Buruk') $badgeClass = 'danger';
                    elseif ($balita->status_gizi === 'Gizi Lebih') $badgeClass = 'primary';
                  @endphp
                  @if ($balita->status_gizi)
                    <span class="badge bg-{{ $badgeClass }}">{{ $balita->status_gizi }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>{{ optional($balita->tanggal_posyandu)->format('d/m/Y') ?? '-' }}</td>
                <td>
                  <div class="d-flex justify-content-center align-items-center gap-2">
                    <button type="button"
                      class="btn btn-sm btn-outline-secondary"
                      data-bs-toggle="modal"
                      data-bs-target="#detailModal"
                      data-nama="{{ $balita->nama }}"
                      data-nik="{{ $balita->nik }}"
                      data-jenis-kelamin="{{ $balita->jenis_kelamin }}"
                      data-tanggal-lahir="{{ optional($balita->tanggal_lahir)->format('Y-m-d') }}"
                      data-tanggal-posyandu="{{ optional($balita->tanggal_posyandu)->format('Y-m-d') }}"
                      data-usia="{{ $balita->usia_bulan }}"
                      data-berat="{{ $balita->berat }}"
                      data-tinggi="{{ $balita->tinggi }}"
                      data-ayah="{{ $balita->nama_ayah }}"
                      data-ibu="{{ $balita->nama_ibu }}"
                      data-status="{{ $balita->status_gizi }}"
                      data-rekomendasi="{{ $balita->rekomendasi }}">
                      <i class="ti ti-eye"></i>
                    </button>
                    <button type="button"
                      class="btn btn-sm btn-outline-primary"
                      data-bs-toggle="modal"
                      data-bs-target="#editModal"
                      data-action="{{ route('data-balita.update', $balita) }}"
                      data-nik="{{ $balita->nik }}"
                      data-nama="{{ $balita->nama }}"
                      data-jenis-kelamin="{{ $balita->jenis_kelamin }}"
                      data-tanggal-lahir="{{ optional($balita->tanggal_lahir)->format('Y-m-d') }}"
                      data-tanggal-posyandu="{{ optional($balita->tanggal_posyandu)->format('Y-m-d') }}"
                      data-berat="{{ $balita->berat }}"
                      data-tinggi="{{ $balita->tinggi }}"
                      data-ayah="{{ $balita->nama_ayah }}"
                      data-ibu="{{ $balita->nama_ibu }}">
                      <i class="ti ti-edit"></i>
                    </button>
                    <form action="{{ route('data-balita.destroy', $balita) }}" method="POST" onsubmit="return confirm('Yakin menghapus data {{ $balita->nama }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="ti ti-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center text-muted">Belum ada data balita.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if (method_exists($balitas, 'links'))
        <div class="mt-3">
          {{ $balitas->withQueryString()->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Balita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('data-balita.store') }}" method="POST" id="createBalitaForm">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">NIK Balita</label>
              <input type="text" name="nik" id="createNik" class="form-control" maxlength="16" pattern="\d{16}" inputmode="numeric" value="{{ old('nik') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Balita</label>
              <input type="text" name="nama" id="createNama" class="form-control" value="{{ old('nama') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Jenis Kelamin</label>
              <select name="jenis_kelamin" id="createJK" class="form-select" required>
                <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih jenis kelamin</option>
                <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" id="createTanggal" class="form-control" max="{{ $today }}" value="{{ old('tanggal_lahir') }}" required>
            </div>
            <div class="col-md-12 row g-2 align-items-end">
              <div class="col-md-8">
                <label class="form-label d-flex justify-content-between align-items-center">
                  <span>Tanggal Posyandu</span>
                  <button type="button" class="btn btn-sm btn-outline-secondary" data-action="set-today" data-target="#createTglPosyandu">Hari Ini</button>
                </label>
                <input type="date" name="tanggal_posyandu" id="createTglPosyandu" class="form-control" max="{{ $today }}" value="{{ old('tanggal_posyandu', $today) }}" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Usia (bln)</label>
                <input type="text" class="form-control" id="createUmur" value="{{ old('tanggal_lahir') ? now()->diffInMonths(\Carbon\Carbon::parse(old('tanggal_lahir'))) : '' }}" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Berat (kg)</label>
              <input type="number" step="0.1" min="0" name="berat" id="createBB" class="form-control" value="{{ old('berat') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Tinggi (cm)</label>
              <input type="number" step="0.1" min="0" name="tinggi" id="createTB" class="form-control" value="{{ old('tinggi') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Ayah</label>
              <input type="text" name="nama_ayah" id="createAyah" class="form-control" value="{{ old('nama_ayah') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Ibu</label>
              <input type="text" name="nama_ibu" id="createIbu" class="form-control" value="{{ old('nama_ibu') }}">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="createBalitaForm">Simpan</button>
      </div>
    </div>
  </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Balita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="#" method="POST" id="editBalitaForm">
          @csrf
          @method('PUT')
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">NIK Balita</label>
              <input type="text" name="nik" id="editNik" class="form-control" maxlength="16" pattern="\d{16}" inputmode="numeric" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Balita</label>
              <input type="text" name="nama" id="editNama" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Jenis Kelamin</label>
              <select name="jenis_kelamin" id="editJK" class="form-select" required>
                <option value="" disabled>Pilih jenis kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" id="editTanggal" class="form-control" max="{{ $today }}" required>
            </div>
            <div class="col-md-12 row g-2 align-items-end">
              <div class="col-md-8">
                <label class="form-label d-flex justify-content-between align-items-center">
                  <span>Tanggal Posyandu</span>
                  <button type="button" class="btn btn-sm btn-outline-secondary" data-action="set-today" data-target="#editTglPosyandu">Hari Ini</button>
                </label>
                <input type="date" name="tanggal_posyandu" id="editTglPosyandu" class="form-control" max="{{ $today }}" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Usia (bln)</label>
                <input type="text" class="form-control" id="editUmur" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Berat (kg)</label>
              <input type="number" step="0.1" min="0" name="berat" id="editBB" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Tinggi (cm)</label>
              <input type="number" step="0.1" min="0" name="tinggi" id="editTB" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Ayah</label>
              <input type="text" name="nama_ayah" id="editAyah" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Ibu</label>
              <input type="text" name="nama_ibu" id="editIbu" class="form-control">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="editBalitaForm">Simpan</button>
      </div>
    </div>
  </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Data Balita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm">
            <tr><th style="width:200px;">Nama Balita</th><td data-field="nama">-</td></tr>
            <tr><th>NIK Balita</th><td data-field="nik">-</td></tr>
            <tr><th>Nama Ayah</th><td data-field="ayah">-</td></tr>
            <tr><th>Nama Ibu</th><td data-field="ibu">-</td></tr>
            <tr><th>Jenis Kelamin</th><td data-field="jk">-</td></tr>
            <tr><th>Tanggal Lahir</th><td data-field="lahir">-</td></tr>
            <tr><th>Usia (bln)</th><td data-field="usia">-</td></tr>
            <tr><th>Berat (kg)</th><td data-field="bb">-</td></tr>
            <tr><th>Tinggi (cm)</th><td data-field="tb">-</td></tr>
            <tr><th>Status Gizi</th><td data-field="status">-</td></tr>
            <tr><th>Tanggal Posyandu</th><td data-field="posyandu">-</td></tr>
            <tr data-field="rekom-row"><th>Rekomendasi</th><td data-field="rekomendasi">-</td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const computeMonths = (value) => {
    if (!value) return '';
    const ref = new Date(value);
    if (Number.isNaN(ref.getTime())) return '';
    const today = new Date();
    let months = (today.getFullYear() - ref.getFullYear()) * 12 + (today.getMonth() - ref.getMonth());
    if (today.getDate() < ref.getDate()) months -= 1;
    return months < 0 ? 0 : months;
  };

  const toHuman = (value) => {
    if (!value) return '-';
    const ref = new Date(value);
    if (Number.isNaN(ref.getTime())) return '-';
    const day = String(ref.getDate()).padStart(2, '0');
    const month = String(ref.getMonth() + 1).padStart(2, '0');
    return `${day}/${month}/${ref.getFullYear()}`;
  };

  const createTanggal = document.getElementById('createTanggal');
  const createUmur = document.getElementById('createUmur');
  if (createTanggal && createUmur) {
    createTanggal.addEventListener('input', () => {
      createUmur.value = computeMonths(createTanggal.value);
    });
    if (createTanggal.value) {
      createUmur.value = computeMonths(createTanggal.value);
    }
  }

  const editModalEl = document.getElementById('editModal');
  if (editModalEl) {
    editModalEl.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;
      if (!button) return;
      const form = document.getElementById('editBalitaForm');
      form.action = button.getAttribute('data-action');
      document.getElementById('editNik').value = button.dataset.nik || '';
      document.getElementById('editNama').value = button.dataset.nama || '';
      document.getElementById('editJK').value = button.dataset.jenisKelamin || '';
      document.getElementById('editTanggal').value = button.dataset.tanggalLahir || '';
      document.getElementById('editTglPosyandu').value = button.dataset.tanggalPosyandu || '';
      document.getElementById('editBB').value = button.dataset.berat || '';
      document.getElementById('editTB').value = button.dataset.tinggi || '';
      document.getElementById('editAyah').value = button.dataset.ayah || '';
      document.getElementById('editIbu').value = button.dataset.ibu || '';
      document.getElementById('editUmur').value = computeMonths(button.dataset.tanggalLahir);
    });

    const editTanggal = document.getElementById('editTanggal');
    const editUmur = document.getElementById('editUmur');
    if (editTanggal && editUmur) {
      editTanggal.addEventListener('input', () => {
        editUmur.value = computeMonths(editTanggal.value);
      });
    }
  }

  const detailModalEl = document.getElementById('detailModal');
  if (detailModalEl) {
    const fieldMap = {
      nama: detailModalEl.querySelector('[data-field="nama"]'),
      nik: detailModalEl.querySelector('[data-field="nik"]'),
      ayah: detailModalEl.querySelector('[data-field="ayah"]'),
      ibu: detailModalEl.querySelector('[data-field="ibu"]'),
      jk: detailModalEl.querySelector('[data-field="jk"]'),
      lahir: detailModalEl.querySelector('[data-field="lahir"]'),
      usia: detailModalEl.querySelector('[data-field="usia"]'),
      bb: detailModalEl.querySelector('[data-field="bb"]'),
      tb: detailModalEl.querySelector('[data-field="tb"]'),
      status: detailModalEl.querySelector('[data-field="status"]'),
      posyandu: detailModalEl.querySelector('[data-field="posyandu"]'),
      rekomendasi: detailModalEl.querySelector('[data-field="rekomendasi"]'),
      rekomRow: detailModalEl.querySelector('[data-field="rekom-row"]'),
    };

    detailModalEl.addEventListener('show.bs.modal', (event) => {
      const data = event.relatedTarget?.dataset || {};
      fieldMap.nama.textContent = data.nama || '-';
      fieldMap.nik.textContent = data.nik || '-';
      fieldMap.ayah.textContent = data.ayah || '-';
      fieldMap.ibu.textContent = data.ibu || '-';
      fieldMap.jk.textContent = data.jenisKelamin || '-';
      fieldMap.lahir.textContent = toHuman(data.tanggalLahir);
      fieldMap.usia.textContent = data.usia || '-';
      fieldMap.bb.textContent = data.berat || '-';
      fieldMap.tb.textContent = data.tinggi || '-';
      fieldMap.status.textContent = data.status || '-';
      fieldMap.posyandu.textContent = toHuman(data.tanggalPosyandu);
      if (data.rekomendasi) {
        fieldMap.rekomendasi.textContent = data.rekomendasi;
        fieldMap.rekomRow.classList.remove('d-none');
      } else {
        fieldMap.rekomendasi.textContent = '-';
        fieldMap.rekomRow.classList.add('d-none');
      }
    });
  }

  document.querySelectorAll('[data-action="set-today"]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const selector = btn.getAttribute('data-target');
      const target = selector ? document.querySelector(selector) : null;
      if (!target) return;
      const today = new Date().toISOString().slice(0, 10);
      target.value = today;
      target.dispatchEvent(new Event('input'));
    });
  });
});
</script>
@endpush
