@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        Dashboard Admin
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- BAHAN PALING BANYAK DIPAKAI --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex justify-between items-center mb-2">
                <h2 class="font-semibold">Bahan Paling Banyak Dipakai</h2>

                <form method="GET">
                    <select name="satuan"
                        onchange="this.form.submit()"
                        class="text-xs border rounded px-2 py-1">
                        @foreach ($listSatuan as $satuan)
                            <option value="{{ $satuan }}" {{ $selectedSatuan == $satuan ? 'selected' : '' }}>
                                {{ strtoupper($satuan) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div style="height:160px;">
                <canvas id="topBahanChart"></canvas>
            </div>
        </div>

        {{-- TREN PEMAKAIAN BAHAN (MULTI-LINE) --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="font-semibold mb-2">
                Tren Pemakaian Bahan ({{ strtoupper($chartTrenBahan['satuan']) }})
            </h2>

            <div style="height:160px;">
                <canvas id="trenBahanChart"></canvas>
            </div>
        </div>

        {{-- STOCK RISK --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="font-semibold mb-2">Stock Risk Indicator</h2>

            <div style="height:160px;">
                <canvas id="riskStokChart"></canvas>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('topBahanChart'), {
    type: 'bar',
    data: {
        labels: @json($chartTopBahan['labels']),
        datasets: [{
            data: @json($chartTopBahan['data']),
            backgroundColor: 'rgba(59,130,246,0.8)'
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});

new Chart(document.getElementById('trenBahanChart'), {
    type: 'line',
    data: {
        labels: @json($chartTrenBahan['labels']),
        datasets: @json($chartTrenBahan['datasets'])
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } }
    }
});

new Chart(document.getElementById('riskStokChart'), {
    type: 'doughnut',
    data: {
        labels: @json($chartRiskStok['labels']),
        datasets: [{
            data: @json($chartRiskStok['data']),
            backgroundColor: ['#22c55e','#eab308','#ef4444']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endsection
