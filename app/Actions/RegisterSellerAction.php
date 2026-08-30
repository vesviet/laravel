<?php

namespace App\Actions;

use App\Exceptions\SellerActionException;
use App\Models\SellerPage;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Register a new Seller: creates SellerProfile + default SellerPage.
 *
 * CONTRACT: Caller is responsible for providing an already-persisted User.
 * This Action does NOT create the User — it receives one explicitly.
 *
 * ADR-S2: This Action owns the sole DB::transaction() boundary.
 * Services and Models MUST NOT open their own transactions when called from here.
 *
 * SF-09 fix: removed ambiguous auth()->user() fallback; User is now an explicit parameter.
 * SF-03 dependency: the Filament Register page wraps BOTH user creation AND this Action
 *   in a single outer transaction so orphan Users are impossible on SellerProfile failure.
 *
 * @throws SellerActionException
 */
class RegisterSellerAction
{
    /**
     * Register a new seller and provision their default page within a transaction.
     *
     * @param  User   $user  The already-created User that will own this seller profile.
     * @param  array  $data  Must contain: shop_name, phone. Optionally: email.
     * @return SellerProfile
     *
     * @throws SellerActionException
     */
    public function execute(User $user, array $data): SellerProfile
    {
        try {
            return DB::transaction(function () use ($user, $data) {
                $sellerProfile = SellerProfile::create([
                    'user_id'   => $user->id,
                    'shop_name' => $data['shop_name'],
                    'subdomain' => (new SellerProfile)->generateUniqueSubdomain($data['shop_name']),
                    'phone'     => $data['phone'],
                    'email'     => $data['email'] ?? null,
                    'status'    => 'active',
                ]);

                // Provision a default Seller Page with sensible defaults.
                SellerPage::create([
                    'seller_id'    => $sellerProfile->id,
                    'is_published' => true,
                    'theme_config' => [
                        'primary_color' => '#3b82f6',
                        'font'          => 'Inter',
                        'mode'          => 'light',
                    ],
                    'blocks' => [
                        [
                            'type' => 'hero',
                            'data' => [
                                'title'    => 'Chào mừng đến với ' . $data['shop_name'],
                                'subtitle' => 'Chuyên cung cấp các sản phẩm chất lượng cao',
                            ],
                        ],
                        [
                            'type' => 'products',
                            'data' => [
                                'title' => 'Sản phẩm nổi bật',
                                'limit' => 6,
                            ],
                        ],
                    ],
                ]);

                return $sellerProfile;
            });
        } catch (Throwable $e) {
            throw SellerActionException::registrationFailed($e);
        }
    }
}
