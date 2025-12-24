<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    public function index()
    {
        $items = CartItem::with('product')
            ->where('user_id', auth()->id())
            ->get();

        // Tambahan: total dihitung dari accessor CartItem (biar Blade tidak hitung manual)
        $grandProductTotal   = $items->sum(fn ($i) => $i->product_total);
        $grandFinishingTotal = $items->sum(fn ($i) => $i->finishing_total);
        $grandTotal          = $items->sum(fn ($i) => $i->line_total);

        return view('cart.index', compact(
            'items',
            'grandProductTotal',
            'grandFinishingTotal',
            'grandTotal'
        ));
    }

    public function create(Product $product)
    {
        return view('cart.add', compact('product'));
    }

    /**
     * PROSES TAMBAH KE KERANJANG
     * - action = cart → redirect ke /cart
     * - action = buy_now → redirect ke /checkout
     */
    public function add(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity'    => 'required|integer|min:1',
            'length'      => 'required|numeric|min:0.01',
            'width'       => 'required|numeric|min:0.01',
            'finishing'   => 'required|string',
            'note'        => 'nullable|string',
            'design_file' => 'nullable|file|max:10240',
        ]);

        $length    = $data['length'];
        $width     = $data['width'];
        $area      = $length * $width;
        $finishing = $data['finishing'];

        $designPath = null;
        if ($request->hasFile('design_file')) {
            $designPath = $request->file('design_file')->store('designs', 'public');
        }

        CartItem::create([
            'user_id'     => auth()->id(),
            'product_id'  => $product->id,
            'quantity'    => $data['quantity'],
            'length'      => $length,
            'width'       => $width,
            'area'        => $area,
            'finishing'   => $finishing,
            'note'        => $data['note'] ?? null,
            'design_file' => $designPath,
        ]);

        // Tentukan redirect berdasarkan tombol yang ditekan
        $action = $request->input('action', 'cart');

        if ($action === 'buy_now') {
            return redirect()->route('checkout.index');
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Produk berhasil dimasukkan ke keranjang.');
    }

    public function edit($item)
    {
        $cartItem = CartItem::with('product')->findOrFail($item);

        if ($cartItem->user_id !== auth()->id()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit item ini.');
        }

        return view('cart.edit', compact('cartItem'));
    }

    public function update(Request $request, $item)
    {
        $cartItem = CartItem::with('product')->findOrFail($item);

        if ($cartItem->user_id !== auth()->id()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit item ini.');
        }

        $data = $request->validate([
            'quantity'    => 'required|integer|min:1',
            'length'      => 'required|numeric|min:0.01',
            'width'       => 'required|numeric|min:0.01',
            'finishing'   => 'required|string',
            'note'        => 'nullable|string',
            'design_file' => 'nullable|file|max:10240',
        ]);

        $area = $data['length'] * $data['width'];

        if ($request->hasFile('design_file')) {
            if ($cartItem->design_file) {
                Storage::disk('public')->delete($cartItem->design_file);
            }
            $cartItem->design_file = $request->file('design_file')->store('designs', 'public');
        }

        $cartItem->quantity  = $data['quantity'];
        $cartItem->length    = $data['length'];
        $cartItem->width     = $data['width'];
        $cartItem->area      = $area;
        $cartItem->finishing = $data['finishing'];
        $cartItem->note      = $data['note'] ?? null;

        $cartItem->save();

        return redirect()
            ->route('cart.index')
            ->with('success', 'Item keranjang berhasil diperbarui.');
    }

    public function remove($item)
    {
        $cartItem = CartItem::findOrFail($item);

        if ($cartItem->user_id !== auth()->id()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus item ini.');
        }

        // hapus file desain dari storage (jika ada) sebelum hapus item
        if ($cartItem->design_file) {
            Storage::disk('public')->delete($cartItem->design_file);
        }

        $cartItem->delete();

        return redirect()
            ->route('cart.index')
            ->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
