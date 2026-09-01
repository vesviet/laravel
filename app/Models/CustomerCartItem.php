<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerCartItem extends Model
{
    /**
     * product_variant_id = 0 means "no variant" (sentinel value).
     *
     * We use 0 instead of NULL because MySQL treats NULL != NULL in UNIQUE constraints,
     * which causes upsert() to INSERT duplicates instead of UPDATE when product_variant_id is NULL.
     *
     * Pattern: store 0, expose ?int via getVariantIdOrNull().
     */
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'product_id',
        'product_variant_id', // 0 = no variant
        'quantity',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Returns the actual variant ID as int, or null if sentinel 0. */
    public function getVariantIdOrNull(): ?int
    {
        return $this->product_variant_id === 0 ? null : (int) $this->product_variant_id;
    }
}
