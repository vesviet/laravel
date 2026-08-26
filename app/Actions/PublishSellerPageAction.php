<?php

namespace App\Actions;

use App\Models\SellerPage;
use App\Models\SellerProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Exception;

class PublishSellerPageAction
{
    /**
     * Publish or unpublish seller's page and clear relevant cache.
     * Enforces ADR-S2 transactional safety.
     *
     * @param SellerProfile $seller
     * @param bool $publish
     * @return SellerPage
     * @throws RuntimeException
     */
    public function execute(SellerProfile $seller, bool $publish = true): SellerPage
    {
        try {
            return DB::transaction(function () use ($seller, $publish) {
                $page = $seller->pages()->first();
                if (!$page) {
                    throw new RuntimeException('Trang web chưa được khởi tạo.');
                }

                $page->is_published = $publish;
                $page->save();

                // Clear cache for this subdomain
                Cache::forget("seller_page_{$seller->subdomain}");

                return $page;
            });
        } catch (Exception $e) {
            throw new RuntimeException('Không thể cập nhật trạng thái trang: ' . $e->getMessage());
        }
    }
}
