<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
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

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->whereIn('status', ['active', 'published']);
    }

    public function scopeWithActiveProductsCount($query)
    {
        return $query->withCount(['products' => fn ($q) => $q->active()]);
    }

    public function scopeParentsOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Recursively retrieve all descendant category IDs including self.
     */
    public function getAllChildrenIds(): array
    {
        $ids = [$this->id];

        $children = $this->relationLoaded('children') ? $this->children : $this->children()->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }

        return array_unique($ids);
    }
}
