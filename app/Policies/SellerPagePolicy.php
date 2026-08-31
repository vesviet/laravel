<?php

namespace App\Policies;

use App\Models\SellerPage;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for SellerPage within the Seller Panel.
 *
 * P1-03 fix: SellerPageResource previously had no Policy, leaving authorization
 * gaps on page record access. This Policy mirrors SellerProductPolicy's
 * ownsTenant() pattern to ensure sellers can only view/edit their own page.
 *
 * A seller has exactly one SellerPage (created atomically with their profile
 * by RegisterSellerAction). CRUD is therefore constrained:
 *   - viewAny/create: requires active tenant (but create is hidden; page is pre-created)
 *   - view/update: requires ownership
 *   - delete/deleteAny/restore/forceDelete: false — pages are never deleted via UI
 *
 * This policy is registered in AppServiceProvider via Gate::policy().
 * It is consumed by SellerPageResource in the Seller Filament panel.
 *
 * ADR-S3 Trust Zone: Standard — requires PR review before merging.
 */
class SellerPagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any pages.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasActiveTenant($user);
    }

    /**
     * Determine whether the user can view the page.
     */
    public function view(User $user, SellerPage $page): bool
    {
        return $this->ownsTenant($user, $page);
    }

    /**
     * Determine whether the user can create a page.
     * Pages are created automatically by RegisterSellerAction — this is a safety gate.
     */
    public function create(User $user): bool
    {
        return $this->hasActiveTenant($user);
    }

    /**
     * Determine whether the user can update the page.
     */
    public function update(User $user, SellerPage $page): bool
    {
        return $this->ownsTenant($user, $page);
    }

    /**
     * Page deletion is not exposed in the Seller Panel — always false.
     */
    public function delete(User $user, SellerPage $page): bool
    {
        return false;
    }

    /**
     * Bulk deletion is not exposed in the Seller Panel — always false.
     */
    public function deleteAny(User $user): bool
    {
        return false;
    }

    /**
     * Restore is not exposed in the Seller Panel — always false.
     */
    public function restore(User $user, SellerPage $page): bool
    {
        return false;
    }

    /**
     * Force delete is not exposed in the Seller Panel — always false.
     */
    public function forceDelete(User $user, SellerPage $page): bool
    {
        return false;
    }

    // ─── Private helpers ────────────────────────────────────────────────────

    /**
     * Check that the authenticated user has an active SellerProfile.
     */
    private function hasActiveTenant(User $user): bool
    {
        $profile = $user->sellerProfile()->withoutGlobalScopes()->first();

        return $profile !== null && $profile->isActive();
    }

    /**
     * Check that the SellerPage's seller_id matches the user's active SellerProfile.
     */
    private function ownsTenant(User $user, SellerPage $page): bool
    {
        $profile = $user->sellerProfile()->withoutGlobalScopes()->first();

        if ($profile === null || ! $profile->isActive()) {
            return false;
        }

        return (int) $page->seller_id === (int) $profile->id;
    }
}
