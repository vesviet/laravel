<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\PromotionRule;
use App\Services\Promotions\PromotionEngine;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PromotionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('promotion seeder populates exactly 7 scandinavian promotion campaigns', function () {
    $this->seed(PromotionSeeder::class);

    expect(PromotionRule::count())->toBe(7);
});

test('promotion seeder populates campaign 1 WELCOME10 coupon rule with exact attributes', function () {
    $this->seed(PromotionSeeder::class);

    $rule = PromotionRule::where('code', 'WELCOME10')->first();

    expect($rule)->not->toBeNull()
        ->and($rule->rule_type)->toBe(PromotionRule::RULE_TYPE_CART)
        ->and($rule->action_type)->toBe(PromotionRule::ACTION_PERCENTAGE)
        ->and((float) $rule->discount_value)->toBe(10.0)
        ->and((float) $rule->max_discount_amount)->toBe(500000.0)
        ->and((float) $rule->min_order_amount)->toBe(300000.0)
        ->and($rule->usage_limit_per_user)->toBe(1)
        ->and($rule->priority)->toBe(10)
        ->and($rule->is_active)->toBeTrue()
        ->and($rule->isCoupon())->toBeTrue();
});

test('promotion seeder populates campaign 2 TIERED_PROMO automatic cart rule with exact tiered steps', function () {
    $this->seed(PromotionSeeder::class);

    $rule = PromotionRule::where('action_type', PromotionRule::ACTION_TIERED_QUANTITY)->first();

    expect($rule)->not->toBeNull()
        ->and($rule->rule_type)->toBe(PromotionRule::RULE_TYPE_CART)
        ->and($rule->code)->toBeNull()
        ->and($rule->priority)->toBe(20)
        ->and($rule->is_active)->toBeTrue()
        ->and($rule->isAutomatic())->toBeTrue()
        ->and($rule->conditions)->toBeArray()
        ->and($rule->conditions['tiered_steps'])->toHaveCount(3);

    $steps = $rule->conditions['tiered_steps'];
    expect($steps[0]['min_qty'])->toBe(2)
        ->and((float) ($steps[0]['discount_value'] ?? $steps[0]['discount_percent']))->toBe(5.0)
        ->and($steps[1]['min_qty'])->toBe(4)
        ->and((float) ($steps[1]['discount_value'] ?? $steps[1]['discount_percent']))->toBe(10.0)
        ->and($steps[2]['min_qty'])->toBe(6)
        ->and((float) ($steps[2]['discount_value'] ?? $steps[2]['discount_percent']))->toBe(15.0);
});

test('promotion seeder populates campaign 3 BUY_DESK_GET_CHAIR automatic BXGY rule linking desk and chair', function () {
    $this->seed(PromotionSeeder::class);

    $rule = PromotionRule::where('action_type', PromotionRule::ACTION_BUY_X_GET_Y)->first();
    $desk = Product::where('slug', 'copenhague-desk')->first();
    $chair = Product::where('slug', 'synnes-dining-chair')->first();

    expect($desk)->not->toBeNull()
        ->and($chair)->not->toBeNull()
        ->and($rule)->not->toBeNull()
        ->and($rule->rule_type)->toBe(PromotionRule::RULE_TYPE_CART)
        ->and($rule->code)->toBeNull()
        ->and($rule->priority)->toBe(30)
        ->and($rule->is_active)->toBeTrue()
        ->and($rule->conditions)->toBeArray()
        ->and($rule->conditions['trigger_product_ids'])->toContain($desk->id)
        ->and($rule->conditions['reward_product_id'])->toBe($chair->id)
        ->and((float) $rule->conditions['reward_discount_percent'])->toBe(100.0)
        ->and($rule->conditions['bxgy_config'])->toBeArray()
        ->and($rule->conditions['bxgy_config']['buy_product_id'])->toBe($desk->id)
        ->and($rule->conditions['bxgy_config']['get_product_id'])->toBe($chair->id);
});

test('promotion seeder populates campaign 4 CATALOG_LIGHTING_15 catalog rule for lighting category', function () {
    $this->seed(PromotionSeeder::class);

    $rule = PromotionRule::where('rule_type', PromotionRule::RULE_TYPE_CATALOG)->first();
    $lightingCategory = Category::where('slug', 'den-chieu-sang')->first();

    expect($lightingCategory)->not->toBeNull()
        ->and($rule)->not->toBeNull()
        ->and($rule->action_type)->toBe(PromotionRule::ACTION_PERCENTAGE)
        ->and((float) $rule->discount_value)->toBe(15.0)
        ->and($rule->priority)->toBe(5)
        ->and($rule->is_active)->toBeTrue()
        ->and($rule->isCatalogRule())->toBeTrue()
        ->and($rule->conditions)->toBeArray()
        ->and($rule->conditions['category_ids'])->toContain($lightingCategory->id);
});

test('promotion seeder populates campaign 5 FREESHIP500 automatic free shipping rule for orders from 500k', function () {
    $this->seed(PromotionSeeder::class);

    $rule = PromotionRule::where('action_type', PromotionRule::ACTION_FREE_SHIPPING)->first();

    expect($rule)->not->toBeNull()
        ->and($rule->rule_type)->toBe(PromotionRule::RULE_TYPE_CART)
        ->and($rule->code)->toBeNull()
        ->and((float) $rule->min_order_amount)->toBe(500000.0)
        ->and($rule->priority)->toBe(50)
        ->and($rule->is_active)->toBeTrue()
        ->and($rule->isAutomatic())->toBeTrue();
});

test('promotion seeder populates campaign 6 VIPGOLD20 coupon rule targeting VIP Gold tier with 1M cap', function () {
    $this->seed(PromotionSeeder::class);

    $rule = PromotionRule::where('code', 'VIPGOLD20')->first();

    expect($rule)->not->toBeNull()
        ->and($rule->rule_type)->toBe(PromotionRule::RULE_TYPE_CART)
        ->and($rule->action_type)->toBe(PromotionRule::ACTION_PERCENTAGE)
        ->and((float) $rule->discount_value)->toBe(20.0)
        ->and($rule->target_customer_tier)->toBe('vip_gold')
        ->and((float) $rule->max_discount_amount)->toBe(1000000.0)
        ->and($rule->priority)->toBe(5)
        ->and($rule->is_active)->toBeTrue()
        ->and($rule->isCoupon())->toBeTrue();
});

test('promotion seeder dynamically resolves existing categories and products without duplicate creation', function () {
    // Pre-create lighting category and desk/chair products with custom initial IDs
    $existingLighting = Category::create([
        'name' => 'Đèn Chiếu Sáng Pre-existing',
        'slug' => 'den-chieu-sang',
        'description' => 'Pre-existing lighting category',
    ]);

    $existingDesk = Product::create([
        'name' => 'Bàn Làm Việc Copenhague Desk Pre-existing',
        'slug' => 'copenhague-desk',
        'sku' => 'DSK-005',
        'price' => 14200000,
        'stock' => 10,
        'status' => 'published',
    ]);

    $existingChair = Product::create([
        'name' => 'Ghế Ăn Gỗ Sồi Synnes Dining Chair Pre-existing',
        'slug' => 'synnes-dining-chair',
        'sku' => 'CHR-004',
        'price' => 5800000,
        'stock' => 15,
        'status' => 'published',
    ]);

    $this->seed(PromotionSeeder::class);

    // Verify seeder reused existing IDs
    $catalogRule = PromotionRule::where('rule_type', PromotionRule::RULE_TYPE_CATALOG)->first();
    expect($catalogRule->conditions['category_ids'])->toContain($existingLighting->id);

    $bxgyRule = PromotionRule::where('action_type', PromotionRule::ACTION_BUY_X_GET_Y)->first();
    expect($bxgyRule->conditions['trigger_product_ids'])->toContain($existingDesk->id)
        ->and($bxgyRule->conditions['reward_product_id'])->toBe($existingChair->id);

    // Ensure no duplicate categories/products created
    expect(Category::where('slug', 'den-chieu-sang')->count())->toBe(1)
        ->and(Product::where('slug', 'copenhague-desk')->count())->toBe(1)
        ->and(Product::where('slug', 'synnes-dining-chair')->count())->toBe(1);
});

test('promotion seeder is strictly idempotent across consecutive executions', function () {
    // 1st run
    $this->seed(PromotionSeeder::class);
    expect(PromotionRule::count())->toBe(7);

    // 2nd run
    $this->seed(PromotionSeeder::class);
    expect(PromotionRule::count())->toBe(7);

    // 3rd run
    $this->seed(PromotionSeeder::class);
    expect(PromotionRule::count())->toBe(7);

    // Verify all 6 rules still exist with correct codes and actions
    expect(PromotionRule::where('code', 'WELCOME10')->count())->toBe(1)
        ->and(PromotionRule::where('code', 'VIPGOLD20')->count())->toBe(1)
        ->and(PromotionRule::where('action_type', PromotionRule::ACTION_TIERED_QUANTITY)->count())->toBe(1)
        ->and(PromotionRule::where('action_type', PromotionRule::ACTION_BUY_X_GET_Y)->count())->toBe(1)
        ->and(PromotionRule::where('rule_type', PromotionRule::RULE_TYPE_CATALOG)->count())->toBe(1)
        ->and(PromotionRule::where('action_type', PromotionRule::ACTION_FREE_SHIPPING)->count())->toBe(1);
});

test('promotion engine correctly applies seeded WELCOME10 coupon on eligible orders', function () {
    $this->seed(PromotionSeeder::class);
    $engine = app(PromotionEngine::class);

    // Eligible order: 1.000.000₫ >= 300.000₫ min order => 10% = 100.000₫ discount (under 500k cap)
    $cartItems = [
        ['product_id' => 10, 'price' => 1000000.0, 'quantity' => 1, 'subtotal' => 1000000.0],
    ];

    $breakdown = $engine->calculateCartDiscounts(1000000.0, $cartItems, 'WELCOME10');

    expect($breakdown->hasCouponApplied())->toBeTrue()
        ->and($breakdown->couponDiscount)->toBe(100000.0)
        ->and($breakdown->totalDiscount)->toBeGreaterThanOrEqual(100000.0);

    // Ineligible order: 200.000₫ < 300.000₫ min order
    $smallCartItems = [
        ['product_id' => 10, 'price' => 200000.0, 'quantity' => 1, 'subtotal' => 200000.0],
    ];

    $smallBreakdown = $engine->calculateCartDiscounts(200000.0, $smallCartItems, 'WELCOME10');
    expect($smallBreakdown->hasCouponApplied())->toBeFalse();
});

test('promotion engine correctly applies seeded TIERED_PROMO volume discounts', function () {
    $this->seed(PromotionSeeder::class);
    $engine = app(PromotionEngine::class);

    // 2 items => 5% discount
    $cart2 = [
        ['product_id' => 1, 'price' => 100000.0, 'quantity' => 2, 'subtotal' => 200000.0],
    ];
    $breakdown2 = $engine->calculateCartDiscounts(200000.0, $cart2);
    expect($breakdown2->itemDiscounts)->toBe(10000.0); // 5% of 200k = 10k

    // 4 items => 10% discount
    $cart4 = [
        ['product_id' => 1, 'price' => 100000.0, 'quantity' => 4, 'subtotal' => 400000.0],
    ];
    $breakdown4 = $engine->calculateCartDiscounts(400000.0, $cart4);
    expect($breakdown4->itemDiscounts)->toBe(40000.0); // 10% of 400k = 40k

    // 6 items => 15% discount
    $cart6 = [
        ['product_id' => 1, 'price' => 100000.0, 'quantity' => 6, 'subtotal' => 600000.0],
    ];
    $breakdown6 = $engine->calculateCartDiscounts(600000.0, $cart6);
    expect($breakdown6->itemDiscounts)->toBe(90000.0); // 15% of 600k = 90k
});

test('promotion engine correctly applies seeded BUY_DESK_GET_CHAIR reward discount', function () {
    $this->seed(PromotionSeeder::class);
    $engine = app(PromotionEngine::class);

    $desk = Product::where('slug', 'copenhague-desk')->first();
    $chair = Product::where('slug', 'synnes-dining-chair')->first();

    $cartItems = [
        ['product_id' => $desk->id, 'product_name' => $desk->name, 'price' => (float) $desk->price, 'quantity' => 1, 'subtotal' => (float) $desk->price],
        ['product_id' => $chair->id, 'product_name' => $chair->name, 'price' => (float) $chair->price, 'quantity' => 1, 'subtotal' => (float) $chair->price],
    ];

    $subtotal = (float) $desk->price + (float) $chair->price;
    $breakdown = $engine->calculateCartDiscounts($subtotal, $cartItems);

    // Chair price (5.800.000₫) is 100% discounted
    $bxgyDiscount = collect($breakdown->appliedRules)->firstWhere('actionType', PromotionRule::ACTION_BUY_X_GET_Y);
    expect($bxgyDiscount)->not->toBeNull()
        ->and($bxgyDiscount->discountAmount)->toBe((float) $chair->price);
});

test('promotion engine correctly resolves seeded CATALOG_LIGHTING_15 catalog strike price', function () {
    $this->seed(PromotionSeeder::class);
    $engine = app(PromotionEngine::class);

    $lightingCategory = Category::where('slug', 'den-chieu-sang')->first();
    $lampProduct = Product::create([
        'category_id' => $lightingCategory->id,
        'name' => 'Test Ambit Lamp',
        'slug' => 'test-ambit-lamp',
        'price' => 4000000.0,
        'stock' => 10,
        'status' => 'published',
    ]);

    $promotedResult = $engine->resolveProductPromotedPrice($lampProduct);

    expect($promotedResult)->not->toBeNull()
        ->and($promotedResult->originalPrice)->toBe(4000000.0)
        ->and($promotedResult->promotedPrice)->toBe(3400000.0) // 15% off 4.000.000 = 3.400.000
        ->and($promotedResult->discountPercent)->toBe(15.0)
        ->and($promotedResult->badgeLabel)->toBe('-15% PROMO');
});

test('promotion engine correctly applies seeded FREESHIP500 automatic free shipping rule', function () {
    $this->seed(PromotionSeeder::class);
    $engine = app(PromotionEngine::class);

    // Order of 600.000₫ with 35.000₫ shipping fee qualifies for freeship
    $cartItems = [
        ['product_id' => 1, 'price' => 600000.0, 'quantity' => 1, 'subtotal' => 600000.0],
    ];

    $breakdown = $engine->calculateCartDiscounts(600000.0, $cartItems, null, 35000.0);

    expect($breakdown->shippingDiscount)->toBe(35000.0)
        ->and($breakdown->finalShippingFee)->toBe(0.0);
});

test('promotion engine correctly applies seeded VIPGOLD20 coupon with tier validation and cap', function () {
    $this->seed(PromotionSeeder::class);
    $engine = app(PromotionEngine::class);

    $vipCustomer = Customer::create([
        'name' => 'VIP Gold Member',
        'email' => 'vipgold@example.com',
        'phone' => '0988776655',
        'password' => bcrypt('password'),
        'status' => 'published',
    ]);

    Order::create([
        'customer_id' => $vipCustomer->id,
        'order_number' => 'ORD-VIP-PRIOR',
        'customer_name' => $vipCustomer->name,
        'email' => $vipCustomer->email,
        'phone' => $vipCustomer->phone,
        'address' => '123 VIP St',
        'subtotal' => 25000000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
        'total_amount' => 25000000,
        'status' => OrderStatus::Delivered,
        'payment_method' => 'cod',
    ]);

    $regularCustomer = Customer::create([
        'name' => 'Regular Member',
        'email' => 'regular@example.com',
        'phone' => '0988112233',
        'password' => bcrypt('password'),
        'status' => 'published',
    ]);

    // Order of 8.000.000₫: 20% of 8M = 1.6M, capped at 1.000.000₫ max discount
    $cartItems = [
        ['product_id' => 1, 'price' => 8000000.0, 'quantity' => 1, 'subtotal' => 8000000.0],
    ];

    // VIP Gold customer gets 1.000.000₫ capped discount
    $vipBreakdown = $engine->calculateCartDiscounts(8000000.0, $cartItems, 'VIPGOLD20', 0.0, $vipCustomer);
    expect($vipBreakdown->hasCouponApplied())->toBeTrue()
        ->and($vipBreakdown->couponDiscount)->toBe(1000000.0);

    // Regular customer cannot apply VIPGOLD20
    $regularBreakdown = $engine->calculateCartDiscounts(8000000.0, $cartItems, 'VIPGOLD20', 0.0, $regularCustomer);
    expect($regularBreakdown->hasCouponApplied())->toBeFalse();
});

test('database seeder executes complete application seed including promotion seeder', function () {
    $this->seed(DatabaseSeeder::class);

    expect(PromotionRule::count())->toBe(7);

    $welcomeRule = PromotionRule::where('code', 'WELCOME10')->first();
    $vipRule = PromotionRule::where('code', 'VIPGOLD20')->first();
    $tieredRule = PromotionRule::where('action_type', PromotionRule::ACTION_TIERED_QUANTITY)->first();
    $bxgyRule = PromotionRule::where('action_type', PromotionRule::ACTION_BUY_X_GET_Y)->first();
    $catalogRule = PromotionRule::where('rule_type', PromotionRule::RULE_TYPE_CATALOG)->first();
    $freeshipRule = PromotionRule::where('action_type', PromotionRule::ACTION_FREE_SHIPPING)->first();

    expect($welcomeRule)->not->toBeNull()
        ->and($vipRule)->not->toBeNull()
        ->and($tieredRule)->not->toBeNull()
        ->and($bxgyRule)->not->toBeNull()
        ->and($catalogRule)->not->toBeNull()
        ->and($freeshipRule)->not->toBeNull();
});
