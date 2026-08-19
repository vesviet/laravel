<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ReservedRouteRule implements ValidationRule
{
    /**
     * Reserved route names and system prefixes that cannot be used as dynamic slugs.
     *
     * @var array<int, string>
     */
    protected array $reservedSlugs = [
        'admin',
        'blog',
        'products',
        'categories',
        'cart',
        'checkout',
        'login',
        'register',
        'logout',
        'api',
        'about',
        'contact',
        'track-order',
        'account',
        'newsletter',
        'wishlist',
        'order-tracking',
        'password',
        'forgot-password',
        'reset-password',
        'storage',
        'livewire',
        'up',
        'horizon',
        'telescope',
        'shield',
        'settings',
        'sanctum',
        'oauth',
        'health',
    ];

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalized = strtolower(trim((string) $value));
        $normalized = trim($normalized, '/');

        if (in_array($normalized, $this->reservedSlugs, true)) {
            $fail("The slug '{$normalized}' conflicts with a reserved system route. Please choose another slug.");
        }
    }

    /**
     * Get the list of reserved slugs.
     *
     * @return array<int, string>
     */
    public function getReservedSlugs(): array
    {
        return $this->reservedSlugs;
    }
}
