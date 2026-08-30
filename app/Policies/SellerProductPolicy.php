<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Product within the Seller Panel.
 *
 * Each seller may only view, create, edit, and delete their own products.
 * Product ownership is verified against the active Filament tenant (SellerProfile).
 *
 * This policy is registered in AppServiceProvider via Gate::policy() and is
 * consumed by SimpleProductResource in the Seller Filament panel.
 *
 * ADR-S3 Trust Zone: Standard — requires PR review before merging.
 */
class SellerProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any products.
     * Requires an active seller profile (tenant).
     */
    public function viewAny(User $user): bool
    {
        if (! $this->isSellerPanel()) {
            return $user->can('view_any_product');
        }

        return $this->hasActiveTenant($user);
    }

    /**
     * Determine whether the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        if (! $this->isSellerPanel()) {
            return $user->can('view_product');
        }

        return $this->ownsTenant($user, $product);
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        if (! $this->isSellerPanel()) {
            return $user->can('create_product');
        }

        return $this->hasActiveTenant($user);
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        if (! $this->isSellerPanel()) {
            return $user->can('update_product');
        }

        return $this->ownsTenant($user, $product);
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        if (! $this->isSellerPanel()) {
            return $user->can('delete_product');
        }

        return $this->ownsTenant($user, $product);
    }

    /**
     * Determine whether the user can bulk delete products.
     * Only allowed if all selected products belong to the active tenant.
     */
    public function deleteAny(User $user): bool
    {
        if (! $this->isSellerPanel()) {
            return $user->can('delete_any_product');
        }

        return $this->hasActiveTenant($user);
    }

    /**
     * Determine whether the user can restore soft-deleted products.
     * Not exposed in Seller Panel — always false.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can force-delete products.
     * Not exposed in Seller Panel — always false.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }

    // ─── Private helpers ────────────────────────────────────────────────────

    /**
     * Returns true only when evaluated inside the Seller Filament Panel.
     * This prevents SellerProductPolicy from overriding admin panel behavior
     * for non-super_admin admin users who have standard Spatie permissions.
     */
    private function isSellerPanel(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'seller';
    }

    /**
     * Check that the authenticated user has an active SellerProfile
     * and that it is the current Filament tenant.
     */
    private function hasActiveTenant(User $user): bool
    {
        $profile = $user->sellerProfile;

        return $profile !== null && $profile->isActive();
    }

    /**
     * Check that the product's seller_id matches the authenticated user's
     * SellerProfile ID (the active Filament tenant).
     */
    private function ownsTenant(User $user, Product $product): bool
    {
        $profile = $user->sellerProfile;

        if ($profile === null || ! $profile->isActive()) {
            return false;
        }

        return (int) $product->seller_id === (int) $profile->id;
    }
}
