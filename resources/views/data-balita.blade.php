@extends('layouts.app')
@section('title','Data Balita')
@section('content')
<div class="container-fluid mt-2">
  <div class="card">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="card-title fw-semibold m-0">Data Balita</h5>
        <button type="button" id="openCreateBtn" class="btn btn-primary">
          <i class="ti ti-plus me-1"></i> Tambah Data
        </button>
      </div>
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
          <tbody id="tabelBalita"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// load and render table same logic as original
const dataStatis = [
  { nama: "Ahmad Dani", jk: "Laki-laki", usia: 24, bb: 9.5, tb: 73, status: "Gizi Kurang", rekom: "Tingkatkan asupan protein hewani seperti telur, ikan, ayam.", tanggal: "09/10/2023" },
  { nama: "Rani Lestari", jk: "Perempuan", usia: 30, bb: 11.2, tb: 80, status: "Gizi Baik", rekom: "Pertahankan pola makan seimbang, tambah buah setiap hari.", tanggal: "09/10/2023" }
];
let dataLocal = JSON.parse(localStorage.getItem('dataBalita') || '[]');
let changed = false;
dataLocal = dataLocal.map((d, idx) => { if (!d._id) { d._id = 'l_' + (Date.now() + idx); changed = true; } return d; });
if (changed) localStorage.setItem('dataBalita', JSON.stringify(dataLocal));
const dataBalita = [...dataStatis, ...dataLocal];
const tbody = document.getElementById('tabelBalita');

function parseDDMMYYYY(s){
  if (!s || typeof s !== 'string') return null;
  const m = s.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
  if (!m) return null;
  const d = parseInt(m[1],10), mo = parseInt(m[2],10)-1, y = parseInt(m[3],10);
  const dt = new Date(y, mo, d);
  if (dt.getFullYear() !== y || dt.getMonth() !== mo || dt.getDate() !== d) return null;
  return dt;
}

function monthsDiff(from, to){
  let months = (to.getFullYear() - from.getFullYear()) * 12 + (to.getMonth() - from.getMonth());
  if (to.getDate() < from.getDate()) months -= 1;
  return months < 0 ? 0 : months;
}

function parseYYYYMMDD(s){
  if (!s || typeof s !== 'string') return null;
  const m = s.match(/^(\d{4})-(\d{2})-(\d{2})$/);
  if (!m) return null;
  const y = parseInt(m[1],10), mo = parseInt(m[2],10)-1, d = parseInt(m[3],10);
  const dt = new Date(y, mo, d);
  if (dt.getFullYear() !== y || dt.getMonth() !== mo || dt.getDate() !== d) return null;
  return dt;
}

function parseFlexibleDate(s){
  return parseDDMMYYYY(s) || parseYYYYMMDD(s);
}

function formatDDMMYYYY(dt){
  const dd = String(dt.getDate()).padStart(2,'0');
  const mm = String(dt.getMonth()+1).padStart(2,'0');
  const yyyy = dt.getFullYear();
  return `${dd}/${mm}/${yyyy}`;
}

function formatYYYYMMDD(dt){
  const dd = String(dt.getDate()).padStart(2,'0');
  const mm = String(dt.getMonth()+1).padStart(2,'0');
  const yyyy = dt.getFullYear();
  return `${yyyy}-${mm}-${dd}`;
}
function renderTable() {
  tbody.innerHTML = '';
  dataBalita.forEach((b, i) => {
    const isLocal = !!b._id;

    let badgeClass = 'secondary';
    if (b.status === 'Gizi Baik') badgeClass = 'success';
    else if (b.status === 'Gizi Kurang') badgeClass = 'warning';
    else if (b.status === 'Gizi Buruk') badgeClass = 'danger';
    else if (b.status === 'Gizi Lebih') badgeClass = 'primary';

    // 🔹 tombol aksi disusun horizontal (samping)
    let aksiHtml = '';
    if (isLocal) {
      aksiHtml = `
        <div style="display:flex; justify-content:center; align-items:center; gap:6px;">
          <button class="btn btn-sm btn-outline-secondary" data-action="detail" data-id="${b._id}" title="Detail"
            style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;">
            <i class="ti ti-eye" style="font-size:16px;line-height:1;"></i>
          </button>
          <button class="btn btn-sm btn-outline-primary" data-action="edit" data-id="${b._id}" title="Edit"
            style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;">
            <i class="ti ti-edit" style="font-size:16px;line-height:1;"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${b._id}" title="Hapus"
            style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;">
            <i class="ti ti-trash" style="font-size:16px;line-height:1;"></i>
          </button>
        </div>`;
    }

    // usia dihitung dari tanggal lahir jika ada
    const tglLahirStr = b.tglLahir || b.tanggal;
    const tglLahirDate = parseFlexibleDate(tglLahirStr);
    let usiaBulan = b.usia;
    if (tglLahirDate) usiaBulan = monthsDiff(tglLahirDate, new Date());
    const statusHtml = b.status ? `<span class="badge bg-${badgeClass}">${b.status}</span>` : '-';
    const displayTgl = tglLahirDate ? formatDDMMYYYY(tglLahirDate) : '-';
    const tglInputDate = parseFlexibleDate(b.tglInput);
    const displayTglInput = tglInputDate ? formatDDMMYYYY(tglInputDate) : '-';

    const row = `
      <tr>
        <td>${i + 1}</td>
        <td>${b.nama}</td>
        <td>${b.jk}</td>
        <td>${displayTgl}</td>
        <td>${typeof usiaBulan === 'number' ? usiaBulan : '-'}</td>
        <td>${b.bb ?? '-'}</td>
        <td>${b.tb ?? '-'}</td>
        <td>${statusHtml}</td>
        <td>${displayTglInput}</td>
        <td style="text-align:center;">${aksiHtml}</td>
      </tr>`;
    tbody.innerHTML += row;
  });

  // biar tombol edit & hapus tetap aktif
  tbody.querySelectorAll('button[data-action]').forEach(btn => {
    btn.addEventListener('click', ev => {
      const act = btn.getAttribute('data-action');
      const id = btn.getAttribute('data-id');
      if (act === 'detail') openDetailModal(id);
      if (act === 'edit') openEditModal(id);
      if (act === 'delete') openDeleteConfirm(id);
    });
  });
}

renderTable();

// notifications top-center
const notifDiv = document.createElement('div');
notifDiv.id = 'notifCrud';
notifDiv.setAttribute('aria-live','polite');
notifDiv.style.position = 'fixed';
notifDiv.style.left = '50%';
notifDiv.style.top = '20px';
notifDiv.style.transform = 'translateX(-50%)';
notifDiv.style.zIndex = 99999;
notifDiv.style.pointerEvents = 'none';
document.body.appendChild(notifDiv);
let notifTimer = null;
function showCrudNotif(success, msg){
  if (notifTimer) { clearTimeout(notifTimer); notifTimer = null; }
  notifDiv.innerHTML = '';
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
  txt.textContent = msg;

  box.appendChild(icon);
  box.appendChild(txt);
  notifDiv.appendChild(box);
  requestAnimationFrame(() => { box.style.opacity = '1'; });
  notifTimer = setTimeout(() => { notifDiv.innerHTML = ''; notifTimer = null; }, 2600);
}

// modals (create elements)
const modalContainer = document.createElement('div');
modalContainer.innerHTML = `
  <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Data Balita</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="createForm">
            <div class="mb-2"><label class="form-label">NIK Balita</label><input id="createNik" class="form-control" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" required title="Masukkan 16 digit angka"></div>
            <div class="mb-2"><label class="form-label">Nama Balita</label><input id="createNama" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Jenis Kelamin</label>
              <select id="createJK" class="form-select" required>
                <option value="" selected disabled>Pilih jenis kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
            <div class="mb-2"><label class="form-label">Tanggal Lahir</label><input id="createTanggal" class="form-control" type="date" autocomplete="off" required></div>
            <div class="mb-2">
              <label class="form-label d-flex justify-content-between align-items-center">
                <span>Tanggal Posyandu</span>
                <button type="button" id="todayCreateTP" class="btn btn-sm btn-outline-secondary">Hari Ini</button>
              </label>
              <input id="createTglPosyandu" class="form-control" type="date" autocomplete="off" required>
            </div>
            <div class="mb-2"><label class="form-label">Usia (bln)</label><input id="createUmur" class="form-control" type="number" placeholder="Otomatis dari tanggal lahir" disabled></div>
            <div class="mb-2"><label class="form-label">Berat (kg)</label><input id="createBB" class="form-control" type="number" step="0.1" min="0"></div>
            <div class="mb-2"><label class="form-label">Tinggi (cm)</label><input id="createTB" class="form-control" type="number" step="0.1" min="0"></div>
            <div class="mb-2"><label class="form-label">Nama Ayah</label><input id="createAyah" class="form-control"></div>
            <div class="mb-2"><label class="form-label">Nama Ibu</label><input id="createIbu" class="form-control"></div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
          <button type="button" id="saveCreateBtn" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Data Balita</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm">
            <input type="hidden" id="editId">
            <div class="mb-2"><label class="form-label">NIK Balita</label><input id="editNik" class="form-control" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" required title="Masukkan 16 digit angka"></div>
            <div class="mb-2"><label class="form-label">Nama Balita</label><input id="editNama" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Jenis Kelamin</label>
              <select id="editJK" class="form-select" required>
                <option value="" disabled>Pilih jenis kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
            <div class="mb-2"><label class="form-label">Tanggal Lahir</label><input id="editTanggal" class="form-control" type="date" autocomplete="off"></div>
            <div class="mb-2">
              <label class="form-label d-flex justify-content-between align-items-center">
                <span>Tanggal Posyandu</span>
                <button type="button" id="todayEditTP" class="btn btn-sm btn-outline-secondary">Hari Ini</button>
              </label>
              <input id="editTglPosyandu" class="form-control" type="date" autocomplete="off">
            </div>
            <div class="mb-2"><label class="form-label">Usia (bln)</label><input id="editUmur" class="form-control" type="number" placeholder="Otomatis dari tanggal lahir" disabled></div>
            <div class="mb-2"><label class="form-label">Berat (kg)</label><input id="editBB" class="form-control" type="number" step="0.1"></div>
            <div class="mb-2"><label class="form-label">Tinggi (cm)</label><input id="editTB" class="form-control" type="number" step="0.1"></div>
            <div class="mb-2"><label class="form-label">Nama Ayah</label><input id="editAyah" class="form-control"></div>
            <div class="mb-2"><label class="form-label">Nama Ibu</label><input id="editIbu" class="form-control"></div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
          <button type="button" id="saveEditBtn" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">Apakah Anda yakin ingin menghapus data ini?</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
          <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Hapus</button>
        </div>
      </div>
    </div>
  </div>`;
  document.body.appendChild(modalContainer);

  // Detail modal (lazy append)
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

const createModalEl = document.getElementById('createModal');
const editModalEl = document.getElementById('editModal');
const confirmModalEl = document.getElementById('confirmModal');
const createModal = new bootstrap.Modal(createModalEl);
const editModal = new bootstrap.Modal(editModalEl);
const confirmModal = new bootstrap.Modal(confirmModalEl);
const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

// normalisasi input NIK: hanya digit dan max 16 + pewarnaan validasi
const createNikInput = document.getElementById('createNik');
const editNikInput = document.getElementById('editNik');
function updateNikValidity(el){
  if (!el) return;
  const len = (el.value || '').length;
  el.classList.remove('is-valid','is-invalid');
  if (len === 16) el.classList.add('is-valid');
  else el.classList.add('is-invalid');
}
function normalizeNikInput(el){
  if (!el) return;
  el.addEventListener('input', () => {
    el.value = (el.value || '').replace(/\D/g,'').slice(0,16);
    updateNikValidity(el);
  });
  // inisialisasi state saat attach
  updateNikValidity(el);
}
normalizeNikInput(createNikInput);
normalizeNikInput(editNikInput);

// umur (bulan) auto dari tanggal lahir
const createTanggalInput = document.getElementById('createTanggal');
const createUmurInput = document.getElementById('createUmur');
const editTanggalInput = document.getElementById('editTanggal');
const editUmurInput = document.getElementById('editUmur');
// tanggal posyandu inputs + quick buttons
const createTglPosyanduInput = document.getElementById('createTglPosyandu');
const editTglPosyanduInput = document.getElementById('editTglPosyandu');
const todayCreateTPBtn = document.getElementById('todayCreateTP');
const todayEditTPBtn = document.getElementById('todayEditTP');

function computeCreateUmur(){
  const dt = parseFlexibleDate(createTanggalInput.value);
  createUmurInput.value = dt ? monthsDiff(dt, new Date()) : '';
}
function computeEditUmur(){
  const dt = parseFlexibleDate(editTanggalInput.value);
  editUmurInput.value = dt ? monthsDiff(dt, new Date()) : '';
}

createTanggalInput.addEventListener('input', computeCreateUmur);
editTanggalInput.addEventListener('input', computeEditUmur);

// open create modal
document.getElementById('openCreateBtn').addEventListener('click', () => {
  document.getElementById('createForm').reset();
  // reset validasi nik (kosong = merah sesuai aturan < 16)
  if (createNikInput){ createNikInput.value=''; updateNikValidity(createNikInput); }
  const today = formatYYYYMMDD(new Date());
  createTanggalInput.setAttribute('max', today);
  if (createTglPosyanduInput){
    createTglPosyanduInput.value = today;
    createTglPosyanduInput.setAttribute('max', today);
  }
  createModal.show();
});

// tombol "Hari Ini" untuk tanggal posyandu
if (todayCreateTPBtn && createTglPosyanduInput){
  todayCreateTPBtn.addEventListener('click', () => {
    const today = formatYYYYMMDD(new Date());
    createTglPosyanduInput.value = today;
  });
}
if (todayEditTPBtn && editTglPosyanduInput){
  todayEditTPBtn.addEventListener('click', () => {
    const today = formatYYYYMMDD(new Date());
    editTglPosyanduInput.value = today;
  });
}

// save create
document.getElementById('saveCreateBtn').addEventListener('click', () => {
  const nik = ((document.getElementById('createNik').value || '').replace(/\D/g,'')).trim();
  const nama = (document.getElementById('createNama').value || '').trim();
  const ayah = (document.getElementById('createAyah').value || '').trim();
  const ibu = (document.getElementById('createIbu').value || '').trim();
  const jk = document.getElementById('createJK').value;
  const tanggal = (document.getElementById('createTanggal').value || '').trim();
  const tglPosyandu = (document.getElementById('createTglPosyandu').value || '').trim();
  const bb = parseFloat(document.getElementById('createBB').value);
  const tb = parseFloat(document.getElementById('createTB').value);

  if (!nik || !nama || !jk || !tanggal || !tglPosyandu) {
    showCrudNotif(false, 'NIK, nama, jenis kelamin, tanggal lahir, dan tanggal posyandu wajib diisi');
    return;
  }
  if (nik.length !== 16) { showCrudNotif(false, 'NIK harus 16 digit'); return; }

  const item = {
    _id: 'l_' + Date.now(),
    nik,
    nama,
    ayah: ayah || undefined,
    ibu: ibu || undefined,
    jk,
    tglLahir: tanggal,
    tglInput: tglPosyandu,
    bb: isNaN(bb) ? undefined : bb,
    tb: isNaN(tb) ? undefined : tb
  };
  dataLocal.push(item);
  localStorage.setItem('dataBalita', JSON.stringify(dataLocal));
  dataBalita.length = 0; dataBalita.push(...dataStatis, ...dataLocal);
  renderTable();
  createModal.hide();
  showCrudNotif(true, 'Data berhasil ditambahkan');
});

function openEditModal(id){
  const item = dataLocal.find(x => x._id === id);
  if (!item) return showCrudNotif(false, 'Data tidak ditemukan');
  document.getElementById('editId').value = item._id;
  document.getElementById('editNik').value = item.nik || '';
  updateNikValidity(editNikInput);
  document.getElementById('editNama').value = item.nama || '';
  document.getElementById('editAyah').value = item.ayah || '';
  document.getElementById('editIbu').value = item.ibu || '';
  document.getElementById('editJK').value = item.jk || '';
  const srcDateStr = item.tglLahir || item.tanggal || '';
  const parsed = parseFlexibleDate(srcDateStr);
  const editInput = document.getElementById('editTanggal');
  editInput.value = parsed ? formatYYYYMMDD(parsed) : '';
  editInput.setAttribute('max', formatYYYYMMDD(new Date()));
  // set tanggal posyandu (editable) default dari data atau kosong
  if (editTglPosyanduInput){
    const tpi = item.tglInput ? parseFlexibleDate(item.tglInput) : null;
    editTglPosyanduInput.value = tpi ? formatYYYYMMDD(tpi) : '';
    editTglPosyanduInput.setAttribute('max', formatYYYYMMDD(new Date()));
  }
  document.getElementById('editBB').value = item.bb || '';
  document.getElementById('editTB').value = item.tb || '';
  editModal.show();
}

function openDetailModal(id){
  const item = dataLocal.find(x => x._id === id);
  if (!item) { showCrudNotif(false, 'Data tidak ditemukan'); return; }
  const tglLahirDate = parseFlexibleDate(item.tglLahir || item.tanggal);
  const usiaBln = tglLahirDate ? monthsDiff(tglLahirDate, new Date()) : (typeof item.usia === 'number' ? item.usia : '-');
  const html = `
    <div class="table-responsive">
      <table class="table table-sm">
        <tr><th style="width:160px;">Nama Balita</th><td>${item.nama || '-'}</td></tr>
        <tr><th style="width:160px;">NIK Balita</th><td>${item.nik || '-'}</td></tr>
        <tr><th>Nama Ayah</th><td>${item.ayah || '-'}</td></tr>
        <tr><th>Nama Ibu</th><td>${item.ibu || '-'}</td></tr>
        <tr><th>Jenis Kelamin</th><td>${item.jk || '-'}</td></tr>
        <tr><th>Tanggal Lahir</th><td>${tglLahirDate ? formatDDMMYYYY(tglLahirDate) : '-'}</td></tr>
        <tr><th>Usia (bln)</th><td>${usiaBln}</td></tr>
        <tr><th>Berat (kg)</th><td>${(typeof item.bb !== 'undefined') ? item.bb : '-'}</td></tr>
        <tr><th>Tinggi (cm)</th><td>${(typeof item.tb !== 'undefined') ? item.tb : '-'}</td></tr>
        <tr><th>Status Gizi</th><td>${item.status || '-'}</td></tr>
        <tr><th>Tanggal Posyandu</th><td>${item.tglInput ? formatDDMMYYYY(parseFlexibleDate(item.tglInput)) : '-'}</td></tr>
        ${item.rekom ? `<tr><th>Rekomendasi</th><td>${item.rekom}</td></tr>` : ''}
      </table>
    </div>`;
  document.getElementById('detailContent').innerHTML = html;
  detailModal.show();
}

document.getElementById('saveEditBtn').addEventListener('click', () => {
  const id = document.getElementById('editId').value;
  const idx = dataLocal.findIndex(x => x._id === id);
  if (idx === -1) return showCrudNotif(false, 'Data tidak ditemukan');
  const nikEdit = ((document.getElementById('editNik').value || '').replace(/\D/g,'')).trim();
  if (!nikEdit || nikEdit.length !== 16) { showCrudNotif(false, 'NIK harus 16 digit'); return; }
  dataLocal[idx].nik = nikEdit;
  dataLocal[idx].nama = document.getElementById('editNama').value;
  dataLocal[idx].ayah = (document.getElementById('editAyah').value || '').trim();
  dataLocal[idx].ibu = (document.getElementById('editIbu').value || '').trim();
  dataLocal[idx].jk = document.getElementById('editJK').value;
  const tl = (document.getElementById('editTanggal').value || '').trim();
  // deteksi perubahan data yang mempengaruhi status gizi
  let statusAffectChanged = false;
  if (tl) {
    if (tl !== (dataLocal[idx].tglLahir || '')) statusAffectChanged = true;
    dataLocal[idx].tglLahir = tl;
  }
  const tpi = (document.getElementById('editTglPosyandu').value || '').trim();
  if (tpi) dataLocal[idx].tglInput = tpi;
  // update BB jika diisi
  const bbStr = (document.getElementById('editBB').value || '').trim();
  if (bbStr !== '') {
    const bbVal = parseFloat(bbStr);
    if (!isNaN(bbVal)) {
      if (bbVal !== dataLocal[idx].bb) statusAffectChanged = true;
      dataLocal[idx].bb = bbVal;
    }
  }
  // update TB jika diisi
  const tbStr = (document.getElementById('editTB').value || '').trim();
  if (tbStr !== '') {
    const tbVal = parseFloat(tbStr);
    if (!isNaN(tbVal)) {
      if (tbVal !== dataLocal[idx].tb) statusAffectChanged = true;
      dataLocal[idx].tb = tbVal;
    }
  }
  // jika ada perubahan pada tgl lahir/BB/TB, kosongkan status gizi & rekomendasi
  if (statusAffectChanged) {
    delete dataLocal[idx].status;
    delete dataLocal[idx].rekom;
  }
  localStorage.setItem('dataBalita', JSON.stringify(dataLocal));
  dataBalita.length = 0; dataBalita.push(...dataStatis, ...dataLocal);
  renderTable();
  editModal.hide();
  showCrudNotif(true, 'Perubahan disimpan');
});

let pendingDeleteId = null; function openDeleteConfirm(id){ pendingDeleteId = id; confirmModal.show(); }
document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
  if (!pendingDeleteId) return;
  const idx = dataLocal.findIndex(x => x._id === pendingDeleteId);
  if (idx === -1) { showCrudNotif(false, 'Data tidak ditemukan'); confirmModal.hide(); return; }
  dataLocal.splice(idx,1);
  localStorage.setItem('dataBalita', JSON.stringify(dataLocal));
  dataBalita.length = 0; dataBalita.push(...dataStatis, ...dataLocal);
  renderTable();
  confirmModal.hide();  
  showCrudNotif(false, 'Data berhasil dihapus');
});
</script>
@endpush
@endsection
