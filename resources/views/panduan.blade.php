@extends('layouts.app')
@section('title','Panduan')
@section('content')
<div class="container-fluid mt-2">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title fw-semibold mb-3">Panduan Penggunaan Sistem</h5>

      <p>Halaman ini membantu kader posyandu memahami langkah‑langkah menggunakan sistem dan alur data antar halaman.</p>

      <h6 class="fw-semibold mt-4">Navigasi Menu</h6>
      <ul>
        <li><b>Dashboard:</b> Ringkasan dan statistik.</li>
        <li><b>Data Balita:</b> Tambah/kelola data dasar balita (Nama, JK, Tanggal Lahir, BB/TB opsional). Usia dihitung otomatis dari tanggal lahir.</li>
        <li><b>Diagnosis Gizi:</b> Pilih balita dari dropdown, isi/cek BB & TB, lalu klik <i>Diagnosis</i> untuk mendapat status & rekomendasi. Hasilnya akan memperbarui data balita, bukan menambah baris baru.</li>
        <li><b>Prediksi:</b> Melihat prediksi bulan depan per balita (tanpa tombol edit/hapus). Tanggal lahir tampil sebelum usia.</li>
        <li><b>Panduan:</b> Petunjuk penggunaan sistem (halaman ini).</li>
      </ul>

      <h6 class="fw-semibold mt-4">Langkah Tambah/Kelola Data Balita</h6>
      <ol>
        <li>Buka menu <b>Data Balita</b>, klik <b>Tambah Data</b>.</li>
        <li>Isi <b>Nama</b>, pilih <b>Jenis Kelamin</b> (mulai dari "Pilih jenis kelamin"), dan pilih <b>Tanggal Lahir</b> dari kalender.</li>
        <li>Kolom <b>Usia (bln)</b> terisi otomatis dari tanggal lahir. <b>Berat</b> dan <b>Tinggi</b> bersifat opsional.</li>
        <li>Klik <b>Simpan</b>. Data muncul di tabel dengan tombol edit/hapus.</li>
        <li>Untuk mengubah/hapus, gunakan ikon <i>pensil</i>/<i>tempat sampah</i> pada baris data.</li>
      </ol>

      <h6 class="fw-semibold mt-4">Langkah Diagnosis Gizi</h6>
      <ol>
        <li>Pastikan data balita sudah ada di menu <b>Data Balita</b>.</li>
        <li>Buka menu <b>Diagnosis Gizi</b>, pilih <b>Nama Balita</b> dari dropdown (diambil dari Data Balita).</li>
        <li>Sistem akan mengisi otomatis <b>JK</b>, <b>Tanggal Lahir</b>, dan menghitung <b>Usia</b>. Lengkapi/cek <b>BB</b> dan <b>TB</b> bila perlu.</li>
        <li>Klik tombol <b>Diagnosis</b> untuk melihat status gizi dan rekomendasi.</li>
        <li>Hasil diagnosis akan <b>memperbarui</b> data balita yang dipilih (tidak menambah baris baru).</li>
      </ol>

      <h6 class="fw-semibold mt-4">📊 Arti Warna Status Gizi</h6>
      <div class="table-responsive">
        <table class="table table-bordered align-middle" style="max-width:400px;">
          <thead class="table-light">
            <tr><th>Warna</th><th>Keterangan</th></tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="badge" style="background-color:#28a745;">&nbsp;&nbsp;&nbsp;</span></td>
              <td>Gizi Baik</td>
            </tr>
            <tr>
              <td><span class="badge" style="background-color:#ffc107; color:#000;">&nbsp;&nbsp;&nbsp;</span></td>
              <td>Gizi Kurang</td>
            </tr>
            <tr>
              <td><span class="badge" style="background-color:#dc3545;">&nbsp;&nbsp;&nbsp;</span></td>
              <td>Gizi Buruk</td>
            </tr>
            <tr>
              <td><span class="badge" style="background-color:#0d6efd;">&nbsp;&nbsp;&nbsp;</span></td>
              <td>Gizi Lebih</td>
            </tr>
          </tbody>
        </table>
      </div>

      <h6 class="fw-semibold mt-4">Halaman Prediksi</h6>
      <ul>
        <li>Menampilkan data balita (dari Data Balita) dengan kolom: Tanggal Lahir sebelum Usia.</li>
        <li>Kolom terakhir berisi <b>Prediksi Bulan Depan</b> (perkiraan BB/TB/Usia dan status).</li>
        <li>Tidak ada tombol edit/hapus pada halaman ini.</li>
      </ul>

      <h6 class="fw-semibold mt-4">Tips Membaca Rekomendasi AI</h6>
      <p>Rekomendasi bersifat informatif. Jika hasil menunjukkan <b>Gizi Kurang</b> atau <b>Gizi Buruk</b>, arahkan untuk konsultasi ke fasilitas kesehatan terdekat.</p>
    </div>
  </div>
</div>
@endSection
