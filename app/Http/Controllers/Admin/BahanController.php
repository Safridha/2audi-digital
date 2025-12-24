<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    public function index()
    {
        $bahans = Bahan::orderBy('nama_bahan')->paginate(10);

        return view('admin.bahans.index', compact('bahans'));
    }

    public function create()
    {
        return view('admin.bahans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan'    => 'required|string|max:255',
            'satuan'        => 'required|string|max:50',
            'stok'          => 'nullable|numeric|min:0',
            'minimal_stock' => 'nullable|integer|min:0',
        ]);

        Bahan::create([
            'nama_bahan'       => $request->nama_bahan,
            'satuan'           => $request->satuan,
            'stok'             => $request->stok ?? 0,
            'minimal_stock'    => $request->minimal_stock ?? 0,
            'low_stock_notified' => false,
        ]);

        return redirect()
            ->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function edit(Bahan $bahan)
    {
        return view('admin.bahans.edit', compact('bahan'));
    }

    public function update(Request $request, Bahan $bahan)
    {
        $request->validate([
            'nama_bahan'    => 'required|string|max:255',
            'satuan'        => 'required|string|max:50',
            'stok'          => 'nullable|numeric|min:0',
            'minimal_stock' => 'nullable|integer|min:0',
        ]);

        $stok          = $request->stok ?? $bahan->stok;
        $minimal_stock = $request->minimal_stock ?? 0;

        $dataUpdate = [
            'nama_bahan'    => $request->nama_bahan,
            'satuan'        => $request->satuan,
            'stok'          => $stok,
            'minimal_stock' => $minimal_stock,
        ];

        // kalau stok sudah di atas minimal → reset flag notifikasi
        if ($stok > $minimal_stock) {
            $dataUpdate['low_stock_notified'] = false;
        }

        $bahan->update($dataUpdate);

        return redirect()
            ->route('admin.bahans.index')
            ->with('success', 'Data bahan berhasil diperbarui.');
    }

    public function destroy(Bahan $bahan)
    {
        $bahan->delete();

        return redirect()
            ->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil dihapus.');
    }

    // HAPUS BANYAK BAHAN SEKALIGUS (BULK DELETE)
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return redirect()
                ->route('admin.bahans.index')
                ->with('error', 'Tidak ada bahan yang dipilih.');
        }

        Bahan::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.bahans.index')
            ->with('success', 'Bahan terpilih berhasil dihapus.');
    }
}
