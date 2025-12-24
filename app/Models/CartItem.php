<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'length',
        'width',
        'area',
        'finishing',
        'note',
        'design_file',
    ];

    // Tambahan: supaya hasil perhitungan bisa langsung dipakai di Blade
    protected $appends = [
        'harga_per_m2',
        'luas',
        'finishing_rate',
        'product_total',
        'finishing_total',
        'line_total',
        'is_design_image',
        'design_ext',
    ];

    protected $casts = [
        'quantity' => 'int',
        'length'   => 'float',
        'width'    => 'float',
        'area'     => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getHargaPerM2Attribute(): float
    {
        return (float) ($this->product->price ?? 0);
    }

    public function getLuasAttribute(): float
    {
        // Prioritaskan kolom 'area' kalau tersedia, fallback hitung manual
        if (!is_null($this->area) && (float) $this->area > 0) {
            return (float) $this->area;
        }

        return (float) $this->length * (float) $this->width;
    }

    public function getFinishingRateAttribute(): float
    {
        return strtolower((string) $this->finishing) === 'finishing' ? 500.0 : 0.0;
    }

    public function getProductTotalAttribute(): float
    {
        return $this->luas * (int) $this->quantity * $this->harga_per_m2;
    }

    public function getFinishingTotalAttribute(): float
    {
        return $this->luas * (int) $this->quantity * $this->finishing_rate;
    }

    public function getLineTotalAttribute(): float
    {
        return $this->product_total + $this->finishing_total;
    }

    public function getDesignExtAttribute(): string
    {
        if (!$this->design_file) return '';
        return strtolower(pathinfo($this->design_file, PATHINFO_EXTENSION));
    }

    public function getIsDesignImageAttribute(): bool
    {
        return in_array($this->design_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }
}
