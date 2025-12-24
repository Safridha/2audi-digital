<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function byCategory(Category $category)
    {
        $products = $category->products()
            ->latest()
            ->get();

        return view('catalog.products', compact('category', 'products'));
    }

    public function search(Request $request)
    {
        $keyword = trim($request->input('q', ''));

        if ($keyword === '') {
            return redirect()->route('home')
                ->with('error', 'Masukkan kata kunci pencarian.');
        }

        $products = Product::where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('description', 'LIKE', "%{$keyword}%")
            ->get();

        return view('catalog.search', [
            'keyword'  => $keyword,
            'products' => $products,
        ]);
    }
}
