<?php

namespace App\Http\Middleware;

use App\Models\SellerProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureSellerTenantForgotten — cleanup middleware for /shop/* routes.
 *
 * Slice 2 / ADR-SC1: When SellerStorefrontController::shopPath() calls
 * $seller->makeCurrent(), it sets Spatie's active tenant in memory for that
 * request. This is necessary so QuickCheckout Livewire (which calls
 * Tenant::current()) works correctly.
 *
 * However, in persistent-process environments (Laravel Octane, queue workers),
 * the tenant context can leak to the next request if not explicitly cleared.
 *
 * This middleware's terminate() method runs AFTER the response is sent,
 * ensuring forgetCurrent() is always called at request end.
 *
 * Applied to: /shop/{shop_slug} route ONLY (not global middleware).
 */
class EnsureSellerTenantForgotten
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Runs after the response has been sent to the browser.
     * Clears the Spatie tenant context to prevent cross-request leakage.
     */
    public function terminate(Request $request, Response $response): void
    {
        SellerProfile::forgetCurrent();
    }
}
