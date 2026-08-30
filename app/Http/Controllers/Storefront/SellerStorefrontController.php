<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\SellerPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Multitenancy\Models\Tenant;

/**
 * SellerStorefrontController — serves the public-facing seller storefront pages.
 *
 * SF-08 fix: The index() action now caches the published SellerPage for 10 minutes
 * using the SellerPage::cacheKeyFor() key. Cache is invalidated by:
 *   - UpdateSellerPageAction (when seller saves page config)
 *   - PublishSellerPageAction (when seller publishes/unpublishes)
 *
 * The preview() action intentionally bypasses cache — it always shows the
 * latest draft to the authenticated seller.
 */
class SellerStorefrontController extends Controller
{
    /**
     * Cache TTL for the public storefront (seconds).
     * Matches typical seller page update frequency.
     */
    private const CACHE_TTL = 600; // 10 minutes

    /**
     * Display the published Seller Storefront.
     *
     * SF-08: Uses Cache::remember() to avoid hitting the DB on every request.
     * The cache key is derived from the seller's subdomain so each seller has
     * an independent cache entry.
     */
    public function index(Request $request, string $seller_subdomain)
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            abort(404, 'Store not found.');
        }

        // Attempt to load the published page from cache; fall back to DB.
        $page = Cache::remember(
            SellerPage::cacheKeyFor($tenant->subdomain),
            self::CACHE_TTL,
            fn () => SellerPage::where('seller_id', $tenant->id)
                ->where('is_published', true)
                ->first(),
        );

        if (! $page) {
            // Cache::remember returns null if the closure returns null.
            // No page configured or not published yet.
            abort(404, 'Store page is not published yet.');
        }

        return view('storefront.seller.index', [
            'page'      => $page,
            'tenant'    => $tenant,
            'isPreview' => false,
        ]);
    }

    /**
     * Display the preview of the Seller Storefront for the Filament panel.
     *
     * Preview intentionally bypasses cache — it must always show the latest
     * unsaved/unpublished draft to the authenticated seller.
     */
    public function preview(Request $request)
    {
        $user = auth()->user();

        if (! $user || ! $user->sellerProfile) {
            abort(403, 'Unauthorized.');
        }

        $tenant = $user->sellerProfile;

        // Fetch latest data directly — no cache for preview.
        $page = SellerPage::where('seller_id', $tenant->id)->first();

        if (! $page) {
            return response('No page configured yet. Please configure and save.', 200);
        }

        return view('storefront.seller.index', [
            'page'      => $page,
            'tenant'    => $tenant,
            'isPreview' => true,
        ]);
    }
}
