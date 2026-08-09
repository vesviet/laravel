<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'used_count' => 'integer',
        'usage_limit' => 'integer',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if the coupon is currently applicable for the given subtotal.
     *
     * @param  float  $subtotal  Cart eligible subtotal.
     */
    public function isApplicable(float $subtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($subtotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for the given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        return match ($this->type) {
            'percentage' => $subtotal * ((float) $this->value / 100),
            'fixed' => min((float) $this->value, $subtotal),
            default => 0.0,
        };
    }

    /**
     * Increment usage count — call within the order creation transaction.
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
