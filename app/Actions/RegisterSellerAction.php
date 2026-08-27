<?php

namespace App\Actions;

use App\Models\SellerProfile;
use App\Models\SellerPage;
use App\Models\User;
use App\Exceptions\SellerActionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class RegisterSellerAction
{
    /**
     * Register a new seller and provision their default page within a transaction.
     *
     * @param  array  $data
     * @return SellerProfile
     *
     * @throws SellerActionException
     */
    public function execute(array $data): SellerProfile
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = auth()->user();

                if (! $user && isset($data['user_id'])) {
                    $user = User::find($data['user_id']);
                }

                if (! $user) {
                    $user = User::firstOrCreate(
                        ['email' => $data['email'] ?? null],
                        [
                            'name' => $data['shop_name'],
                            'password' => bcrypt(Str::random(12)),
                        ]
                    );
                }

                $sellerProfile = SellerProfile::create([
                    'user_id' => $user->id,
                    'shop_name' => $data['shop_name'],
                    'subdomain' => (new SellerProfile)->generateUniqueSubdomain($data['shop_name']),
                    'phone' => $data['phone'],
                    'email' => $data['email'] ?? null,
                    'status' => 'active',
                ]);

                SellerPage::create([
                    'seller_id' => $sellerProfile->id,
                    'is_published' => true,
                    'theme_config' => [
                        'primary_color' => '#3b82f6',
                        'font' => 'Inter',
                        'mode' => 'light',
                    ],
                    'blocks' => [
                        [
                            'type' => 'hero',
                            'data' => [
                                'title' => 'Chào mừng đến với '.$data['shop_name'],
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
            throw new SellerActionException(
                'Đăng ký tài khoản Seller thất bại: '.$e->getMessage(),
                previous: $e
            );
        }
    }
}
