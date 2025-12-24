<?php

namespace App\Services;

use App\Models\Bahan;
use App\Models\StockBatch;
use App\Models\BahanUsage;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function tambahStok(int $bahanId, float $qty, float $harga): StockBatch
    {
        return DB::transaction(function () use ($bahanId, $qty, $harga) {

            $batch = StockBatch::create([
                'bahan_id'      => $bahanId,
                'tanggal_masuk' => now()->toDateString(),
                'qty_awal'      => $qty,
                'qty_sisa'      => $qty,
                'harga_satuan'  => $harga,
            ]);

            // update kolom stok di tabel bahans 
            if ($batch->bahan) {
                $batch->bahan->recalcStokFromBatches();
            }

            return $batch;
        });
    }

    // Pemakaian stok dengan algoritma FIFO
    public function pakaiStokFIFO(int $bahanId, float $qty, ?string $tanggal = null): void
    {
        DB::transaction(function () use ($bahanId, $qty, $tanggal) {

            $bahan = Bahan::findOrFail($bahanId);

            // Cek stok 
            $stokAktual = $this->getStokAktual($bahan);
            if ($qty > $stokAktual) {
                throw new \Exception(
                    "Stok {$bahan->nama_bahan} tidak cukup. "
                    . "Diminta {$qty} {$bahan->satuan}, tersedia {$stokAktual} {$bahan->satuan}."
                );
            }

            // Ambil batch FIFO & kunci row untuk hindari race condition
            $batches = StockBatch::where('bahan_id', $bahanId)
                ->where('qty_sisa', '>', 0)
                ->orderBy('tanggal_masuk', 'asc')
                ->lockForUpdate()
                ->get();

            $sisaPermintaan = $qty;

            foreach ($batches as $batch) {
                if ($sisaPermintaan <= 0) {
                    break;
                }

                $ambil = min($batch->qty_sisa, $sisaPermintaan);

                $batch->qty_sisa -= $ambil;
                $batch->save();

                $sisaPermintaan -= $ambil;
            }

            // Catat pemakaian di tabel bahan_usages
            BahanUsage::create([
                'bahan_id' => $bahanId,
                'tanggal'  => $tanggal ?? now()->toDateString(),
                'qty'      => $qty,
            ]);
            $bahan->recalcStokFromBatches();
        });
    }

    // Hitung stok aktual: jumlah semua qty_sisa dari batch bahan 
    public function getStokAktual(Bahan $bahan): float
    {
        return (float) $bahan->stockBatches()->sum('qty_sisa');
    }

    // Total pemakaian dalam periode tertentu
    public function getPemakaianPeriode(Bahan $bahan, string $start, string $end): float
    {
        return (float) $bahan->usages()
            ->whereBetween('tanggal', [$start, $end])
            ->sum('qty');
    }

    // Rumus "Martingale" sederhana berbasis permintaan:
    public function hitungTargetStokMartingale(float $Q, float $D, float $k = 0.5): float
    {
        return $Q + $k * ($D - $Q);
    }

    // Ringkasan stok mingguan per bahan: stok sekarang, pemakaian 7 hari, target stok, rekomendasi pembelian
    public function getRingkasanStokMingguan(float $k = 0.5): array
    {
        $today = Carbon::today();
        $start = $today->copy()->subDays(6)->toDateString(); 
        $end   = $today->toDateString();

        $data = [];

        foreach (Bahan::all() as $bahan) {

            $stokSekarang = $this->getStokAktual($bahan);
            $pemakaian    = $this->getPemakaianPeriode($bahan, $start, $end);

            $targetStokMartingale = $this->hitungTargetStokMartingale(
                0,
                $pemakaian,
                $k
            );

            $targetStok = max(
                $targetStokMartingale,
                $bahan->minimal_stock ?? 0
            );

            $rekomendasi = max(0, $targetStok - $stokSekarang);

            $data[] = [
                'bahan'          => $bahan,
                'stok_sekarang'  => round($stokSekarang, 2),
                'pemakaian'      => round($pemakaian, 2),
                'target_stok'    => round($targetStok, 2),
                'rekomendasi'    => round($rekomendasi, 2),
                'periode_start'  => $start,
                'periode_end'    => $end,
            ];
        }

        return $data;
    }

    public function applyUsageFromOrder(Order $order): void
    {
        $order->loadMissing('items.product.bahans');

        if ($order->items->isEmpty()) {
            return;
        }

        $tanggal = optional($order->created_at)->toDateString() ?? now()->toDateString();

        // Akumulasi kebutuhan per bahan dari SEMUA item
        $needs = []; 

        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product) continue;

            $qty = (float) ($item->quantity ?? 1);

            $area = $item->area;
            if (is_null($area)) {
                $panjang = (float) ($item->length ?? 0);
                $lebar   = (float) ($item->width  ?? 0);
                $area    = $panjang * $lebar;
            } else {
                $area = (float) $area;
            }

            $luasTotal = $area * $qty;
            if ($luasTotal <= 0) continue;

            foreach ($product->bahans as $bahan) {
                $faktorPerM2 = (float) ($bahan->pivot->qty_per_unit ?? 0);
                if ($faktorPerM2 <= 0) continue;

                $totalPemakaian = $luasTotal * $faktorPerM2;

                if (!isset($needs[$bahan->id])) {
                    $needs[$bahan->id] = 0.0;
                }
                $needs[$bahan->id] += $totalPemakaian;
            }
        }

        // Simpan daftar kekurangan untuk WA
        $shortages = [];

        foreach ($needs as $bahanId => $qtyNeed) {
            $bahan = Bahan::find($bahanId);
            if (! $bahan) continue;

            $stokAktual = $this->getStokAktual($bahan);

            // ambil semaksimal mungkin (biar stok real turun)
            $qtyTake = (float) min($qtyNeed, $stokAktual);

            if ($qtyTake > 0) {
                try {
                    $this->pakaiStokFIFO($bahanId, $qtyTake, $tanggal);
                } catch (\Throwable $e) {
                    continue; // error lain jangan ganggu proses order
                }
            }

            $kurang = (float) ($qtyNeed - $qtyTake);

            if ($kurang > 0) {
                // hitung target (martingale + minimal) supaya WA bisa kasih saran buffer juga
                $today = Carbon::today();
                $start = $today->copy()->subDays(6)->toDateString();
                $end   = $today->toDateString();

                $pemakaian7Hari = $this->getPemakaianPeriode($bahan, $start, $end);
                $targetMartingale = $this->hitungTargetStokMartingale(0, $pemakaian7Hari, 0.5);
                $targetStok = max($targetMartingale, $bahan->minimal_stock ?? 0);

                $shortages[$bahanId] = [
                    'nama'         => $bahan->nama_bahan,
                    'satuan'       => $bahan->satuan,
                    'butuh'        => (float) $qtyNeed,
                    'terpakai'     => (float) $qtyTake,
                    'kurang'       => (float) $kurang,    
                    'target_stok'  => (float) $targetStok, 
                ];
            }
        }

        // KIRIM WA kalau ada bahan kurang (order tetap jalan)
        if (!empty($shortages)) {
            $adminWa = env('WA_ADMIN');
            if ($adminWa) {
                $lines = [];
                $lines[] = "⚠️ Reminder Restock (Order #{$order->id})";
                $lines[] = "Pemesan: {$order->customer_name}";
                $lines[] = "Tanggal: " . ($order->created_at?->format('d-m-Y H:i') ?? '-');
                $lines[] = "";
                $lines[] = "Bahan kurang (stok sudah dipotong semaksimal mungkin):";

                foreach ($shortages as $s) {
                    $nama   = $s['nama'];
                    $butuh  = round((float) $s['butuh'], 2);
                    $pakai  = round((float) $s['terpakai'], 2);
                    $kurang = round((float) $s['kurang'], 2);
                    $target = round((float) $s['target_stok'], 2);
                    $sat    = $s['satuan'] ? " {$s['satuan']}" : "";

                    $saranTotal = round($kurang + $target, 2);

                    $lines[] = "- {$nama}: butuh {$butuh}{$sat}, terpakai {$pakai}{$sat}, kurang {$kurang}{$sat}";
                    $lines[] = "  Saran beli: {$kurang}{$sat} (tutup order) + {$target}{$sat} (buffer target) = {$saranTotal}{$sat}";
                }

                $lines[] = "";
                $lines[] = "Catatan: status order tetap bisa diproses. Mohon segera lakukan pembelian bahan di atas.";

                $msg = implode("\n", $lines);

                try {
                    $wa = app(\App\Services\WhatsappService::class);
                    $wa->sendMessage($adminWa, $msg);
                } catch (\Throwable $e) {
                    // kalau WA error, jangan ganggu proses order
                }
            }
        }
    }
}
