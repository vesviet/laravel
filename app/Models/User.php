<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
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
}
