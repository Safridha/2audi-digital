<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahans';

    protected $fillable = [
        'nama_bahan',
        'satuan',
        'stok',
        'minimal_stock',
        'low_stock_notified',
    ];

    protected $casts = [
        'stok'               => 'float',
        'minimal_stock'      => 'integer',
        'low_stock_notified' => 'boolean',
    ];

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function usages()
    {
        return $this->hasMany(BahanUsage::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_bahan')
                    ->withPivot('qty_per_unit')
                    ->withTimestamps();
    }

    // Scope: bahan yang perlu restok, Dipakai oleh dashboard & notifikasi WA
    public function scopePerluRestok($query)
    {
        return $query->whereColumn('stok', '<=', 'minimal_stock');
    }

    // Hitung ulang stok dari batch FIFO, Sekaligus reset flag notifikasi kalau stok sudah aman

    public function recalcStokFromBatches(): void
    {
        $total = $this->stockBatches()->sum('qty_sisa');

        $this->stok = $total;
        if ($this->stok > $this->minimal_stock) {
            $this->low_stock_notified = false;
        }

        $this->save();
    }
}
