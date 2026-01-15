@extends('layouts.app')
@section('title','Prediksi Gizi')

@section('content')
<div class="container-fluid mt-2">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title fw-semibold mb-3">Data Balita & Prediksi</h5>
      <div class="table-responsive">
        <table class="table table-bordered align-middle" style="font-size:14px;">
          <thead class="table-light">
            <tr>
              <th style="width:50px;text-align:center;">No</th>
              <th>Nama Balita</th>
              <th style="width:100px;">Jenis Kelamin</th>
              <th style="width:120px;">Tanggal Lahir</th>
              <th style="width:90px;">Usia (bln)</th>
              <th style="width:90px;">Berat (kg)</th>
              <th style="width:90px;">Tinggi (cm)</th>
              <th style="width:120px;">Status Gizi</th>
              <th>Prediksi Bulan Depan</th>
              <th style="width:80px;text-align:center;">Detail</th>
            </tr>
          </thead>
          <tbody>
            @php
              $today = now();
            @endphp
            @forelse ($balitas as $balita)
              @php
                $usiaBulan = $balita->usia_bulan;
                $bb = $balita->berat;
                $tb = $balita->tinggi;
                $status = $balita->status_gizi;
                $kenaikanBB = match ($status) {
                  'Gizi Kurang' => 0.4,
                  'Gizi Baik' => 0.3,
                  default => 0.2,
                };
                $kenaikanTB = 1;
                $bbPred = is_null($bb) ? '-' : number_format($bb + $kenaikanBB, 1) . ' kg';
                $tbPred = is_null($tb) ? '-' : number_format($tb + $kenaikanTB, 1) . ' cm';
                $usiaPred = is_null($usiaBulan) ? '-' : ($usiaBulan + 1) . ' bln';
                $statusPred = $status ?: '-';
                if ($status === 'Gizi Kurang' && $bb !== null && $kenaikanBB < 0.3) {
                    $statusPred = 'Gizi Kurang';
                } elseif ($status === 'Gizi Lebih' && $bb !== null && $kenaikanBB > 0.3) {
                    $statusPred = 'Gizi Lebih';
                } elseif (!$status) {
                    $statusPred = '-';
                }
                $prediksiText = $statusPred . ' (BB: ' . $bbPred . ', TB: ' . $tbPred . ', Usia: ' . $usiaPred . ')';

                $badgeClass = 'secondary';
                if ($status === 'Gizi Baik') {
                    $badgeClass = 'success';
                } elseif ($status === 'Gizi Kurang') {
                    $badgeClass = 'warning';
                } elseif ($status === 'Gizi Buruk') {
                    $badgeClass = 'danger';
                } elseif ($status === 'Gizi Lebih') {
                    $badgeClass = 'primary';
                }
              @endphp
              <tr>
                <td style="text-align:center;">{{ $loop->iteration }}</td>
                <td>{{ $balita->nama }}</td>
                <td>{{ $balita->jenis_kelamin }}</td>
                <td>{{ optional($balita->tanggal_lahir)->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $usiaBulan ?? '-' }}</td>
                <td>{{ $bb !== null ? number_format($bb, 1) : '-' }}</td>
                <td>{{ $tb !== null ? number_format($tb, 1) : '-' }}</td>
                <td>
                  @if ($status)
                    <span class="badge bg-{{ $badgeClass }}">{{ $status }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>{{ $prediksiText }}</td>
                <td class="text-center">
                  <button type="button"
                    class="btn btn-sm btn-outline-secondary"
                    data-bs-toggle="modal"
                    data-bs-target="#detailModal"
                    data-nama="{{ $balita->nama }}"
                    data-nik="{{ $balita->nik }}"
                    data-ayah="{{ $balita->nama_ayah }}"
                    data-ibu="{{ $balita->nama_ibu }}"
                    data-jk="{{ $balita->jenis_kelamin }}"
                    data-tanggal-lahir="{{ optional($balita->tanggal_lahir)->format('Y-m-d') }}"
                    data-usia="{{ $usiaBulan }}"
                    data-bb="{{ $bb }}"
                    data-tb="{{ $tb }}"
                    data-status="{{ $status }}"
                    data-prediksi="{{ $prediksiText }}"
                    data-tanggal-posyandu="{{ optional($balita->tanggal_posyandu)->format('Y-m-d') }}"
                    data-rekomendasi="{{ $balita->rekomendasi }}">
                    <i class="ti ti-eye"></i>
                  </button>
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
            <tr><th>Prediksi Bulan Depan</th><td data-field="prediksi">-</td></tr>
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
  const humanDate = (value) => {
    if (!value) return '-';
    const ref = new Date(value);
    if (Number.isNaN(ref.getTime())) return '-';
    const day = String(ref.getDate()).padStart(2, '0');
    const month = String(ref.getMonth() + 1).padStart(2, '0');
    return `${day}/${month}/${ref.getFullYear()}`;
  };

  const detailModalEl = document.getElementById('detailModal');
  if (!detailModalEl) return;

  const refs = {
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
    prediksi: detailModalEl.querySelector('[data-field="prediksi"]'),
    rekom: detailModalEl.querySelector('[data-field="rekomendasi"]'),
    rekomRow: detailModalEl.querySelector('[data-field="rekom-row"]'),
  };

  detailModalEl.addEventListener('show.bs.modal', (event) => {
    const data = event.relatedTarget?.dataset || {};
    refs.nama.textContent = data.nama || '-';
    refs.nik.textContent = data.nik || '-';
    refs.ayah.textContent = data.ayah || '-';
    refs.ibu.textContent = data.ibu || '-';
    refs.jk.textContent = data.jk || '-';
    refs.lahir.textContent = humanDate(data.tanggalLahir);
    refs.usia.textContent = data.usia || '-';
    refs.bb.textContent = data.bb ? Number(data.bb).toFixed(1) : '-';
    refs.tb.textContent = data.tb ? Number(data.tb).toFixed(1) : '-';
    refs.status.textContent = data.status || '-';
    refs.posyandu.textContent = humanDate(data.tanggalPosyandu);
    refs.prediksi.textContent = data.prediksi || '-';
    if (data.rekomendasi) {
      refs.rekom.textContent = data.rekomendasi;
      refs.rekomRow.classList.remove('d-none');
    } else {
      refs.rekom.textContent = '-';
      refs.rekomRow.classList.add('d-none');
    }
  });
});
</script>
@endpush
