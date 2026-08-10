<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and accessories',
        ]);

        $clothing = Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
            'description' => 'Apparel and fashion',
        ]);

        // Products
        $laptop = Product::create([
            'category_id' => $electronics->id,
            'name' => 'MacBook Pro 16"',
            'slug' => 'macbook-pro-16',
            'sku' => 'MBP-16-2023',
            'description' => 'Powerful laptop for professionals.',
            'price' => 2500.00,
            'stock' => 10,
            'status' => 'published',
            'attributes_json' => ['color' => ['Space Gray', 'Silver'], 'storage' => ['512GB', '1TB']],
            'seo_title' => 'Buy MacBook Pro 16"',
            'seo_description' => 'Get the latest MacBook Pro.',
        ]);

        $tshirt = Product::create([
            'category_id' => $clothing->id,
            'name' => 'Basic Cotton T-Shirt',
            'slug' => 'basic-cotton-tshirt',
            'sku' => 'TSHIRT-BASIC',
            'description' => 'Comfortable 100% cotton t-shirt.',
            'price' => 20.00,
            'stock' => 0, // Stock handled by variants
            'status' => 'published',
            'attributes_json' => ['size' => ['S', 'M', 'L'], 'color' => ['Black', 'White']],
        ]);

        // Product Variants
        ProductVariant::create([
            'product_id' => $tshirt->id,
            'name' => 'Black - Size M',
            'sku' => 'TSHIRT-BASIC-BLK-M',
            'price' => 20.00,
            'stock' => 50,
            'attributes_json' => ['size' => 'M', 'color' => 'Black'],
        ]);

        ProductVariant::create([
            'product_id' => $tshirt->id,
            'name' => 'White - Size L',
            'sku' => 'TSHIRT-BASIC-WHT-L',
            'price' => 22.00,
            'stock' => 30,
            'attributes_json' => ['size' => 'L', 'color' => 'White'],
        ]);

        // Provinces
        $this->call(ProvinceSeeder::class);
    }
}
