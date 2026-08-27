<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $panelId = $this->safePanelId($panel);

        if ($panelId === 'seller') {
            return $this->sellerProfile !== null && $this->sellerProfile->status === 'active';
        }

        if ($panelId === null) {
            // Default panel access for tools that resolve a bare Panel instance
            // (e.g. `app(Panel::class)` outside the Filament registry).
            return $this->hasAnyRole((array) config('auth.admin_roles', []));
        }

        return $this->hasAnyRole((array) config('auth.admin_roles', []));
    }

    private function safePanelId(Panel $panel): ?string
    {
        try {
            return $panel->getId();
        } catch (\Throwable) {
            return null;
        }
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function getTenants(Panel $panel): array|Collection
    {
        if ($this->sellerProfile) {
            return collect([$this->sellerProfile]);
        }

        return collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->sellerProfile && $this->sellerProfile->id === $tenant->id;
    }
}
