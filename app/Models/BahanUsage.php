<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BahanUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_id',
        'tanggal',
        'qty',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
}
