<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        // [I-09] stripe_customer_id is NOT included — Stripe package not yet integrated.
        // Add back only when Stripe integration is approved via ADR and migration adds the column.
        // 'stripe_customer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Total lifetime spent on successful / active orders.
     */
    public function getTotalSpentAttribute(): int
    {
        return (int) $this->orders()
            ->whereIn('status', [
                \App\Enums\OrderStatus::Confirmed,
                \App\Enums\OrderStatus::Processing,
                \App\Enums\OrderStatus::Shipped,
                \App\Enums\OrderStatus::Delivered,
            ])
            ->sum('total_amount');
    }

    /**
     * Formatted total lifetime spend in VND.
     */
    public function getFormattedTotalSpentAttribute(): string
    {
        return number_format($this->total_spent, 0, ',', '.') . '₫';
    }

    /**
     * Membership Tier calculation based on lifetime spend.
     */
    public function getMembershipTierAttribute(): string
    {
        $spent = $this->total_spent;

        if ($spent >= 20000000) {
            return 'VIP Diamond';
        }

        if ($spent >= 5000000) {
            return 'Thành Viên Thân Thiết';
        }

        return 'Thành Viên Mới';
    }

    /**
     * Tailwind CSS badge styling for Membership Tier.
     */
    public function getMembershipTierBadgeClassesAttribute(): string
    {
        $tier = $this->membership_tier;

        return match ($tier) {
            'VIP Diamond'              => 'bg-[#23232C] text-white border border-[#23232C]',
            'Thành Viên Thân Thiết'   => 'bg-amber-100 text-amber-900 border border-amber-300',
            default                    => 'bg-[#F0F0F0] text-[#23232C] border border-[#E5E5E5]',
        };
    }

    /**
     * Resolve latest known shipping address from the customer's previous orders.
     */
    public function getLastShippingAddressAttribute(): ?array
    {
        $latestOrder = $this->orders()->latest()->first();

        if (!$latestOrder) {
            return null;
        }

        return [
            'customer_name' => $latestOrder->customer_name ?? $this->name,
            'phone'         => $latestOrder->phone ?? $this->phone,
            'email'         => $latestOrder->email ?? $this->email,
            'address'       => $latestOrder->address,
            'city'          => $latestOrder->city,
            'district'      => $latestOrder->district,
            'ward'          => $latestOrder->ward,
        ];
    }

    /**
     * Get recent orders with eager loaded items.
     */
    public function getRecentOrders(int $limit = 5)
    {
        return $this->orders()
            ->with(['items.product'])
            ->latest()
            ->take($limit)
            ->get();
    }
}
