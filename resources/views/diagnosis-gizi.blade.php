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
              <input type="text" class="form-control" id="namaBalita" required>
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
// ==== Kalkulator usia ====
const tglLahirInput = document.getElementById('tglLahir');
const usiaInput = document.getElementById('usia');
tglLahirInput.addEventListener('change', () => {
  const tglLahir = new Date(tglLahirInput.value);
  const today = new Date();
  let months = (today.getFullYear() - tglLahir.getFullYear()) * 12 + (today.getMonth() - tglLahir.getMonth());
  if (today.getDate() < tglLahir.getDate()) months -= 1;
  usiaInput.value = months >= 0 ? months : 0;
});

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
  // Simpan ke LocalStorage
  const rekom = status === "Gizi Baik" ? "Pertahankan pola makan seimbang, tambah buah setiap hari." :
    status === "Gizi Kurang" ? "Tingkatkan asupan protein hewani seperti telur, ikan, ayam." :
    status === "Gizi Buruk" ? "Segera konsultasi ke tenaga kesehatan dan perbaiki pola makan." :
    "Perhatikan asupan makanan dan aktivitas fisik.";
  const tanggal = new Date().toLocaleDateString('id-ID');
  const balitaBaru = { nama, jk, usia, bb, tb, status, rekom, tanggal };
  try {
    let dataBalita = JSON.parse(localStorage.getItem('dataBalita') || '[]');
    dataBalita.push(balitaBaru);
    localStorage.setItem('dataBalita', JSON.stringify(dataBalita));
    // feedback sukses (do NOT reset form per request)
    if (window.showNotification) window.showNotification(true, 'Data berhasil ditambahkan');
  } catch (err) {
    console.error('Gagal menyimpan data:', err);
    if (window.showNotification) window.showNotification(false, 'Gagal menyimpan data');
  }
});
</script>
@endpush
@endsection