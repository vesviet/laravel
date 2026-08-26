<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Traits\UsesTenantConnection;

class SellerPage extends Model
{
    use HasFactory, SoftDeletes, UsesTenantConnection;

    protected $guarded = ['id'];

    protected $casts = [
        'theme_config' => 'array',
        'blocks' => 'array',
        'is_published' => 'boolean',
    ];

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }
}
