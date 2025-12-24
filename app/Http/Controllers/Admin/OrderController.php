<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\StockService;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Order::with(['items.product', 'user'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function ($qp) use ($search) {
                      $qp->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('admin.orders._table', compact('orders'))->render();
        }
        return view('admin.orders.index', compact('orders', 'search'));
    }

    public function edit(Order $order)
    {
        $order->load('items.product', 'user');

        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Kalau status berubah ke "diproses" → stok bahan otomatis berkurang,
     * Jika stok kurang, status tetap bisa berubah.
     */
    public function update(Request $request, Order $order, StockService $stockService)
    {
        $data = $request->validate([
            'status' => 'required|string|in:menunggu_pembayaran,diproses,selesai,diantar_diambil,dibatalkan',
        ]);

        $oldStatus = $order->status;
        $newStatus = $data['status'];

        // Jika mau pindah ke "diproses" dan sebelumnya belum diproses, panggil applyUsageFromOrder.
        // - stok kurang: tidak throw (di StockService kamu sudah skip + WA)
        // - error lain: ditangkap, tapi tidak memblok update status
        if ($oldStatus !== 'diproses' && $newStatus === 'diproses') {
            try {
                $order->loadMissing('items.product.bahans');

                $stockService->applyUsageFromOrder($order);
            } catch (\Throwable $e) { 
                // sengaja diabaikan supaya status tetap bisa berubah
            }
        }

        $order->status = $newStatus;
        $order->save();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(Order $order)
    {
        $order->load('items');

        foreach ($order->items as $item) {
            if ($item->design_file) {
                Storage::disk('public')->delete($item->design_file);
            }
        }

        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('order_ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Tidak ada pesanan yang dipilih.');
        }

        $orders = Order::with('items')->whereIn('id', $ids)->get();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if ($item->design_file) {
                    Storage::disk('public')->delete($item->design_file);
                }
            }

            $order->delete();
        }

        return back()->with('success', 'Pesanan terpilih berhasil dihapus.');
    }
}
