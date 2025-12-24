<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds        = config('midtrans.is_3ds');

        // Ambil data notif dari Midtrans
        $notification = new \Midtrans\Notification();
        $orderId           = $notification->order_id;           
        $transactionStatus = $notification->transaction_status; 
        $fraudStatus       = $notification->fraud_status;       

        // Ambil id internal kita dari ORDER
        $internalId = $this->extractInternalOrderId($orderId);
        if (! $internalId) {
            return response()->json(['message' => 'Invalid order id format'], 400);
        }

        $order = Order::find($internalId);
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $isFinalOrHandled = in_array($order->status, [
            Order::STATUS_DIPROSES,
            Order::STATUS_SELESAI,
            Order::STATUS_DIBATALKAN,
        ], true);

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                if ($fraudStatus !== 'challenge') {
                    $order->status = Order::STATUS_DIPROSES;
                    $order->save();
                }
                break;

            case 'pending':
                // kalau status masih menunggu, biarkan saja (tidak perlu save)
                if (! $isFinalOrHandled && $order->status === Order::STATUS_MENUNGGU) {
                }
                break;

            case 'deny':
            case 'expire':
            case 'cancel':
                if (! $isFinalOrHandled && $order->status === Order::STATUS_MENUNGGU) {
                    $order->status = Order::STATUS_DIBATALKAN;
                    $order->save();
                }
                break;
        }

        return response()->json(['message' => 'OK']);
    }

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
