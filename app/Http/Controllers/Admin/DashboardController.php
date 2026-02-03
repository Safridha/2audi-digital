<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\BahanUsage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER SATUAN
        |--------------------------------------------------------------------------
        */
        $listSatuan = Bahan::select('satuan')
            ->whereNotNull('satuan')
            ->distinct()
            ->pluck('satuan');

        $selectedSatuan = request('satuan', $listSatuan->first());

        /*
        |--------------------------------------------------------------------------
        | BAHAN PALING BANYAK DIPAKAI (BAR)
        |--------------------------------------------------------------------------
        */
        $topBahan = Bahan::where('satuan', $selectedSatuan)
            ->withSum('usages as total_usage', 'qty')
            ->orderByDesc('total_usage')
            ->get();

        $chartTopBahan = [
            'labels' => $topBahan->pluck('nama_bahan'),
            'data'   => $topBahan->pluck('total_usage')->map(fn ($v) => (float) $v),
        ];

        /*
        |--------------------------------------------------------------------------
        | TREN PEMAKAIAN BAHAN (MULTI-LINE, 1 SATUAN)
        |--------------------------------------------------------------------------
        */
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        $months = [];

        for ($i = 0; $i < 6; $i++) {
            $months[] = $startDate->copy()->addMonths($i);
        }

        $labels = collect($months)->map(fn ($m) => $m->translatedFormat('M Y'))->toArray();

        $bahans = Bahan::where('satuan', $selectedSatuan)->get();

        $palette = [
            '#22c55e', '#8b5cf6', '#3b82f6',
            '#f97316', '#ef4444', '#14b8a6',
            '#a855f7', '#0ea5e9'
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($bahans as $bahan) {
            $usagePerMonth = BahanUsage::where('bahan_id', $bahan->id)
                ->whereDate('tanggal', '>=', $startDate)
                ->selectRaw('DATE_FORMAT(tanggal, "%Y-%m") as bulan, SUM(qty) as total_qty')
                ->groupBy('bulan')
                ->pluck('total_qty', 'bulan');

            $data = [];
            foreach ($months as $m) {
                $key = $m->format('Y-m');
                $data[] = (float) ($usagePerMonth[$key] ?? 0);
            }

            $datasets[] = [
                'label' => $bahan->nama_bahan,
                'data' => $data,
                'borderColor' => $palette[$colorIndex % count($palette)],
                'backgroundColor' => 'transparent',
                'tension' => 0.3,
            ];

            $colorIndex++;
        }

        $chartTrenBahan = [
            'labels' => $labels,
            'datasets' => $datasets,
            'satuan' => $selectedSatuan,
        ];

        /*
        |--------------------------------------------------------------------------
        | STOCK RISK INDICATOR
        |--------------------------------------------------------------------------
        */
        $bahansAll = Bahan::withSum('stockBatches as current_stock', 'qty_sisa')->get();

        $riskCount = ['aman' => 0, 'waspada' => 0, 'kritis' => 0];

        foreach ($bahansAll as $bahan) {
            $stock = (float) ($bahan->current_stock ?? 0);
            $min   = (float) ($bahan->minimal_stock ?? 0);

            if ($min <= 0) {
                $riskCount['aman']++;
            } elseif ($stock <= 0 || $stock < 0.5 * $min) {
                $riskCount['kritis']++;
            } elseif ($stock < $min) {
                $riskCount['waspada']++;
            } else {
                $riskCount['aman']++;
            }
        }

        $chartRiskStok = [
            'labels' => ['Aman', 'Waspada', 'Kritis'],
            'data'   => array_values($riskCount),
        ];

        return view('admin.dashboard', compact(
            'chartTopBahan',
            'chartTrenBahan',
            'chartRiskStok',
            'listSatuan',
            'selectedSatuan'
        ));
    }
}
