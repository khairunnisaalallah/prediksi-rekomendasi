@extends('layouts.app')
@section('title','Data Balita')
@section('content')
<div class="container-fluid mt-2">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title fw-semibold mb-3">Data Balita</h5>
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Nama Balita</th>
              <th>Jenis Kelamin</th>
              <th>Usia (bln)</th>
              <th>Berat (kg)</th>
              <th>Tinggi (cm)</th>
              <th>Status Gizi</th>
              <th>Aksi</th>
              <th>Tanggal</th>
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
  { nama: "Dani Ahmad", jk: "Laki-laki", usia: 24, bb: 9.5, tb: 73, status: "Gizi Kurang", rekom: "Tingkatkan asupan protein hewani seperti telur, ikan, ayam.", tanggal: "09-10-2025" },
  { nama: "Rani Lestari", jk: "Perempuan", usia: 30, bb: 11.2, tb: 80, status: "Gizi Baik", rekom: "Pertahankan pola makan seimbang, tambah buah setiap hari.", tanggal: "09-10-2025" }
];
let dataLocal = JSON.parse(localStorage.getItem('dataBalita') || '[]');
let changed = false;
dataLocal = dataLocal.map((d, idx) => { if (!d._id) { d._id = 'l_' + (Date.now() + idx); changed = true; } return d; });
if (changed) localStorage.setItem('dataBalita', JSON.stringify(dataLocal));
const dataBalita = [...dataStatis, ...dataLocal];
const tbody = document.getElementById('tabelBalita');
function renderTable(){
  tbody.innerHTML = '';
  dataBalita.length && dataBalita.forEach((b, i) => {
    const isLocal = !!b._id;
    let badgeClass = 'secondary';
    if (b.status === 'Gizi Baik') badgeClass = 'success';
    else if (b.status === 'Gizi Kurang') badgeClass = 'warning';
    else if (b.status === 'Gizi Buruk') badgeClass = 'danger';
    else if (b.status === 'Gizi Lebih') badgeClass = 'primary';
    let aksiHtml = '';
    if (isLocal) {
      aksiHtml = `<button class="btn btn-sm btn-outline-primary me-1" data-action="edit" data-id="${b._id}">Edit</button>` +
                `<button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${b._id}">Hapus</button>`;
    }
    let row = `
      <tr>
        <td>${i + 1}</td>
        <td>${b.nama}</td>
        <td>${b.jk}</td>
        <td>${b.usia}</td>
        <td>${b.bb}</td>
        <td>${b.tb}</td>
        <td><span class="badge bg-${badgeClass}">${b.status}</span></td>
        <td>${aksiHtml}</td>
        <td>${b.tanggal || '-'}</td>
      </tr>`;
    tbody.innerHTML += row;
  });
  tbody.querySelectorAll('button[data-action]').forEach(btn => {
    btn.addEventListener('click', (ev) => {
      const act = btn.getAttribute('data-action');
      const id = btn.getAttribute('data-id');
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
            <div class="mb-2"><label class="form-label">Nama</label><input id="editNama" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Jenis Kelamin</label><select id="editJK" class="form-select"><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
            <div class="mb-2"><label class="form-label">Usia (bln)</label><input id="editUsia" class="form-control" type="number"></div>
            <div class="mb-2"><label class="form-label">Berat (kg)</label><input id="editBB" class="form-control" type="number" step="0.1"></div>
            <div class="mb-2"><label class="form-label">Tinggi (cm)</label><input id="editTB" class="form-control" type="number" step="0.1"></div>
            <div class="mb-2"><label class="form-label">Rekomendasi</label><textarea id="editRekom" class="form-control" rows="2"></textarea></div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Hapus</button>
        </div>
      </div>
    </div>
  </div>`;
document.body.appendChild(modalContainer);

const editModalEl = document.getElementById('editModal');
const confirmModalEl = document.getElementById('confirmModal');
const editModal = new bootstrap.Modal(editModalEl);
const confirmModal = new bootstrap.Modal(confirmModalEl);

function openEditModal(id){
  const item = dataLocal.find(x => x._id === id);
  if (!item) return showCrudNotif(false, 'Data tidak ditemukan');
  document.getElementById('editId').value = item._id;
  document.getElementById('editNama').value = item.nama || '';
  document.getElementById('editJK').value = item.jk || '';
  document.getElementById('editUsia').value = item.usia || '';
  document.getElementById('editBB').value = item.bb || '';
  document.getElementById('editTB').value = item.tb || '';
  document.getElementById('editRekom').value = item.rekom || '';
  editModal.show();
}

document.getElementById('saveEditBtn').addEventListener('click', () => {
  const id = document.getElementById('editId').value;
  const idx = dataLocal.findIndex(x => x._id === id);
  if (idx === -1) return showCrudNotif(false, 'Data tidak ditemukan');
  dataLocal[idx].nama = document.getElementById('editNama').value;
  dataLocal[idx].jk = document.getElementById('editJK').value;
  dataLocal[idx].usia = parseFloat(document.getElementById('editUsia').value) || dataLocal[idx].usia;
  dataLocal[idx].bb = parseFloat(document.getElementById('editBB').value) || dataLocal[idx].bb;
  dataLocal[idx].tb = parseFloat(document.getElementById('editTB').value) || dataLocal[idx].tb;
  dataLocal[idx].rekom = document.getElementById('editRekom').value;
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