<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_number',
        'status',
        'waybill_id',
        'payment_method',
        'customer_name',
        'phone',
        'email',
        'address',
        'city',
        'district',
        'ward',
        'notes',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'total_amount',
        'utm_source',
        'landing_page_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }
}
