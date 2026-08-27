<?php

namespace App\Actions;

use App\Exceptions\SellerActionException;
use App\Models\SellerPage;
use App\Models\SellerProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class PublishSellerPageAction
{
    /**
     * Publish or unpublish seller's page and clear relevant cache.
     * ADR-S2: this Action owns the only DB::transaction() boundary.
     *
     * @throws SellerActionException
     */
    public function execute(SellerProfile $seller, bool $publish = true): SellerPage
    {
        try {
            return DB::transaction(function () use ($seller, $publish) {
                $page = $seller->pages()->first();

                if (! $page) {
                    throw SellerActionException::pageNotInitialized();
                }

                $page->is_published = $publish;
                $page->save();

                return $page;
            });
        } catch (SellerActionException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw SellerActionException::pageUpdateFailed($e);
        } finally {
            // Cache invalidation runs even when transaction rolls back,
            // ensuring stale state is never served to the next request.
            Cache::forget(SellerPage::cacheKeyFor($seller->subdomain));
        }
    }
}
