<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Bahan;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $query = Product::with(['category', 'bahans'])->orderBy('id', 'desc');

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('category', function ($cat) use ($q) {
                        $cat->where('name', 'like', "%{$q}%");
                    });
            });
        }

        $products = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.products._table', compact('products'))->render();
        }

        return view('admin.products.index', compact('products', 'q'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $bahans     = Bahan::orderBy('nama_bahan')->get();

        return view('admin.products.create', compact('categories', 'bahans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        $sync = [];
        foreach ($request->bahans ?? [] as $bahanId => $row) {
            if (!empty($row['enabled']) && $row['qty_per_unit'] > 0) {
                $sync[$bahanId] = [
                    'qty_per_unit' => $row['qty_per_unit'],
                ];
            }
        }
        $product->bahans()->sync($sync);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $product->loadMissing('bahans');

        $categories     = Category::orderBy('name')->get();
        $bahans         = Bahan::orderBy('nama_bahan')->get();
        $selectedBahans = $product->bahans->pluck('pivot.qty_per_unit', 'id');

        return view('admin.products.edit', compact(
            'product', 'categories', 'bahans', 'selectedBahans'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        $sync = [];
        foreach ($request->bahans ?? [] as $bahanId => $row) {
            if (!empty($row['enabled']) && $row['qty_per_unit'] > 0) {
                $sync[$bahanId] = [
                    'qty_per_unit' => $row['qty_per_unit'],
                ];
            }
        }
        $product->bahans()->sync($sync);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->bahans()->detach();
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('product_ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Tidak ada produk yang dipilih.');
        }

        $products = Product::whereIn('id', $ids)->get();

        foreach ($products as $product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->bahans()->detach();
            $product->delete();
        }

        return back()->with('success', 'Produk terpilih berhasil dihapus!');
    }
}
