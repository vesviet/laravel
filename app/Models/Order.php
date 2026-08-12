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
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'shipping_fee' => 'integer',
        'total_amount' => 'integer',
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
