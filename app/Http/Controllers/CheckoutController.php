<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function productForm(Product $product)
    {
        return view('checkout.product', compact('product'));
    }

    public function singleStart(Request $request, Product $product)
{
    $data = $request->validate([
        'length'      => 'required|numeric|min:0.1',
        'width'       => 'required|numeric|min:0.1',
        'quantity'    => 'required|integer|min:1',
        'finishing'   => 'required|string',
        'note'        => 'nullable|string',

        // ✅ WAJIB UPLOAD + validasi format & ukuran
        'design_file' => 'required|file|mimes:jpg,jpeg,png,pdf,ai,cdr|max:10240',
    ]);

    $designPath = null;
    if ($request->hasFile('design_file')) {
        $designPath = $request->file('design_file')->store('designs', 'public');
    }

    session([
        'single_checkout' => [
            'product_id'  => $product->id,
            'length'      => $data['length'],
            'width'       => $data['width'],
            'quantity'    => $data['quantity'],
            'finishing'   => $data['finishing'],
            'note'        => $data['note'] ?? null,
            'design_file' => $designPath,
        ]
    ]);

    return redirect()->route('checkout.single');
}

    public function single()
    {
        $detail = session('single_checkout');
        if (!$detail) {
            return redirect()->route('home')->with('error', 'Data checkout tidak ditemukan.');
        }

        $product = Product::findOrFail($detail['product_id']);

        $area           = $detail['length'] * $detail['width'];
        $productTotal   = $area * $detail['quantity'] * $product->price;
        $finishingRate  = $detail['finishing'] !== 'tanpa' ? 500 : 0;
        $finishingTotal = $finishingRate * $area * $detail['quantity'];
        $subtotal       = $productTotal + $finishingTotal;

        return view('checkout.single', compact(
            'product',
            'detail',
            'area',
            'productTotal',
            'finishingRate',
            'finishingTotal',
            'subtotal'
        ));
    }

    public function singleStore(Request $request)
    {
        $detail = session('single_checkout');
        if (!$detail) {
            return redirect()->route('home')->with('error', 'Session checkout hilang.');
        }

        $data = $request->validate([
            'customer_name'   => 'required|string',
            'customer_email'  => 'required|email',
            'customer_phone'  => 'required|string',
            'address'         => 'required|string',
            'district'        => 'required|string',
            'city'            => 'required|string',
            'postal_code'     => 'required|string',

            'shipping_option' => 'required|in:ambil,kirim',
            'payment_option'  => 'required|string',

            'shipping_cost'    => 'nullable|integer|min:0',
            'shipping_etd'     => 'nullable|string',
            'shipping_courier' => Rule::requiredIf(fn () => $request->shipping_option === 'kirim'),
            'shipping_service' => Rule::requiredIf(fn () => $request->shipping_option === 'kirim'),
        ]);

        $product = Product::findOrFail($detail['product_id']);

        $area         = $detail['length'] * $detail['width'];
        $subtotal     = ($area * $detail['quantity'] * $product->price)
                      + ($detail['finishing'] !== 'tanpa' ? 500 * $area * $detail['quantity'] : 0);

        $shippingCost = $data['shipping_option'] === 'kirim'
            ? (int) ($data['shipping_cost'] ?? 0)
            : 0;

        $orderId = null;

        DB::transaction(function () use (&$orderId, $data, $detail, $product, $area, $subtotal, $shippingCost) {

            $order = Order::create([
                'user_id'          => auth()->id(),
                'customer_name'    => $data['customer_name'],
                'customer_email'   => $data['customer_email'],
                'customer_phone'   => $data['customer_phone'],
                'address'          => $data['address'],
                'district'         => $data['district'],
                'city'             => $data['city'],
                'postal_code'      => $data['postal_code'],
                'note'             => $detail['note'],

                'shipping_option'  => $data['shipping_option'],
                'shipping_courier' => $data['shipping_courier'] ?? null,
                'shipping_service' => $data['shipping_service'] ?? null,
                'shipping_etd'     => $data['shipping_etd'] ?? null,
                'shipping_cost'    => $shippingCost,

                'payment_option'   => $data['payment_option'],
                'total_payment'    => $subtotal,
                'grand_total'      => $subtotal + $shippingCost,
                'status'           => Order::STATUS_MENUNGGU,
            ]);

            OrderItem::create([
                'order_id'      => $order->id,
                'product_id'    => $product->id,
                'length'        => $detail['length'],
                'width'         => $detail['width'],
                'area'          => $area,
                'quantity'      => $detail['quantity'],
                'finishing'     => $detail['finishing'],
                'product_price' => $product->price,
                'line_total'    => $subtotal,
                'design_file'   => $detail['design_file'],
            ]);

            $orderId = $order->id;
        });

        session()->forget('single_checkout');

        return redirect()->route('checkout.pay', ['order' => $orderId]);
    }


    public function index(Request $request)
    {
        $selectedIds = $request->input('items', []); 

        $query = CartItem::with('product')
            ->where('user_id', auth()->id());

        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $subtotal = $items->sum(function ($item) {
            $area = $item->length * $item->width;
            return ($area * $item->quantity * $item->product->price)
                 + ($item->finishing !== 'tanpa' ? 500 * $area * $item->quantity : 0);
        });

        return view('checkout.index', compact('items', 'subtotal'));
    }

    public function store(Request $request)
    {
        $selectedIds = $request->input('items', []);

        $query = CartItem::with('product')
            ->where('user_id', auth()->id());

        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $data = $request->validate([
            'customer_name'   => 'required|string',
            'customer_email'  => 'required|email',
            'customer_phone'  => 'required|string',
            'address'         => 'required|string',
            'district'        => 'required|string',
            'city'            => 'required|string',
            'postal_code'     => 'required|string',

            'shipping_option' => 'required|in:ambil,kirim',
            'payment_option'  => 'required|string',

            'shipping_cost'    => 'nullable|integer|min:0',
            'shipping_etd'     => 'nullable|string',
            'shipping_courier' => Rule::requiredIf(fn () => $request->shipping_option === 'kirim'),
            'shipping_service' => Rule::requiredIf(fn () => $request->shipping_option === 'kirim'),
        ]);

        $subtotal = $items->sum(function ($item) {
            $area = $item->length * $item->width;

            $finishing = ($item->finishing !== 'tanpa')
                ? (500 * $area * $item->quantity)
                : 0;

            return ($area * $item->quantity * $item->product->price) + $finishing;
        });

        $shippingCost = $data['shipping_option'] === 'kirim'
            ? (int) ($data['shipping_cost'] ?? 0)
            : 0;

        $orderId = null;

        DB::transaction(function () use (&$orderId, $data, $items, $subtotal, $shippingCost) {

            $order = Order::create([
                'user_id'          => auth()->id(),
                'customer_name'    => $data['customer_name'],
                'customer_email'   => $data['customer_email'],
                'customer_phone'   => $data['customer_phone'],
                'address'          => $data['address'],
                'district'         => $data['district'],
                'city'             => $data['city'],
                'postal_code'      => $data['postal_code'],

                'shipping_option'  => $data['shipping_option'],
                'shipping_courier' => $data['shipping_courier'] ?? null,
                'shipping_service' => $data['shipping_service'] ?? null,
                'shipping_etd'     => $data['shipping_etd'] ?? null,
                'shipping_cost'    => $shippingCost,

                'payment_option'   => $data['payment_option'],
                'total_payment'    => $subtotal,
                'grand_total'      => $subtotal + $shippingCost,
                'status'           => Order::STATUS_MENUNGGU,
            ]);

            foreach ($items as $item) {
                $area = $item->length * $item->width;

                $finishing = ($item->finishing !== 'tanpa')
                    ? (500 * $area * $item->quantity)
                    : 0;

                $lineTotal = ($area * $item->quantity * $item->product->price) + $finishing;

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item->product_id,
                    'length'        => $item->length,
                    'width'         => $item->width,
                    'area'          => $area,
                    'quantity'      => $item->quantity,
                    'finishing'     => $item->finishing,
                    'product_price' => $item->product->price,
                    'line_total'    => $lineTotal,
                    'design_file'   => $item->design_file ?? null,
                ]);
            }

            CartItem::whereIn('id', $items->pluck('id'))->delete();

            $orderId = $order->id;
        });

        return redirect()->route('checkout.pay', ['order' => $orderId]);
    }

    public function pay($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        return view('checkout.pay', compact('order'));
    }
}
