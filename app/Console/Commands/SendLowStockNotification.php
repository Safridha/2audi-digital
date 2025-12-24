<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bahan;
use App\Services\WhatsappService;
use App\Services\StockService;
use Illuminate\Support\Facades\Log;

class SendLowStockNotification extends Command
{
    protected $signature = 'stock:notify-low';
    protected $description = 'Kirim notifikasi WA jika ada bahan yang perlu restok';

    public function handle(WhatsappService $wa, StockService $stockService)
    {
        $bahans = Bahan::whereColumn('stok', '<=', 'minimal_stock')
            ->where('low_stock_notified', false)
            ->orderBy('nama_bahan')
            ->get();

        if ($bahans->isEmpty()) {
            $this->info('Tidak ada bahan yang perlu restok.');
            return 0;
        }

        // ambil ringkasan stok mingguan
        $ringkasan = collect($stockService->getRingkasanStokMingguan());

        // ✅ map rekomendasi: [bahan_id => rekomendasi] 
        $rekomendasiMap = $ringkasan->mapWithKeys(function ($row) {
            return [
                $row['bahan']->id => $row['rekomendasi'],
            ];
        });
        
        $message  = "*[Peringatan Stok Bahan]*\n\n";
        $message .= "Bahan yang perlu segera di-restok:\n\n";

        foreach ($bahans as $index => $bahan) {
            $no = $index + 1;

            $rekomendasi = $rekomendasiMap[$bahan->id] ?? 0;

            $message .= "{$no}. {$bahan->nama_bahan}\n";
            $message .= "   Stok saat ini   : {$bahan->stok} {$bahan->satuan}\n";
            $message .= "   Minimal stok    : {$bahan->minimal_stock} {$bahan->satuan}\n";

            if ($rekomendasi > 0) {
                $message .= "   Rekomendasi beli: {$rekomendasi} {$bahan->satuan}\n";
            }

            $message .= "\n";
        }

        $message .= "Silakan cek detail di dashboard manajemen stok bahan.";
        $adminWa = env('WA_ADMIN');

        if (!$adminWa) {
            $this->error('WA_ADMIN kosong / tidak terbaca dari .env');
            return 1;
        }

        try {
            $ok = $wa->sendMessage($adminWa, $message);

            if ($ok) {
                Bahan::whereIn('id', $bahans->pluck('id'))
                    ->update(['low_stock_notified' => true]);

                $this->info('Notifikasi WA terkirim.');
            } else {
                $this->error('Gagal mengirim notifikasi WA (sendMessage mengembalikan false).');
            }
        } catch (\Throwable $e) {
            Log::error('WA notify-low error: ' . $e->getMessage());
            $this->error('Exception saat kirim WA: ' . $e->getMessage());
        }

        return 0;
    }
}
