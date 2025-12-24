@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">
        Dashboard Admin
    </h1>

    {{-- GRID 3 KARTU: Kiri = Top 5 Bahan, Tengah = Tren, Kanan = Risk --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- KIRI: TOP 5 BAHAN PALING BANYAK DIPAKAI --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow flex flex-col">
            <h2 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-100">
                Top 5 Bahan Paling Banyak Dipakai
            </h2>
            <p class="text-xs text-gray-500 mb-2">
                Membantu kamu tahu bahan mana yang perlu distok lebih banyak.
            </p>
            <div class="flex-1">
                <canvas id="topBahanChart" class="w-full h-56"></canvas>
            </div>
        </div>

        {{-- TENGAH: TREN PEMAKAIAN BAHAN PER BULAN --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow flex flex-col">
            <h2 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-100">
                Tren Pemakaian Bahan (6 Bulan Terakhir)
            </h2>
            <p class="text-xs text-gray-500 mb-2">
                Lihat tren naik-turun kebutuhan bahan dari waktu ke waktu.
            </p>
            <div class="flex-1">
                <canvas id="trenBahanChart" class="w-full h-56"></canvas>
            </div>
        </div>

        {{-- KANAN: STOCK RISK INDICATOR --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow flex flex-col">
            <h2 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-100">
                Stock Risk Indicator
            </h2>
            <p class="text-xs text-gray-500 mb-2">
                Indikator cepat bahan yang aman, waspada, atau kritis.
            </p>
            <div class="flex-1">
                <canvas id="riskStokChart" class="w-full h-56"></canvas>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Data dari controller (PHP -> JS)
    const topLabels   = @json($chartTopBahan['labels']);
    const topData     = @json($chartTopBahan['data']);

    const trenLabels  = @json($chartTrenBahan['labels']);
    const trenData    = @json($chartTrenBahan['data']);

    const riskLabels  = @json($chartRiskStok['labels']);
    const riskData    = @json($chartRiskStok['data']);

    // 1. TOP 5 BAHAN (BAR CHART HORIZONTAL)
    const ctxTop = document.getElementById('topBahanChart').getContext('2d');
    new Chart(ctxTop, {
        type: 'bar',
        data: {
            labels: topLabels,
            datasets: [{
                label: 'Qty terpakai',
                data: topData,
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });

    // 2. TREN PEMAKAIAN BAHAN (LINE CHART)
    const ctxTren = document.getElementById('trenBahanChart').getContext('2d');
    new Chart(ctxTren, {
        type: 'line',
        data: {
            labels: trenLabels,
            datasets: [{
                label: 'Total pemakaian',
                data: trenData,
                borderColor: '#6366F1',
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // 3. STOCK RISK INDICATOR (DOUGHNUT CHART)
    const ctxRisk = document.getElementById('riskStokChart').getContext('2d');
    new Chart(ctxRisk, {
        type: 'doughnut',
        data: {
            labels: riskLabels,
            datasets: [{
                data: riskData,
                backgroundColor: [
                    'rgba(34, 197, 94, 0.85)',   // Aman
                    'rgba(234, 179, 8, 0.85)',   // Waspada
                    'rgba(239, 68, 68, 0.85)',   // Kritis
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection
