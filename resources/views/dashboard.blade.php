@extends('layouts.app')

<!-- @section('title', 'Dashboard') -->

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4 ms-2">
        <h4 class="fw-bold" style="color: #0050A5;">Dashboard</h4>
    </div>
    <!-- Top Section (Chart + Prediksi) -->
    <div class="row mb-3">
        <!-- Chart -->
        <div class="col-md-7">
            <div class="bg-white p-3 rounded shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-bar-chart-line-fill me-2 text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold text-primary-emphasis border-bottom pb-1">Obat yang Sering Digunakan</h5>
                </div>
                <canvas id="chartObat"></canvas>
            </div>
        </div>

        <!-- Prediksi Kebutuhan -->
        <div class="col-md-5">
            <div class="bg-white p-3 rounded shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-graph-up-arrow me-2 text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold text-primary-emphasis border-bottom pb-1">Prediksi Kebutuhan</h5>
                </div>
                <div style="max-height: 300px; overflow-y: auto;" class="mt-2">
                    <div class="grid grid-cols-1 gap-2">
                        @foreach ($prediksi as $item)
                            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-blue-500 hover:shadow-xl transition duration-300">
                                <h3 class="text-xl font-semibold text-gray-800 mb-1">{{ $item['nama'] }}</h3>
                                <p class="text-sm text-gray-600 mb-1">
                                    <span class="font-medium">Total 6 bulan terakhir:</span> {{ $item['total'] }} {{ $item['jenis'] }}
                                </p>
                                <p class="text-sm text-gray-600 mb-1">
                                    <span class="font-medium">Rata-rata:</span> {{ $item['rata'] }}/bulan
                                </p>
                                <p class="text-sm text-gray-800 font-medium mt-1">
                                    👉 <span class="text-blue-600">Prediksi pembelian:</span> {{ $item['prediksi'] }} {{ $item['jenis'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Alert: Stok Expired -->
    <div class="bg-white p-4 rounded shadow-sm mb-4">
        <h6 class="fw-bold mb-3 text-primary-emphasis">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Stock Alert (Obat Expired)
        </h6>
        <div class="table-responsive">
            <table class="table table-sm align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Stok</th>
                        <th>Expired</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expiredAlert as $obat)
                    <tr class="{{ $obat['status'] == 'Expired' ? 'table-danger' : 'table-warning' }}"
                        ondblclick="window.location.href=`/stokobat#obat-{{ $obat['id'] }}`">
                        <td>{{ $obat['nama'] }}</td>
                        <td>{{ $obat['jenis'] }}</td>
                        <td>{{ $obat['stok'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($obat['expired'])->format('d F Y') }}</td>
                        <td class="{{ $obat['status'] == 'Expired' ? 'text-danger fw-semibold' : 'text-warning fw-semibold' }}">
                            {{ $obat['status'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stock Alert: Stok Habis -->
    <div class="bg-white p-4 rounded shadow-sm">
        <h6 class="fw-bold mb-3 text-primary-emphasis">
            <i class="bi bi-x-circle-fill me-1"></i> Stock Alert (Stok Habis)
        </h6>
        <div class="table-responsive">
            <table class="table table-sm align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stokAlert as $obat)
                    <tr class="{{ $obat['status'] == 'Habis' ? 'table-danger' : 'table-warning' }}"
                        ondblclick="window.location.href=`/stokobat#obat-{{ $obat['id'] }}`">
                        <td>{{ $obat['nama'] }}</td>
                        <td>{{ $obat['jenis'] }}</td>
                        <td>{{ $obat['stok'] }}</td>
                        <td class="{{ $obat['status'] == 'Habis' ? 'text-danger fw-semibold' : 'text-warning fw-semibold' }}">
                            {{ $obat['status'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = JSON.parse('<?= json_encode($labels) ?>');
    const datasets = JSON.parse('<?= json_encode($chartData) ?>');

    // Fungsi untuk menghasilkan warna HEX acak yang terang
    function getRandomColor(hueShift = 0) {
        const hue = Math.floor((Math.random() * 360) + hueShift) % 360;
        return `hsl(${hue}, 70%, 60%)`; // pakai format HSL biar terang & konsisten
    }

    // Set warna unik untuk setiap dataset
    const usedColors = new Set();
    function getUniqueColor() {
        let color;
        do {
            color = getRandomColor();
        } while (usedColors.has(color));
        usedColors.add(color);
        return color;
    }

    const chart = new Chart(document.getElementById('chartObat').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets.map((d) => {
                const color = getUniqueColor();
                return {
                    label: d.label,
                    data: d.data,
                    borderColor: color,
                    backgroundColor: 'transparent',
                    tension: 0.3,
                };
            })
        },
        options: {
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
