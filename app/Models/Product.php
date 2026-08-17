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
}
