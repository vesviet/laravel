<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateSellerProductAction
{
    /**
     * Create a new Product for a Seller. ADR-S2: Action owns the tx.
     */
    public function execute(array $data): Product
    {
        try {
            return DB::transaction(function () use ($data) {
                $data['show_on_marketplace'] = $data['show_on_marketplace'] ?? false;

                return Product::create($data);
            });
        } catch (Throwable $e) {
            throw new \RuntimeException(
                'Không thể tạo sản phẩm: '.$e->getMessage(),
                0,
                $e
            );
        }
    }
}
