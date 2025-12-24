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
        // TOP 5 BAHAN PALING BANYAK DIPAKAI
        $topBahan = Bahan::withSum('usages as total_usage', 'qty')
            ->orderByDesc('total_usage')
            ->take(5)
            ->get();

        $chartTopBahan = [
            'labels' => $topBahan->pluck('nama_bahan'),
            'data'   => $topBahan->pluck('total_usage')->map(fn ($v) => (float) $v),
        ];

        //TREN PEMAKAIAN BAHAN PER BULAN (6 BULAN TERAKHIR)
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();

        $usageByMonth = BahanUsage::whereDate('tanggal', '>=', $startDate)
            ->selectRaw('DATE_FORMAT(tanggal, "%Y-%m") as bulan, SUM(qty) as total_qty')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $monthLabels = [];
        $monthData   = [];

        for ($i = 0; $i < 6; $i++) {
            $month   = $startDate->copy()->addMonths($i);
            $key     = $month->format('Y-m');
            $label   = $month->translatedFormat('M Y');
            $row     = $usageByMonth->firstWhere('bulan', $key);

            $monthLabels[] = $label;
            $monthData[]   = $row ? (float) $row->total_qty : 0;
        }

        $chartTrenBahan = [
            'labels' => $monthLabels,
            'data'   => $monthData,
        ];

        // STOCK RISK INDICATOR
        $bahans = Bahan::withSum('stockBatches as current_stock', 'qty_sisa')->get();

        $riskCount = [
            'aman'    => 0,
            'waspada' => 0,
            'kritis'  => 0,
        ];

        foreach ($bahans as $bahan) {
            $stock = (float) ($bahan->current_stock ?? 0);
            $min   = (float) ($bahan->minimal_stock ?? 0);

            // kalau minimal stok tidak di-set, anggap aman
            if ($min <= 0) {
                $riskCount['aman']++;
                continue;
            }

            if ($stock <= 0 || $stock < 0.5 * $min) {
                $riskCount['kritis']++;
            } elseif ($stock < $min) {
                $riskCount['waspada']++;
            } else {
                $riskCount['aman']++;
            }
        }

        $chartRiskStok = [
            'labels' => ['Aman', 'Waspada', 'Kritis'],
            'data'   => [
                $riskCount['aman'],
                $riskCount['waspada'],
                $riskCount['kritis'],
            ],
        ];

        return view('admin.dashboard', compact(
            'chartTopBahan',
            'chartTrenBahan',
            'chartRiskStok'
        ));
    }
}
