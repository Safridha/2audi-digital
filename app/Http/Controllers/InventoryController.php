<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Services\StockService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(StockService $stockService)
    {
        $ringkasan = $stockService->getRingkasanStokMingguan();
        $totalBahan = Bahan::count();
        $bahanPerluRestock = Bahan::perluRestok()->count();

        return view('admin.stock.index', [
            'ringkasan'         => $ringkasan,
            'totalBahan'        => $totalBahan,
            'bahanPerluRestock' => $bahanPerluRestock,
        ]);
    }

    // SIMPAN PEMBELIAN (FIFO)
    public function storePembelian(Request $request, StockService $stockService)
    {
        $data = $request->validate([
            'bahan_id' => 'required|exists:bahans,id',
            'qty'      => 'required|numeric|min:0.01',
            'harga'    => 'required|numeric|min:0',
        ]);

        $stockService->tambahStok(
            (int) $data['bahan_id'],
            (float) $data['qty'],
            (float) $data['harga']
        );

        return redirect()
            ->route('admin.stock.index')
            ->with('success', 'Pembelian bahan berhasil disimpan.');
    }

    // SIMPAN PEMAKAIAN (FIFO)
    public function storePemakaian(Request $request, StockService $stockService)
    {
        $data = $request->validate([
            'bahan_id' => 'required|exists:bahans,id',
            'qty'      => 'required|numeric|min:0.01',
        ]);

        try {
            $stockService->pakaiStokFIFO(
                (int) $data['bahan_id'],
                (float) $data['qty'],
                now()->toDateString()
            );

            return redirect()
                ->route('admin.stock.index')
                ->with('success', 'Pemakaian bahan berhasil dicatat.');
        } catch (\Throwable $e) { // tetap jalan meskipun bahan tidak cukup
            return redirect()
                ->route('admin.stock.index')
                ->with('error', 'Gagal mencatat pemakaian: ' . $e->getMessage());
        }
    }

    public function detail(Bahan $bahan)
    {
        $batches = $bahan->stockBatches()
            ->orderBy('tanggal_masuk', 'asc')
            ->get();

        $usages = $bahan->usages()
            ->orderBy('tanggal', 'desc')
            ->limit(20)
            ->get();

        return view('admin.stock._detail', compact('bahan', 'batches', 'usages'));
    }
}
