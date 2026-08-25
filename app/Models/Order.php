<?php

namespace App\Models;

use App\Enums\OrderStatus;
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
        'status'          => OrderStatus::class,
        'subtotal'        => 'integer',
        'discount_amount' => 'integer',
        'shipping_fee'    => 'integer',
        'total_amount'    => 'integer',
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

    /**
     * Formatted subtotal in VND.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 0, ',', '.') . '₫';
    }

    /**
     * Formatted discount amount in VND.
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return number_format($this->discount_amount, 0, ',', '.') . '₫';
    }

    /**
     * Formatted shipping fee in VND.
     */
    public function getFormattedShippingFeeAttribute(): string
    {
        return number_format($this->shipping_fee, 0, ',', '.') . '₫';
    }

    /**
     * Formatted total order amount in VND.
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', '.') . '₫';
    }

    /**
     * Human-readable status label in Vietnamese.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status instanceof OrderStatus) {
            return $this->status->label();
        }

        return OrderStatus::tryFrom((string) $this->status)?->label() ?? (string) $this->status;
    }

    /**
     * Tailwind CSS badge background & text class for storefront display.
     */
    public function getStatusBadgeClassesAttribute(): string
    {
        $status = $this->status instanceof OrderStatus ? $this->status : OrderStatus::tryFrom((string) $this->status);

        return match ($status) {
            OrderStatus::Pending    => 'bg-amber-50 text-amber-700 border border-amber-200',
            OrderStatus::Confirmed  => 'bg-blue-50 text-blue-700 border border-blue-200',
            OrderStatus::Processing => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
            OrderStatus::Shipped    => 'bg-purple-50 text-purple-700 border border-purple-200',
            OrderStatus::Delivered  => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            OrderStatus::Cancelled  => 'bg-rose-50 text-rose-700 border border-rose-200',
            default                 => 'bg-gray-50 text-gray-700 border border-gray-200',
        };
    }

    /**
     * Determine if order can be cancelled by customer.
     */
    public function getIsCancellableAttribute(): bool
    {
        $status = $this->status instanceof OrderStatus ? $this->status : OrderStatus::tryFrom((string) $this->status);

        return in_array($status, [OrderStatus::Pending, OrderStatus::Confirmed]);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 0, ',', '.') . '₫';
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return number_format($this->discount_amount, 0, ',', '.') . '₫';
    }

    public function getFormattedShippingFeeAttribute(): string
    {
        return number_format($this->shipping_fee, 0, ',', '.') . '₫';
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', '.') . '₫';
    }

    /**
     * Total count of physical items in order.
     */
    public function getItemCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /**
     * Scope: orders for a specific customer.
     */
    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope: recent orders.
     */
    public function scopeRecent($query)
    {
        return $query->latest();
    }
}
