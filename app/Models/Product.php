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

    /**
     * Blog posts referencing this product for contextual commerce.
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_product')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Scope: Filter by category slug (or collection of category IDs).
     */
    public function scopeFilterByCategory($query, $categorySlug)
    {
        if (empty($categorySlug)) {
            return $query;
        }

        if (is_array($categorySlug)) {
            return $query->whereIn('category_id', $categorySlug);
        }

        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    /**
     * Scope: Search by keyword in name, sku, and description.
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        $term = trim($keyword);

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope: Filter by price range.
     */
    public function scopePriceRange($query, ?float $minPrice, ?float $maxPrice)
    {
        if (!is_null($minPrice) && $minPrice > 0) {
            $query->where('price', '>=', $minPrice);
        }

        if (!is_null($maxPrice) && $maxPrice > 0) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    /**
     * Scope: Filter only in-stock items.
     */
    public function scopeInStock($query, bool $onlyInStock = true)
    {
        if ($onlyInStock) {
            return $query->where('stock', '>', 0);
        }

        return $query;
    }

    /**
     * Scope: Sort by predefined sorting criteria.
     */
    public function scopeSortedBy($query, ?string $sort)
    {
        return match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            'name_desc'  => $query->orderBy('name', 'desc'),
            'featured'   => $query->orderBy('is_featured', 'desc')->latest(),
            default      => $query->latest(),
        };
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'published']);
    }

    /**
     * Scope: featured AND active products for homepage display.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->whereIn('status', ['active', 'published']);
    }

    /**
     * Resolve any image path or URL safely.
     */
    public static function resolveImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }

    /**
     * Primary image URL accessor.
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        if (!empty($this->image_path)) {
            return static::resolveImageUrl($this->image_path);
        }

        if (!empty($this->attributes_json['primary_image'])) {
            return static::resolveImageUrl($this->attributes_json['primary_image']);
        }

        return null;
    }

    /**
     * Secondary / hover image URL accessor.
     */
    public function getSecondaryImageUrlAttribute(): ?string
    {
        $secondary = $this->secondary_image_path
            ?? $this->secondary_image
            ?? $this->hover_image
            ?? ($this->attributes_json['secondary_image'] ?? null);

        if (!empty($secondary)) {
            return static::resolveImageUrl($secondary);
        }

        return null;
    }

    /**
     * Complete gallery images list accessor.
     */
    public function getGalleryImagesAttribute(): array
    {
        $images = [];

        if ($primary = $this->primary_image_url) {
            $images[] = $primary;
        }

        if ($secondary = $this->secondary_image_url) {
            if (!in_array($secondary, $images)) {
                $images[] = $secondary;
            }
        }

        if (!empty($this->attributes_json['gallery']) && is_array($this->attributes_json['gallery'])) {
            foreach ($this->attributes_json['gallery'] as $item) {
                if ($url = static::resolveImageUrl($item)) {
                    if (!in_array($url, $images)) {
                        $images[] = $url;
                    }
                }
            }
        }

        return $images;
    }

    /**
     * Lifestyle lookbook / gallery album items.
     * Returns an array of structured items with 'url', 'title', 'tag', 'caption'
     */
    public function getAlbumImagesAttribute(): array
    {
        if (!empty($this->attributes_json['album']) && is_array($this->attributes_json['album'])) {
            $album = [];
            foreach ($this->attributes_json['album'] as $item) {
                if (is_string($item)) {
                    if ($url = static::resolveImageUrl($item)) {
                        $album[] = [
                            'url'     => $url,
                            'title'   => $this->name,
                            'tag'     => 'Không Gian Sống',
                            'caption' => 'Phong cách bài trí nội thất Scandinavian hiện đại',
                        ];
                    }
                } elseif (is_array($item) && !empty($item['url'])) {
                    $item['url'] = static::resolveImageUrl($item['url']);
                    $album[] = $item;
                }
            }
            if (!empty($album)) {
                return $album;
            }
        }

        // Fallback structured lookbook album from gallery images
        $gallery = $this->gallery_images;
        $album = [];
        $tags = ['Góc Studio Sáng Tạo', 'Bàn Cạnh Giường & Đôn Trưng Bày', 'Không Gian Phòng Khách', 'Nghệ Thuật Xếp Chồng Điêu Khắc'];
        foreach ($gallery as $index => $imgUrl) {
            $album[] = [
                'url'     => $imgUrl,
                'title'   => $this->name . ' — Góc Nhìn ' . ($index + 1),
                'tag'     => $tags[$index % count($tags)],
                'caption' => 'Thiết kế tối giản kết hợp hoàn hảo trong không gian sống hiện đại.',
            ];
        }

        return $album;
    }

    /**
     * Thumbnail accessor — maps ->thumbnail to primary_image_url.
     * CartService and views reference ->thumbnail.
     */
    public function getThumbnailAttribute(): ?string
    {
        return $this->primary_image_url;
    }

    /**
     * Formatted price in Vietnamese Dong (VND).
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . '₫';
    }

    /**
     * Check if product is in stock.
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Stock status text label.
     */
    public function getStockStatusLabelAttribute(): string
    {
        return $this->stock > 0 ? "Còn hàng ({$this->stock})" : 'Hết hàng';
    }

    /**
     * Stock status Tailwind badge color class.
     */
    public function getStockStatusColorAttribute(): string
    {
        return $this->stock > 0 ? 'text-emerald-600' : 'text-[#E84444]';
    }

    /**
     * Get related products from the same category.
     */
    public function getRelatedProducts(int $limit = 4)
    {
        return static::active()
            ->where('id', '!=', $this->id)
            ->when($this->category_id, fn ($q) => $q->where('category_id', $this->category_id))
            ->with(['category'])
            ->take($limit)
            ->get();
    }

    /**
     * Export standard Schema.org Product JSON-LD array.
     */
    public function toSchemaOrgJsonLd(string $url): array
    {
        $gallery = $this->gallery_images;
        if (empty($gallery) && $this->primary_image_url) {
            $gallery = [$this->primary_image_url];
        }

        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $this->name,
            'description' => $this->seo_description ?? strip_tags($this->description ?? $this->name),
            'sku' => $this->sku ?? "PRD-{$this->id}",
            'offers' => [
                '@type' => 'Offer',
                'url' => $url,
                'priceCurrency' => 'VND',
                'price' => (string) $this->price,
                'availability' => 'https://schema.org/' . ($this->stock > 0 ? 'InStock' : 'OutOfStock'),
                'itemCondition' => 'https://schema.org/NewCondition',
            ],
        ];

        if (!empty($gallery)) {
            $schema['image'] = $gallery;
        }

        if ($this->category) {
            $schema['category'] = $this->category->name;
        }

        return $schema;
    }
}
