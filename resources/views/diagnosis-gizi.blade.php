@extends('layouts.app')
@section('title','Diagnosis Gizi')
@section('content')
<div class="container-fluid mt-2">
  <div class="row">
    <!-- Form kiri -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title fw-semibold mb-4">Form Diagnosis</h5>
          <form id="formDiagnosis">
            <div class="mb-3">
              <label class="form-label">Nama Balita</label>
              <select class="form-select" id="namaBalita" required>
                <option value="" selected disabled>Pilih Balita</option>
              </select>
              <small class="text-muted">Nama diambil dari daftar "Data Balita".</small>
            </div>

            <div class="mb-3">
              <label class="form-label">Jenis Kelamin</label>
              <select class="form-select" id="jenisKelamin" required>
                <option selected disabled>Pilih Jenis Kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Tanggal Lahir Balita</label>
              <input type="date" class="form-control" id="tglLahir" required>
              <small class="text-muted">Tanggal lahir digunakan untuk menghitung usia otomatis.</small>
            </div>

            <div class="mb-3">
              <label class="form-label">Usia (bulan)</label>
              <input type="number" class="form-control" id="usia" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label">Berat Badan (kg)</label>
              <input type="number" class="form-control" id="bb" step="0.1" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Tinggi Badan (cm)</label>
              <input type="number" step="0.1" class="form-control" id="tb" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Diagnosis</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Hasil kanan -->
    <div class="col-lg-6">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title fw-semibold">Data Balita</h5>
          <p>Nama: <span id="outNama">-</span></p>
          <p>Jenis Kelamin: <span id="outJK">-</span></p>
          <p>Usia: <span id="outUsia">-</span> bulan</p>
          <p>Berat Badan: <span id="outBB">-</span> kg</p>
          <p>Tinggi Badan: <span id="outTB">-</span> cm</p>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title fw-semibold">Status Gizi</h5>
          <p><strong id="outStatus">Belum diperiksa</strong></p>
          <div class="progress" style="height:15px;">
            <div id="barGizi" class="progress-bar bg-warning" style="width:40%;"></div>
          </div>
          <small class="text-muted">Acuan: Standar Antropometri Anak (PMK No.2 Tahun 2020)</small>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// ==== Helpers tanggal ====
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
function formatYYYYMMDD(dt){
  const dd = String(dt.getDate()).padStart(2,'0');
  const mm = String(dt.getMonth()+1).padStart(2,'0');
  const yyyy = dt.getFullYear();
  return `${yyyy}-${mm}-${dd}`;
}
function monthsDiff(from, to){
  let months = (to.getFullYear() - from.getFullYear()) * 12 + (to.getMonth() - from.getMonth());
  if (to.getDate() < from.getDate()) months -= 1;
  return months < 0 ? 0 : months;
}

// ==== Kalkulator usia ====
const tglLahirInput = document.getElementById('tglLahir');
const usiaInput = document.getElementById('usia');
tglLahirInput.addEventListener('change', () => {
  const tglLahir = parseYYYYMMDD(tglLahirInput.value);
  const today = new Date();
  usiaInput.value = tglLahir ? monthsDiff(tglLahir, today) : '';
});
// batasi tanggal lahir maksimal hari ini
tglLahirInput.setAttribute('max', formatYYYYMMDD(new Date()));

// ==== Dropdown nama dari LocalStorage ====
const namaSelect = document.getElementById('namaBalita');
const jkSelect = document.getElementById('jenisKelamin');
const bbInput = document.getElementById('bb');
const tbInput = document.getElementById('tb');

function loadNamaOptions(){
  namaSelect.innerHTML = '<option value="" disabled selected>Pilih Balita</option>';
  let list = [];
  try { list = JSON.parse(localStorage.getItem('dataBalita') || '[]'); } catch(e) { list = []; }
  // deduplicate by name, keep latest occurrence
  const map = new Map();
  list.forEach((item, idx) => { if (item && item.nama) map.set(item.nama, {idx, item}); });

  Array.from(map.keys()).sort().forEach(nm => {
    const {item} = map.get(nm);
    const opt = document.createElement('option');
    opt.value = nm;
    opt.textContent = nm;
    if (item._id) opt.dataset.id = item._id;
    opt.dataset.jk = item.jk || '';
    opt.dataset.tgl = item.tglLahir || item.tanggal || '';
    if (typeof item.bb !== 'undefined') opt.dataset.bb = item.bb;
    if (typeof item.tb !== 'undefined') opt.dataset.tb = item.tb;
    namaSelect.appendChild(opt);
  });
}

namaSelect.addEventListener('change', () => {
  const opt = namaSelect.options[namaSelect.selectedIndex];
  if (!opt) return;
  // set JK
  if (opt.dataset.jk) jkSelect.value = opt.dataset.jk;
  // set tgl lahir (if available)
  const parsed = parseFlexibleDate(opt.dataset.tgl);
  tglLahirInput.value = parsed ? formatYYYYMMDD(parsed) : '';
  // trigger usia compute
  const dt = parsed;
  usiaInput.value = dt ? monthsDiff(dt, new Date()) : '';
  // optional isi bb/tb jika ada
  bbInput.value = (typeof opt.dataset.bb !== 'undefined') ? opt.dataset.bb : '';
  tbInput.value = (typeof opt.dataset.tb !== 'undefined') ? opt.dataset.tb : '';
});

// init options pada load
document.addEventListener('DOMContentLoaded', loadNamaOptions);

// ==== Dataset mini WHO (median dan SD BB/U) ====
const refWHO = {
  "Laki-laki": { 0: { median: 3.3, sd: 0.5 }, 6: { median: 7.9, sd: 0.8 }, 12: { median: 9.6, sd: 0.9 }, 24: { median: 12.2, sd: 1.1 }, 36: { median: 14.3, sd: 1.3 }, 48: { median: 16.3, sd: 1.5 }, 60: { median: 18.3, sd: 1.7 } },
  "Perempuan": { 0: { median: 3.2, sd: 0.5 }, 6: { median: 7.3, sd: 0.8 }, 12: { median: 8.9, sd: 0.9 }, 24: { median: 11.5, sd: 1.1 }, 36: { median: 13.9, sd: 1.3 }, 48: { median: 15.9, sd: 1.5 }, 60: { median: 17.9, sd: 1.7 } }
};

function getRef(gender, ageMonth) {
  const data = refWHO[gender];
  const keys = Object.keys(data).map(Number);
  let nearest = keys[0];
  keys.forEach(k => { if (Math.abs(ageMonth - k) < Math.abs(ageMonth - nearest)) nearest = k; });
  return data[nearest];
}

// ==== Notification helper (top-center) ====
(function(){
  const notif = document.createElement('div');
  notif.id = 'notifMessage';
  notif.setAttribute('aria-live','polite');
  notif.style.position = 'fixed';
  notif.style.left = '50%';
  notif.style.top = '24px';
  notif.style.transform = 'translateX(-50%)';
  notif.style.zIndex = 99999;
  notif.style.pointerEvents = 'none';
  document.body.appendChild(notif);

  let notifTimer = null;
  window.showNotification = function(success, message){
    if (notifTimer) { clearTimeout(notifTimer); notifTimer = null; }
    notif.innerHTML = '';
    const box = document.createElement('div');
    box.style.display = 'inline-flex';
    box.style.alignItems = 'center';
    box.style.gap = '8px';
    box.style.pointerEvents = 'auto';
    box.style.padding = '10px 14px';
    box.style.borderRadius = '8px';
    box.style.boxShadow = '0 6px 18px rgba(0,0,0,0.08)';
    box.style.fontSize = '14px';
    box.style.color = success ? '#0f5132' : '#842029';
    box.style.background = success ? '#d1e7dd' : '#f8d7da';
    box.style.opacity = '0';
    box.style.transition = 'opacity 220ms ease';

    const icon = document.createElement('span');
    icon.style.display = 'inline-block';
    icon.style.width = '20px';
    icon.style.height = '20px';
    icon.style.flex = '0 0 20px';
    icon.style.fontWeight = '700';
    icon.style.textAlign = 'center';
    icon.style.lineHeight = '20px';
    icon.textContent = success ? '✓' : '✕';
    icon.style.color = success ? '#0f5132' : '#842029';

    const txt = document.createElement('div');
    txt.textContent = message || (success ? 'Berhasil' : 'Gagal');

    box.appendChild(icon);
    box.appendChild(txt);
    notif.appendChild(box);

    // auto hide after 3500ms
    requestAnimationFrame(() => { box.style.opacity = '1'; });
    notifTimer = setTimeout(() => { notif.innerHTML = ''; notifTimer = null; }, 3500);
  };
})();

// ==== Event submit form ====
document.getElementById('formDiagnosis').addEventListener('submit', e => {
  e.preventDefault();

  const nama = document.getElementById('namaBalita').value;
  const jk = document.getElementById('jenisKelamin').value;
  const usia = parseFloat(document.getElementById('usia').value);
  const bb = parseFloat(document.getElementById('bb').value);
  const tb = parseFloat(document.getElementById('tb').value);
  const tglLahirStr = document.getElementById('tglLahir').value; // yyyy-mm-dd

  // Tampilkan data
  document.getElementById('outNama').innerText = nama;
  document.getElementById('outJK').innerText = jk;
  document.getElementById('outUsia').innerText = usia;
  document.getElementById('outBB').innerText = bb;
  document.getElementById('outTB').innerText = tb;

  // Ambil referensi WHO
  const ref = getRef(jk, usia);
  const z = (bb - ref.median) / ref.sd;

  // Tentukan status
  let status = "";
  let warna = "";
  let persen = 50;

  if (z > 2) { status = "Gizi Lebih"; warna = "bg-primary"; persen = 90; }
  else if (z >= -2) { status = "Gizi Baik"; warna = "bg-success"; persen = 70; }
  else if (z >= -3) { status = "Gizi Kurang"; warna = "bg-warning"; persen = 40; }
  else { status = "Gizi Buruk"; warna = "bg-danger"; persen = 20; }

  // Update UI
  const bar = document.getElementById('barGizi');
  bar.className = `progress-bar ${warna}`;
  bar.style.width = persen + "%";
  document.getElementById('outStatus').innerText = `${status} (Z = ${z.toFixed(2)})`;
  // Siapkan rekomendasi
  const rekom = status === "Gizi Baik" ? "Pertahankan pola makan seimbang, tambah buah setiap hari." :
    status === "Gizi Kurang" ? "Tingkatkan asupan protein hewani seperti telur, ikan, ayam." :
    status === "Gizi Buruk" ? "Segera konsultasi ke tenaga kesehatan dan perbaiki pola makan." :
    "Perhatikan asupan makanan dan aktivitas fisik.";
  try {
    let dataBalita = JSON.parse(localStorage.getItem('dataBalita') || '[]');
    const selectedOpt = namaSelect.options[namaSelect.selectedIndex];
    const selId = selectedOpt ? (selectedOpt.dataset.id || null) : null;
    let idx = -1;
    if (selId) {
      idx = dataBalita.findIndex(x => x && x._id === selId);
    }
    if (idx === -1) {
      idx = dataBalita.findIndex(x => x && x.nama === nama);
    }
    if (idx === -1) {
      if (window.showNotification) window.showNotification(false, 'Data balita tidak ditemukan untuk diperbarui');
      return;
    }

    // Update field yang relevan
    dataBalita[idx].nama = nama;
    dataBalita[idx].jk = jk;
    if (tglLahirStr) dataBalita[idx].tglLahir = tglLahirStr; // yyyy-mm-dd
    if (!isNaN(bb)) dataBalita[idx].bb = bb;
    if (!isNaN(tb)) dataBalita[idx].tb = tb;
    dataBalita[idx].status = status;
    dataBalita[idx].rekom = rekom;

    localStorage.setItem('dataBalita', JSON.stringify(dataBalita));
    loadNamaOptions();
    if (window.showNotification) window.showNotification(true, 'Data balita berhasil diperbarui');
  } catch (err) {
    console.error('Gagal menyimpan data:', err);
    if (window.showNotification) window.showNotification(false, 'Gagal memperbarui data');
  }
});
</script>
@endpush
@endsection
