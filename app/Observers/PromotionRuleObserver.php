<?php

namespace App\Observers;

use App\Models\PromotionRule;
use Illuminate\Support\Facades\Cache;

class PromotionRuleObserver
{
    /**
     * Cache key storing pre-computed active catalog promotion rules.
     */
    public const CATALOG_RULES_CACHE_KEY = 'promotions:active_catalog_rules';

    /**
     * Flush active catalog rules cache on create or update.
     */
    public function saved(PromotionRule $promotionRule): void
    {
        $this->clearCatalogCache();
    }

    /**
     * Flush active catalog rules cache on deletion.
     */
    public function deleted(PromotionRule $promotionRule): void
    {
        $this->clearCatalogCache();
    }

    /**
     * Flush active catalog rules cache on restore.
     */
    public function restored(PromotionRule $promotionRule): void
    {
        $this->clearCatalogCache();
    }

    /**
     * Flush active catalog rules cache on force deletion.
     */
    public function forceDeleted(PromotionRule $promotionRule): void
    {
        $this->clearCatalogCache();
    }

    /**
     * Universal cache invalidator across all cache drivers.
     */
    protected function clearCatalogCache(): void
    {
        Cache::forget(self::CATALOG_RULES_CACHE_KEY);
    }
}
