<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use App\Models\Traits\BelongsToSeller;

class SellerPage extends Model
{
    use HasFactory, SoftDeletes, UsesTenantConnection, BelongsToSeller;

    public const CACHE_KEY_PREFIX = 'seller_page_';

    protected $fillable = [
        'seller_id',
        'theme_config',
        'blocks',
        'is_published',
    ];

    protected $casts = [
        'theme_config' => 'array',
        'blocks' => 'array',
        'is_published' => 'boolean',
    ];

    // seller() relationship is provided by BelongsToSeller trait

    public static function cacheKeyFor(string $subdomain): string
    {
        return self::CACHE_KEY_PREFIX.$subdomain;
    }
}
