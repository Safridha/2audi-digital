<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address',
        'district',
        'city',
        'postal_code',
        'note',

        // pengiriman
        'shipping_option',     
        'shipping_courier',    
        'shipping_service',    
        'shipping_etd',       
        'shipping_cost',

        // pembayaran
        'payment_option',      
        'payment_method',     
        'snap_token',

        // totalan
        'total_payment',
        'grand_total',

        // status pesanan
        'status',
    ];

    const STATUS_MENUNGGU   = 'menunggu_pembayaran';
    const STATUS_DIBAYAR    = 'pembayaran_berhasil';
    const STATUS_DIPROSES   = 'diproses';
    const STATUS_SELESAI    = 'selesai';
    const STATUS_DIBATALKAN = 'dibatalkan';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
