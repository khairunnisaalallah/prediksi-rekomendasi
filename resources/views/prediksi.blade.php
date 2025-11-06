@extends('layouts.app')
@section('title','Data Balita')
@section('content')
<div class="container-fluid mt-2">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title fw-semibold mb-3">Data Balita</h5>
      <div class="table-responsive">
  <table class="table table-bordered align-middle" style="font-size:14px;">
    <thead class="table-light">
      <tr>
        <th style="width:50px;text-align:center;">No</th>
        <th>Nama Balita</th>
        <th style="width:60px;">Jenis Kelamin</th>
        <th style="width:110px;">Tanggal Lahir</th>
        <th style="width:80px;">Usia (bln)</th>
        <th style="width:90px;">Berat (kg)</th>
        <th style="width:90px;">Tinggi (cm)</th>
        <th style="width:110px;">Status Gizi</th>
        <th>Prediksi Bulan Depan</th>
        <th style="width:80px;text-align:center;">Aksi</th>
      </tr>
    </thead>
    <tbody id="tabelBalita"></tbody>
  </table>
</div>

    </div>
  </div>
</div>

@push('scripts')
<script>
const dataStatis = [
  { nama: "Dani Ahmad", jk: "Laki-laki", usia: 24, bb: 9.5, tb: 73, status: "Gizi Kurang", rekom: "Tingkatkan asupan protein hewani seperti telur, ikan, ayam.", tanggal: "09/10/2023" },
  { nama: "Rani Lestari", jk: "Perempuan", usia: 30, bb: 11.2, tb: 80, status: "Gizi Baik", rekom: "Pertahankan pola makan seimbang, tambah buah setiap hari.", tanggal: "09/10/2023" }
];

let dataLocal = JSON.parse(localStorage.getItem('dataBalita') || '[]');
let changed = false;
dataLocal = dataLocal.map((d, idx) => { if (!d._id) { d._id = 'l_' + (Date.now() + idx); changed = true; } return d; });
if (changed) localStorage.setItem('dataBalita', JSON.stringify(dataLocal));

const dataBalita = [...dataStatis, ...dataLocal];
const tbody = document.getElementById('tabelBalita');

// ==== Helpers tanggal & usia ====
function parseDDMMYYYY(s){
  if (!s || typeof s !== 'string') return null;
  const m = s.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
  if (!m) return null;
  const d = parseInt(m[1],10), mo = parseInt(m[2],10)-1, y = parseInt(m[3],10);
  const dt = new Date(y,mo,d);
  return (dt.getFullYear()===y && dt.getMonth()===mo && dt.getDate()===d) ? dt : null;
}
function parseYYYYMMDD(s){
  if (!s || typeof s !== 'string') return null;
  const m = s.match(/^(\d{4})-(\d{2})-(\d{2})$/);
  if (!m) return null;
  const y = parseInt(m[1],10), mo = parseInt(m[2],10)-1, d = parseInt(m[3],10);
  const dt = new Date(y,mo,d);
  return (dt.getFullYear()===y && dt.getMonth()===mo && dt.getDate()===d) ? dt : null;
}
function parseFlexibleDate(s){ return parseDDMMYYYY(s) || parseYYYYMMDD(s); }
function formatDDMMYYYY(dt){
  const dd = String(dt.getDate()).padStart(2,'0');
  const mm = String(dt.getMonth()+1).padStart(2,'0');
  const yyyy = dt.getFullYear();
  return `${dd}/${mm}/${yyyy}`;
}
function monthsDiff(from, to){
  let months = (to.getFullYear() - from.getFullYear()) * 12 + (to.getMonth() - from.getMonth());
  if (to.getDate() < from.getDate()) months -= 1;
  return months < 0 ? 0 : months;
}

// 🔮 Fungsi prediksi bulan depan (tahan terhadap data kosong)
function prediksiBulanDepan(item) {
  const status = item.status || '-';
  const bbNum = typeof item.bb === 'number' ? item.bb : null;
  const tbNum = typeof item.tb === 'number' ? item.tb : null;
  const usiaNum = typeof item.usia === 'number' ? item.usia : null;

  const kenaikanBB = status === 'Gizi Kurang' ? 0.4 : status === 'Gizi Baik' ? 0.3 : 0.2;
  const kenaikanTB = 1;
  const bbNext = bbNum != null ? (bbNum + kenaikanBB).toFixed(1) + ' kg' : '-';
  const tbNext = tbNum != null ? (tbNum + kenaikanTB).toFixed(1) + ' cm' : '-';
  const usiaNext = usiaNum != null ? (usiaNum + 1) + ' bln' : '-';

  let statusNext = status;
  if (status === 'Gizi Kurang' && bbNum != null && kenaikanBB < 0.3) statusNext = 'Gizi Kurang';
  else if (status === 'Gizi Lebih' && bbNum != null && kenaikanBB > 0.3) statusNext = 'Gizi Lebih';
  else if (!status || status === '-') statusNext = '-';

  return { status: statusNext, teksBB: bbNext, teksTB: tbNext, teksUsia: usiaNext };
}

function renderTable() {
  tbody.innerHTML = '';
  dataBalita.forEach((b, i) => {
    const isLocal = !!b._id;
    const jkShort = b.jk === "Laki-laki" ? "Laki-laki" : "Perempuan";

    let badgeClass = 'secondary';
    if (b.status === 'Gizi Baik') badgeClass = 'success';
    else if (b.status === 'Gizi Kurang') badgeClass = 'warning';
    else if (b.status === 'Gizi Buruk') badgeClass = 'danger';
    else if (b.status === 'Gizi Lebih') badgeClass = 'primary';

    // Hitung usia dari tanggal lahir jika tersedia
    const tglStr = b.tglLahir || b.tanggal;
    const tglDate = parseFlexibleDate(tglStr);
    let usiaBulan = b.usia;
    if (tglDate) usiaBulan = monthsDiff(tglDate, new Date());
    const displayTgl = tglDate ? formatDDMMYYYY(tglDate) : (tglStr || '-');

    const pred = prediksiBulanDepan({ bb: b.bb, tb: b.tb, usia: usiaBulan, status: b.status });
    const predText = `${pred.status} (BB: ${pred.teksBB}, TB: ${pred.teksTB}, Usia: ${pred.teksUsia})`;

    tbody.innerHTML += `
      <tr>
        <td style="text-align:center;">${i + 1}</td>
        <td>${b.nama}</td>
        <td>${jkShort}</td>
        <td>${displayTgl}</td>
        <td>${typeof usiaBulan === 'number' ? usiaBulan : '-'}</td>
        <td>${b.bb ?? '-'}</td>
        <td>${b.tb ?? '-'}</td>
        <td>${b.status ? `<span class=\"badge bg-${badgeClass}\">${b.status}</span>` : '-'}</td>
        <td style="font-weight:400;">${predText}</td>
      </tr>`;
  });

  // Tambah kolom aksi (Detail) per baris
  Array.from(tbody.rows).forEach((tr, i) => {
    const td = document.createElement('td');
    td.style.textAlign = 'center';
    td.innerHTML = `
      <button class="btn btn-sm btn-outline-secondary" data-action="detail" data-i="${i}" title="Detail"
        style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;">
        <i class="ti ti-eye" style="font-size:16px;line-height:1;"></i>
      </button>`;
    tr.appendChild(td);
  });

  // Hubungkan tombol detail
  tbody.querySelectorAll('button[data-action="detail"]').forEach(btn => {
    btn.addEventListener('click', () => {
      const i = parseInt(btn.getAttribute('data-i'), 10);
      openDetailModal(i);
    });
  });
}

renderTable();

// Modal detail
const detailWrap = document.createElement('div');
detailWrap.innerHTML = `
  <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Data Balita</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="detailContent"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>`;
document.body.appendChild(detailWrap);
const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

function openDetailModal(i){
  const item = dataBalita[i];
  if (!item) return;
  const tglLahirDate = parseFlexibleDate(item.tglLahir || item.tanggal);
  const usiaBln = tglLahirDate ? monthsDiff(tglLahirDate, new Date()) : (typeof item.usia === 'number' ? item.usia : '-');
  const pred = prediksiBulanDepan({ bb: item.bb, tb: item.tb, usia: (typeof usiaBln === 'number' ? usiaBln : undefined), status: item.status });
  const html = `
    <div class="table-responsive">
      <table class="table table-sm">
        <tr><th style="width:180px;">Nama Balita</th><td>${item.nama || '-'}</td></tr>
        ${item.nik ? `<tr><th>NIK Balita</th><td>${item.nik}</td></tr>` : ''}
        ${item.ayah ? `<tr><th>Nama Ayah</th><td>${item.ayah}</td></tr>` : ''}
        ${item.ibu ? `<tr><th>Nama Ibu</th><td>${item.ibu}</td></tr>` : ''}
        <tr><th>Jenis Kelamin</th><td>${item.jk || '-'}</td></tr>
        <tr><th>Tanggal Lahir</th><td>${tglLahirDate ? formatDDMMYYYY(tglLahirDate) : (item.tanggal || '-')}</td></tr>
        <tr><th>Usia (bln)</th><td>${usiaBln}</td></tr>
        <tr><th>Berat (kg)</th><td>${(typeof item.bb !== 'undefined') ? item.bb : '-'}</td></tr>
        <tr><th>Tinggi (cm)</th><td>${(typeof item.tb !== 'undefined') ? item.tb : '-'}</td></tr>
        <tr><th>Status Gizi</th><td>${item.status || '-'}</td></tr>
        <tr><th>Prediksi Bulan Depan</th><td>${pred.status} (BB: ${pred.teksBB}, TB: ${pred.teksTB}, Usia: ${pred.teksUsia})</td></tr>
        ${item.tglInput ? `<tr><th>Tanggal Posyandu</th><td>${formatDDMMYYYY(parseFlexibleDate(item.tglInput))}</td></tr>` : ''}
        ${item.rekom ? `<tr><th>Rekomendasi</th><td>${item.rekom}</td></tr>` : ''}
      </table>
    </div>`;
  document.getElementById('detailContent').innerHTML = html;
  detailModal.show();
}
</script>
@endpush

@endsection
