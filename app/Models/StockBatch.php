<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_id',
        'tanggal_masuk',
        'qty_awal',
        'qty_sisa',
        'harga_satuan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
}
