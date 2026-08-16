<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Invalidate homepage caches when a product is created, updated, or deleted.
     * Covers: is_featured toggle, status change, price/name updates.
     */
    public function created(Product $product): void
    {
        $this->clearHomepageCache();
    }

    public function updated(Product $product): void
    {
        $this->clearHomepageCache();
    }

    public function deleted(Product $product): void
    {
        $this->clearHomepageCache();
    }

    private function clearHomepageCache(): void
    {
        Cache::forget('homepage.featured');
        Cache::forget('homepage.new_arrivals');
        Cache::forget('homepage.categories');
    }
}
