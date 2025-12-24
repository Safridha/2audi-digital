<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Services\StockService;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    public function pay(Order $order)
    {
        if (! $order->snap_token) {
            // bikin order_id unik buat Midtrans
            $midtransOrderId = 'ORDER-' . $order->id . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id'     => $midtransOrderId,
                    'gross_amount' => $order->grand_total,
                ],
                'customer_details'    => [
                    'first_name' => $order->customer_name,
                    'email'      => $order->customer_email,
                    'phone'      => $order->customer_phone,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $order->snap_token     = $snapToken;
            $order->payment_method = 'midtrans'; 
            $order->save();
        } else {
            $snapToken = $order->snap_token;
        }

        // ambil item2 order + produk
        $order->load('items.product');

        $printingTotal  = $order->items->sum('printing_cost');
        $finishingTotal = $order->items->sum('finishing_cost');
        $itemsSubtotal  = $order->items->sum('line_total');

        if ($order->total_payment == 0) {
            $order->total_payment = $itemsSubtotal;
        }

        return view('payment.waiting', compact(
            'order',
            'snapToken',
            'printingTotal',
            'finishingTotal',
            'itemsSubtotal'
        ));
    }

    // Webhook Midtrans callback Midtrans harus idempotent dan atomic, 
    // supaya status dan stok konsisten dalam satu konteks transaksi
    public function callback(Request $request, StockService $stockService)
    {
        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $orderId           = $notification->order_id;           
        $transactionStatus = $notification->transaction_status; 
        $fraudStatus       = $notification->fraud_status;     
        $paymentType       = $notification->payment_type ?? null;
        $internalId = $this->extractInternalOrderId($orderId);
        if (! $internalId) {
            return response()->json(['message' => 'Invalid order id format'], 400);
        }

        $order = Order::find($internalId);
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $oldStatus = $order->status;

        // simpan jenis payment Midtrans kalau ada
        if ($paymentType) {
            $order->payment_method = $paymentType; 
        }

        $isFinalOrHandled = in_array($order->status, [
            Order::STATUS_DIPROSES,
            Order::STATUS_SELESAI,
            Order::STATUS_DIBATALKAN,
        ], true);

        if (
            in_array($transactionStatus, ['capture', 'settlement']) &&
            $fraudStatus === 'accept'
        ) {
            $order->status = Order::STATUS_DIPROSES;
            $order->save();
            // potong stok hanya sekali (saat pertama kali sukses)
            if ($oldStatus !== Order::STATUS_DIPROSES) {
                $stockService->applyUsageFromOrder($order);
            }

        } elseif ($transactionStatus === 'pending') { // pending: jangan override status admin


        } elseif (in_array($transactionStatus, ['cancel', 'expire', 'deny'])) {

            if (! $isFinalOrHandled && $order->status === Order::STATUS_MENUNGGU) {
                $order->status = Order::STATUS_DIBATALKAN;
                $order->save();
            }
        }

        return response()->json(['message' => 'OK']);
    }

    public function finish(Request $request)
    {
        $midtransOrderId = $request->input('order_id');   
        $internalId      = $this->extractInternalOrderId($midtransOrderId);

        if (! $internalId) {
            return redirect()->route('home')->with('error', 'Order tidak valid.');
        }

        return redirect()->route('checkout.pay', $internalId);
    }

    public function unfinish(Request $request)
    {
        $midtransOrderId = $request->input('order_id');
        $internalId      = $this->extractInternalOrderId($midtransOrderId);

        if (! $internalId) {
            return redirect()->route('home')->with('error', 'Order tidak valid.');
        }

        return redirect()->route('checkout.pay', $internalId);
    }

    public function error(Request $request)
    {
        $midtransOrderId = $request->input('order_id');
        $internalId      = $this->extractInternalOrderId($midtransOrderId);

        if (! $internalId) {
            return redirect()->route('home')->with('error', 'Order tidak valid.');
        }

        return redirect()->route('checkout.pay', $internalId);
    }

    // Ambil ID internal dari order_id Midtrans
    private function extractInternalOrderId(?string $midtransOrderId): ?int
    {
        if (! $midtransOrderId) {
            return null;
        }

        if (preg_match('/^ORDER-(\d+)(-.+)?$/', $midtransOrderId, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
