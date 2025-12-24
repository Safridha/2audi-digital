<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'length',
        'width',
        'area',
        'quantity',
        'finishing',
        'product_price',
        'finishing_rate',
        'printing_cost',
        'finishing_cost',
        'line_total',
        'design_file',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
