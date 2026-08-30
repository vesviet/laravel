<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Orders in the Seller Panel.
 *
 * Sellers can:
 *   - view their own orders
 *   - update status of their own orders (state machine transitions only)
 *
 * Sellers CANNOT:
 *   - delete orders (financial audit trail must be preserved)
 *   - force-delete orders
 *   - view orders from other sellers
 *
 * ADR-S3 Trust Zone: Standard — PR review required.
 */
class SellerOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        if (! $this->isSellerPanel()) {
            return $user->can('view_any_order');
        }

        return $this->hasActiveTenant($user);
    }

    /**
     * Determine whether the user can view a specific order.
     */
    public function view(User $user, Order $order): bool
    {
        if (! $this->isSellerPanel()) {
            return $user->can('view_order');
        }

        return $this->ownsOrder($user, $order);
    }

    /**
     * Sellers do not create orders directly — orders come from storefront.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Sellers may update order status only for their own orders.
     *
     * Note: The resource limits which status transitions are available.
     * This policy only checks ownership; state-machine enforcement is
     * handled in the Order model (OrderStatus::canTransitionTo).
     */
    public function update(User $user, Order $order): bool
    {
        if (! $this->isSellerPanel()) {
            return $user->can('update_order');
        }

        return $this->ownsOrder($user, $order);
    }

    /**
     * Orders must NOT be deleted — financial audit trail.
     * Returning false removes the delete action from Filament UI.
     */
    public function delete(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Bulk delete is explicitly disallowed for sellers.
     * Returning false removes DeleteBulkAction from Filament table.
     */
    public function deleteAny(User $user): bool
    {
        return false;
    }

    /**
     * Restore is not exposed in Seller Panel.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Force delete is not exposed in Seller Panel.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }

    // ─── Private helpers ────────────────────────────────────────────────────

    /**
     * Returns true only when evaluated inside the Seller Filament Panel,
     * OR when no Filament panel is active (CLI / test / queue context).
     *
     * The admin panel always registers its panel ID as 'admin' — it will
     * never be null when an admin is browsing. null means we are in a
     * non-HTTP context (Pest, tinker, queue worker) where seller logic
     * is the correct scope for this policy class.
     */
    private function isSellerPanel(): bool
    {
        $panel = Filament::getCurrentPanel();

        // null  → test / CLI / queue — apply seller ownership logic
        // 'seller' → Seller Filament Panel — apply seller ownership logic
        // 'admin'  → Admin Filament Panel — fall through to Spatie can() checks
        return $panel === null || $panel->getId() === 'seller';
    }

    private function hasActiveTenant(User $user): bool
    {
        return $user->sellerProfile !== null && $user->sellerProfile->isActive();
    }

    private function ownsOrder(User $user, Order $order): bool
    {
        $profile = $user->sellerProfile;

        if ($profile === null || ! $profile->isActive()) {
            return false;
        }

        return (int) $order->seller_id === (int) $profile->id;
    }
}
