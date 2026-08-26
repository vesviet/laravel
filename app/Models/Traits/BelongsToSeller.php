<?php

namespace App\Models\Traits;

use App\Models\Scopes\TenantSellerScope;
use App\Models\SellerProfile;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToSeller
{
    /**
     * The "booted" method of the trait.
     */
    protected static function bootBelongsToSeller(): void
    {
        static::addGlobalScope(new TenantSellerScope);
    }

    /**
     * Get the seller that owns the model.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }
}
