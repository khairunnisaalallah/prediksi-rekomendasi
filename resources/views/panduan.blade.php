@extends('layouts.app')
@section('title','Panduan')
@section('content')
<div class="container-fluid mt-2">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title fw-semibold mb-3">Panduan Penggunaan Sistem</h5>

      <p>Halaman ini membantu kader posyandu memahami langkah-langkah menggunakan sistem rekomendasi gizi balita berbasis AI (SLM).</p>

      <h6 class="fw-semibold mt-4">🧭 Navigasi Menu</h6>
      <ul>
        <li><b>🏠 Dashboard:</b> Menampilkan ringkasan jumlah balita, grafik status gizi, dan hasil diagnosis terbaru.</li>
        <li><b>🧒 Diagnosis Gizi:</b> Isi data balita → klik <i>Diagnosis</i> → hasil status & rekomendasi muncul otomatis.</li>
        <li><b>📋 Data Balita:</b> Menampilkan seluruh hasil diagnosis yang pernah dilakukan.</li>
        <li><b>❓ Panduan:</b> Berisi petunjuk penggunaan sistem (halaman ini).</li>
      </ul>

      <h6 class="fw-semibold mt-4">🩺 Langkah-Langkah Melakukan Diagnosis</h6>
      <ol>
        <li>Masuk ke menu <b>Diagnosis Gizi</b>.</li>
        <li>Isi data balita lengkap: Nama, Jenis Kelamin, Usia, Berat, Tinggi.</li>
        <li>Klik tombol <b>Diagnosis</b>.</li>
        <li>Tunggu hasil muncul di sisi kanan: status gizi & rekomendasi gizi AI.</li>
        <li>Klik <b>Simpan</b> jika ingin menambah ke daftar Data Balita.</li>
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

      <h6 class="fw-semibold mt-4">🍎 Tips Membaca Rekomendasi AI</h6>
      <p>Rekomendasi yang ditampilkan bersifat otomatis dan informatif. Jika hasil menunjukkan <b>Gizi Kurang</b> atau <b>Gizi Buruk</b>, segera sarankan orang tua untuk berkonsultasi ke puskesmas terdekat.</p>
    </div>
  </div>
</div>
@endSection
