<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all(); // ambil semua kategori
        return view('home', compact('categories'));
    }
}
