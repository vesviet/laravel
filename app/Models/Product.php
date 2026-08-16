<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'image_path',
        'price',
        'stock',
        'weight',
        'status',
        'is_featured',
        'attributes_json',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'attributes_json' => 'array',
        'price'           => 'integer',
        'stock'           => 'integer',
        'weight'          => 'integer',
        'is_featured'     => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function scopeFilterByCategory($query, $categorySlug)
    {
        if ($categorySlug) {
            return $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: featured AND active products for homepage display.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('status', 'active');
    }

    /**
     * Thumbnail accessor — maps ->thumbnail to image_path column.
     * CartService and views reference ->thumbnail; the DB column is image_path.
     */
    public function getThumbnailAttribute(): ?string
    {
        return $this->image_path;
    }
}
