<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasTranslations;

    public $translatable = [
        'name',
        'slug',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'slug',
        'price',
        'original_price',
        'stock',
        'image_path',
        'gallery',
        'short_description',
        'description',
        'features',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'original_price' => 'decimal:0',
        'stock' => 'integer',
        'gallery' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_image')
            ->singleFile();

        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->format('webp')
            ->quality(80);

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(800)
            ->format('webp')
            ->quality(85);

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(1200)
            ->format('webp')
            ->quality(90);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (!$this->original_price || $this->original_price <= $this->price) {
            return null;
        }

        return (int) round(
            (($this->original_price - $this->price) / $this->original_price) * 100
        );
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . '₫';
    }

    public function getFormattedOriginalPriceAttribute(): ?string
    {
        if (!$this->original_price) {
            return null;
        }

        return number_format($this->original_price, 0, ',', '.') . '₫';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
