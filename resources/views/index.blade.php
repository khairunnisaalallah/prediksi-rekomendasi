@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div class="container-fluid mt-2">
  <div class="row">
    <!-- Grafik Kiri -->
    <div class="col-lg-8 d-flex align-items-stretch">
      <div class="card w-100">
        <div class="card-body">
          <div class="d-sm-flex d-block align-items-center justify-content-between mb-4">
            <h5 class="card-title fw-semibold">Perkembangan Status Gizi Balita (Per Bulan)</h5>
            <select class="form-select w-auto">
              <option>2025</option>
              <option>2024</option>
            </select>
          </div>
          <div class="chart-wrapper" style="position:relative;">
            <div id="chart"></div>
            <div id="chartMonths" aria-hidden="true" style="position:absolute;left:50%;transform:translateX(-50%);bottom:22px;pointer-events:none;color:rgba(0,0,0,0.65);font-size:12px;line-height:1;font-family:inherit;font-weight:400;">
              <!-- month labels injected by script -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistik Kanan -->
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-body p-4">
          <h5 class="card-title mb-3 fw-semibold">Total Balita Terdata</h5>
          <h3 class="fw-bold text-primary">128</h3>
          <p class="text-muted mb-0">Jumlah total balita yang sudah tercatat dalam sistem</p>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-body p-4">
          <h5 class="card-title mb-3 fw-semibold">Status Gizi Baik</h5>
          <h3 class="fw-bold text-success">80</h3>
          <p class="text-muted mb-0">Balita dengan status gizi baik (AI & perhitungan Z-score)</p>
        </div>
      </div>

      <div class="card">
        <div class="card-body p-4">
          <h5 class="card-title mb-3 fw-semibold">Kasus Gizi Kurang / Buruk</h5>
          <h3 class="fw-bold text-danger">12</h3>
          <p class="text-muted mb-0">Perlu perhatian & tindak lanjut kader posyandu</p>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // === Chart: Perkembangan Status Gizi ===
  const bulanShort = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const seriesData = [
    { name: 'Gizi Baik', data: [70,72,75,78,80,82,83,85,87,88,89,90] },
    { name: 'Gizi Kurang', data: [25,24,22,20,18,17,16,15,14,13,13,12] },
    { name: 'Gizi Buruk', data: [3,2,2,2,2,1,1,1,1,1,1,1] },
    { name: 'Gizi Lebih', data: [2,2,1,0,0,0,0,0,0,0,0,0] },
  ];

  const categories = bulanShort.slice(0, (seriesData[0] && seriesData[0].data) ? seriesData[0].data.length : 12);

  (function(){
    const c = document.getElementById('chartMonths');
    const chartEl = document.getElementById('chart');
    const wrapper = document.querySelector('.chart-wrapper');
    if (!c || !chartEl || !wrapper) return;

    function positionLabels() {
      c.innerHTML = '';
      const markers = document.querySelectorAll('#chart .apexcharts-series.apexcharts-series-0 .apexcharts-marker');
      if (markers && markers.length >= categories.length) {
        const wrapRect = wrapper.getBoundingClientRect();
        for (let i = 0; i < categories.length; i++) {
          const mk = markers[i]; if (!mk) continue;
          const r = mk.getBoundingClientRect();
          const centerX = r.left + r.width / 2;
          const left = Math.round(centerX - wrapRect.left);
          const span = document.createElement('span');
          span.textContent = categories[i];
          span.style.position = 'absolute';
          const labelNudge = 6;
          span.style.left = (left - 22 + labelNudge) + 'px';
          span.style.bottom = '22px';
          span.style.width = '44px';
          span.style.textAlign = 'center';
          span.style.color = 'rgba(0,0,0,0.65)';
          span.style.fontSize = '12px';
          span.style.fontFamily = 'inherit';
          span.style.fontWeight = '300';
          c.appendChild(span);
        }
        c.style.width = wrapper.clientWidth + 'px';
        c.style.left = '0';
        c.style.transform = '';
      } else {
        const chartWidth = chartEl.clientWidth || 600;
        const spanWidth = Math.max(36, Math.floor(chartWidth / categories.length));
        c.innerHTML = categories.map(m => `<span style="display:inline-block;width:${spanWidth}px;text-align:center;color:rgba(0,0,0,0.65);font-size:12px;font-family:inherit;font-weight:400;">${m}</span>`).join('');
        c.style.width = (spanWidth * categories.length) + 'px';
        c.style.left = '53%';
        c.style.transform = 'translateX(-50%)';
      }
    }

    window.__updateMonthLabels = positionLabels;
    window.addEventListener('resize', positionLabels);
  })();

  const options = {
    chart: { type: 'line', height: 300, toolbar: { show: false } },
    series: seriesData,
    xaxis: { categories: categories, labels: { show: false }, title: { text: 'Bulan' }, offsetY: 55 },
    yaxis: { title: { text: 'Jumlah Balita' }, min: 0, max: 100 },
    stroke: { width: 3, curve: 'smooth' },
    markers: { size: 5 },
    colors: ['#28a745', '#ffc107', '#dc3545', '#0d6efd'],
    legend: { position: 'top', horizontalAlign: 'center' },
    tooltip: { shared: true, intersect: false, theme: 'light' }
  };

  const chart = new ApexCharts(document.querySelector("#chart"), options);
  chart.render().then(() => { if (window.__updateMonthLabels) window.__updateMonthLabels(); });
</script>
@endpush
@endsection