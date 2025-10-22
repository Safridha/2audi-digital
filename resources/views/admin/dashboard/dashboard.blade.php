<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

            <!-- Line Chart: Stok Bahan Baku -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Stok Bahan Baku</h2>
                <canvas id="stockChart"></canvas>
            </div>

            <!-- Line Chart: Penjualan -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Penjualan</h2>
                <canvas id="salesChart"></canvas>
            </div>

            <!-- Navigasi Admin -->
            <div class="grid grid-cols-5 gap-4 mt-6">
                <a href="{{ route('admin.categories.index') }}" class="p-4 bg-blue-500 text-white rounded text-center">Kelola Kategori</a>
                <a href="{{ route('admin.products.index') }}" class="p-4 bg-green-500 text-white rounded text-center">Produk</a>
                <a href="{{ route('admin.stock.index') }}" class="p-4 bg-yellow-500 text-white rounded text-center">Stok Bahan</a>
                <a href="{{ route('admin.orders.index') }}" class="p-4 bg-purple-500 text-white rounded text-center">Pesanan</a>
                <a href="{{ route('admin.users.index') }}" class="p-4 bg-red-500 text-white rounded text-center">Pengguna</a>
            </div>
        </div>
    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxStock = document.getElementById('stockChart').getContext('2d');
        new Chart(ctxStock, {
            type: 'line',
            data: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                datasets: [{
                    label: 'Stok Bahan',
                    data: [120, 150, 100, 180],
                    borderColor: 'blue',
                    fill: false
                }]
            }
        });

        const ctxSales = document.getElementById('salesChart').getContext('2d');
        new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'],
                datasets: [{
                    label: 'Penjualan Harian',
                    data: [50, 75, 60, 80, 90, 100, 70],
                    borderColor: 'green',
                    fill: false
                }]
            }
        });
    </script>
</x-app-layout>
