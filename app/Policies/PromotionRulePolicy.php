<?php

namespace App\Policies;

use App\Models\PromotionRule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PromotionRulePolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * Grants full unrestricted access to users with the 'super_admin' role.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_promotion::rule') || $user->can('view_any_promotion_rule');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PromotionRule $promotionRule): bool
    {
        return $user->can('view_promotion::rule') || $user->can('view_promotion_rule');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_promotion::rule') || $user->can('create_promotion_rule');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PromotionRule $promotionRule): bool
    {
        return $user->can('update_promotion::rule') || $user->can('update_promotion_rule');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PromotionRule $promotionRule): bool
    {
        return $user->can('delete_promotion::rule') || $user->can('delete_promotion_rule');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_promotion::rule') || $user->can('delete_any_promotion_rule');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, PromotionRule $promotionRule): bool
    {
        return $user->can('force_delete_promotion::rule') || $user->can('force_delete_promotion_rule');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_promotion::rule') || $user->can('force_delete_any_promotion_rule');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, PromotionRule $promotionRule): bool
    {
        return $user->can('restore_promotion::rule') || $user->can('restore_promotion_rule');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_promotion::rule') || $user->can('restore_any_promotion_rule');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, PromotionRule $promotionRule): bool
    {
        return $user->can('replicate_promotion::rule') || $user->can('replicate_promotion_rule');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_promotion::rule') || $user->can('reorder_promotion_rule');
    }
}
