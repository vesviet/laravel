<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Spatie\Multitenancy\Models\Tenant;

class TenantSellerScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Tenant::checkCurrent()) {
            $builder->where('seller_id', Tenant::current()->id);
        }
    }
}
