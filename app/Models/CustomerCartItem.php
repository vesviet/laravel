<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerCartItem extends Model
{
    /**
     * No timestamps() macro — only updated_at is used (for idle-detection).
     * updated_at is managed automatically via DB DEFAULT + ON UPDATE CURRENT_TIMESTAMP.
     */
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'product_id',
        'product_variant_id',
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

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
