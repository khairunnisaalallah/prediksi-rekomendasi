@extends('layouts.app')
@section('title','Diagnosis Gizi')
@section('content')
<div class="container-fluid mt-2">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title fw-semibold mb-4">Form Diagnosis</h5>
          <form id="formDiagnosis">
            <div class="mb-3">
              <label class="form-label">Nama Balita</label>
              <select class="form-select" id="namaBalita" required>
                <option value="" selected disabled>Pilih Balita</option>
                @foreach ($balitas as $balita)
                  <option
                    value="{{ $balita->id }}"
                    data-nama="{{ $balita->nama }}"
                    data-jk="{{ $balita->jenis_kelamin }}"
                    data-lahir="{{ optional($balita->tanggal_lahir)->format('Y-m-d') }}"
                    data-bb="{{ $balita->berat }}"
                    data-tb="{{ $balita->tinggi }}"
                    data-rekom="{{ $balita->rekomendasi }}"
                  >
                    {{ $balita->nama }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">Nama diambil dari data balita yang tersimpan di database.</small>
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
            <div id="diagFeedback" class="alert d-none mt-3" role="alert"></div>
          </form>
        </div>
      </div>
    </div>

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

      <div class="card">
        <div class="card-body">
          <h5 class="card-title fw-semibold mb-2">Rekomendasi</h5>
          <p id="outRekom" class="mb-0 text-muted">-</p>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrfToken = '{{ csrf_token() }}';

  function parseYYYYMMDD(s){
    if (!s || typeof s !== 'string') return null;
    const m = s.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (!m) return null;
    const y = parseInt(m[1],10), mo = parseInt(m[2],10)-1, d = parseInt(m[3],10);
    const dt = new Date(y,mo,d);
    return (dt.getFullYear()===y && dt.getMonth()===mo && dt.getDate()===d) ? dt : null;
  }
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

  const namaSelect = document.getElementById('namaBalita');
  const jkSelect = document.getElementById('jenisKelamin');
  const tglLahirInput = document.getElementById('tglLahir');
  const usiaInput = document.getElementById('usia');
  const bbInput = document.getElementById('bb');
  const tbInput = document.getElementById('tb');

  const handleAge = () => {
    const tglLahir = parseYYYYMMDD(tglLahirInput.value);
    usiaInput.value = tglLahir ? monthsDiff(tglLahir, new Date()) : '';
  };
  tglLahirInput.addEventListener('change', handleAge);
  tglLahirInput.setAttribute('max', formatYYYYMMDD(new Date()));

  namaSelect.addEventListener('change', () => {
    const opt = namaSelect.selectedOptions[0];
    if (!opt) return;
    const { jk, lahir, bb, tb } = opt.dataset;
    if (jk) jkSelect.value = jk;
    tglLahirInput.value = lahir || '';
    handleAge();
    bbInput.value = bb ?? '';
    tbInput.value = tb ?? '';

    document.getElementById('outNama').innerText = opt.dataset.nama || '-';
    document.getElementById('outJK').innerText = jkSelect.value || '-';
    document.getElementById('outUsia').innerText = usiaInput.value || '-';
    document.getElementById('outBB').innerText = bbInput.value || '-';
    document.getElementById('outTB').innerText = tbInput.value || '-';
    document.getElementById('outRekom').innerText = opt.dataset.rekom || '-';
  });

  const refWHO = {
    "Laki-laki": { 0: { median: 3.3, sd: 0.5 }, 6: { median: 7.9, sd: 0.8 }, 12: { median: 9.6, sd: 0.9 }, 24: { median: 12.2, sd: 1.1 }, 36: { median: 14.3, sd: 1.3 }, 48: { median: 16.3, sd: 1.5 }, 60: { median: 18.3, sd: 1.7 } },
    "Perempuan": { 0: { median: 3.2, sd: 0.5 }, 6: { median: 7.3, sd: 0.8 }, 12: { median: 8.9, sd: 0.9 }, 24: { median: 11.5, sd: 1.1 }, 36: { median: 13.9, sd: 1.3 }, 48: { median: 15.9, sd: 1.5 }, 60: { median: 17.9, sd: 1.7 } }
  };

  function getRef(gender, ageMonth) {
    const data = refWHO[gender];
    if (!data) return null;
    const keys = Object.keys(data).map(Number);
    let nearest = keys[0];
    keys.forEach(k => { if (Math.abs(ageMonth - k) < Math.abs(ageMonth - nearest)) nearest = k; });
    return data[nearest] || null;
  }

  document.getElementById('formDiagnosis').addEventListener('submit', e => {
    e.preventDefault();
    const feedback = document.getElementById('diagFeedback');
    const setFeedback = (msg, ok = true) => {
      feedback.textContent = msg;
      feedback.classList.remove('d-none', 'alert-success', 'alert-danger');
      feedback.classList.add(ok ? 'alert-success' : 'alert-danger');
    };
    const clearFeedback = () => {
      feedback.classList.add('d-none');
      feedback.textContent = '';
    };
    clearFeedback();

    const opt = namaSelect.selectedOptions[0];
    const nama = opt ? (opt.dataset.nama || opt.textContent) : '';
    const balitaId = opt ? opt.value : null;
    const jk = jkSelect.value;
    const usia = parseFloat(usiaInput.value);
    const bb = parseFloat(bbInput.value);
    const tb = parseFloat(tbInput.value);

    document.getElementById('outNama').innerText = nama || '-';
    document.getElementById('outJK').innerText = jk || '-';
    document.getElementById('outUsia').innerText = isNaN(usia) ? '-' : usia;
    document.getElementById('outBB').innerText = isNaN(bb) ? '-' : bb;
    document.getElementById('outTB').innerText = isNaN(tb) ? '-' : tb;

    const ref = getRef(jk, usia);
    if (!ref || isNaN(bb)) {
      document.getElementById('outStatus').innerText = 'Data tidak lengkap';
      const bar = document.getElementById('barGizi');
      bar.className = 'progress-bar bg-warning';
      bar.style.width = '40%';
      return;
    }
    const z = (bb - ref.median) / ref.sd;

    let status = "";
    let warna = "";
    let persen = 50;

    if (z > 2) { status = "Gizi Lebih"; warna = "bg-primary"; persen = 90; }
    else if (z >= -2) { status = "Gizi Baik"; warna = "bg-success"; persen = 70; }
    else if (z >= -3) { status = "Gizi Kurang"; warna = "bg-warning"; persen = 40; }
    else { status = "Gizi Buruk"; warna = "bg-danger"; persen = 20; }

    const bar = document.getElementById('barGizi');
    bar.className = `progress-bar ${warna}`;
    bar.style.width = persen + "%";
    document.getElementById('outStatus').innerText = `${status} (Z = ${z.toFixed(2)})`;

    document.getElementById('outRekom').innerText = 'Menyiapkan rekomendasi...';

    if (!balitaId) {
      alert('Pilih balita terlebih dahulu.');
      return;
    }

    const submitBtn = e.target.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Menyimpan...';
    }

    fetch(`/diagnosis-gizi/${balitaId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        status_gizi: status,
        berat: isNaN(bb) ? null : bb,
        tinggi: isNaN(tb) ? null : tb,
      }),
    })
    .then(async (res) => {
      if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error(err.message || 'Gagal menyimpan status gizi');
      }
      return res.json();
    })
    .then((data) => {
      setFeedback('Diagnosis berhasil disimpan.');
      const rekom = data.rekomendasi || '-';
      document.getElementById('outRekom').innerText = rekom;
      if (opt) opt.dataset.rekom = rekom;
    })
    .catch((err) => {
      console.error(err);
      setFeedback(err.message || 'Gagal menyimpan status gizi', false);
      document.getElementById('outRekom').innerText = '-';
    })
    .finally(() => {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Diagnosis';
      }
    });
  });
});
</script>
@endpush
@endsection
