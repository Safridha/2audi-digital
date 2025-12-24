<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $query = Category::query()->orderBy('name');

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $categories = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.categories._table', compact('categories'))->render();
        }

        return view('admin.categories.index', compact('categories', 'q'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'description' => 'nullable|string',
        ]);

        // Upload ke storage/app/public/categories
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('category_ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Tidak ada kategori yang dipilih.');
        }

        $categories = Category::whereIn('id', $ids)->get();

        foreach ($categories as $category) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $category->delete();
        }

        return back()->with('success', 'Kategori terpilih berhasil dihapus.');
    }
}
