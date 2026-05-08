<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'parent_id',
        'description',
        'image_path',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    protected $casts = [
        'type' => 'string',
        'sort_order' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->format('webp')
            ->quality(80);

        $this->addMediaConversion('banner')
            ->width(1200)
            ->height(600)
            ->format('webp')
            ->quality(85);
    }

    public function scopeProductCategories($query)
    {
        return $query->where('type', 'product');
    }

    public function scopeArticleCategories($query)
    {
        return $query->where('type', 'article');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
