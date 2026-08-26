<?php

namespace App\Actions;

use App\Models\SellerProfile;
use App\Models\SellerPage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Exception;

class RegisterSellerAction
{
    /**
     * Register a new seller and provision their default page within a transaction.
     *
     * @param array $data
     * @return SellerProfile
     * @throws RuntimeException
     */
    public function execute(array $data): SellerProfile
    {
        try {
            return DB::transaction(function () use ($data) {
                // Determine user (create if not exists or passed)
                // For simplicity, we assume the user is already authenticated or we create a new user based on phone/email
                $user = auth()->user();
                
                if (!$user) {
                    $user = User::firstOrCreate(
                        ['phone' => $data['phone']],
                        [
                            'name' => $data['shop_name'],
                            'password' => bcrypt(Str::random(12)), // random password if just fast-onboarding
                        ]
                    );
                    // Usually we'd login the user here if this is a public onboarding
                }

                // Create the Seller Profile
                $sellerProfile = SellerProfile::create([
                    'user_id' => $user->id,
                    'shop_name' => $data['shop_name'],
                    'subdomain' => $this->generateUniqueSubdomain($data['shop_name']),
                    'phone' => $data['phone'],
                    'status' => 'active',
                ]);

                // Provision default Seller Page (One-Page Carrd style)
                SellerPage::create([
                    'seller_id' => $sellerProfile->id,
                    'is_published' => true,
                    'theme_config' => [
                        'primary_color' => '#3b82f6',
                        'font' => 'Inter',
                        'mode' => 'light'
                    ],
                    'blocks' => [
                        [
                            'type' => 'hero',
                            'data' => [
                                'title' => 'Chào mừng đến với ' . $data['shop_name'],
                                'subtitle' => 'Chuyên cung cấp các sản phẩm chất lượng cao',
                            ]
                        ],
                        [
                            'type' => 'products',
                            'data' => [
                                'title' => 'Sản phẩm nổi bật',
                                'limit' => 6
                            ]
                        ]
                    ]
                ]);

                // Assign role if Spatie Permission is used
                if (method_exists($user, 'assignRole')) {
                    // Make sure the role 'seller' exists in DB first, handled via seeder
                    // $user->assignRole('seller');
                }

                return $sellerProfile;
            });
        } catch (Exception $e) {
            throw new RuntimeException('Đăng ký tài khoản Seller thất bại: ' . $e->getMessage());
        }
    }

    private function generateUniqueSubdomain(string $shopName): string
    {
        $baseSlug = Str::slug($shopName);
        $slug = $baseSlug;
        $counter = 1;
        
        $reserved = ['admin', 'seller', 'app', 'api', 'www', 'cpanel', 'mail', 'dev', 'staging'];

        while (SellerProfile::where('subdomain', $slug)->exists() || in_array($slug, $reserved)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
