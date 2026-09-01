<?php

use App\Livewire\AddToCartButton;
use App\Livewire\CartCount;
use App\Livewire\CartDrawer;
use App\Livewire\WishlistButton;
use App\Livewire\WishlistPage;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

beforeEach(function () {
    Cache::flush();
});

/*
|--------------------------------------------------------------------------
| SCENARIO 1: Complete Guest First-Time Visitor Shopping Journey
|--------------------------------------------------------------------------
| A new visitor arrives at homepage, explores the 7 sections, selects a
| featured product, adds it to cart, opens the cart drawer, proceeds to
| checkout, fills the shipping details, and confirms order creation.
*/

it('executes scenario 1: first-time visitor homepage discovery to direct checkout journey', function () {
    // Setup products in database
    $featuredProduct = Product::create([
        'name' => 'Sober Armchair Nordic Edition',
        'slug' => 'sober-armchair-nordic-edition',
        'price' => 3200000,
        'stock' => 10,
        'status' => 'published',
        'is_featured' => true,
    ]);

    // 1. Visitor arrives at homepage
    $homeResponse = $this->get(route('home'));
    $homeResponse->assertOk();

    // Verify all 7 sections are present
    $homeResponse->assertSee('Hero banner', false);
    $homeResponse->assertSee('Phong Cách Mùa Này');
    $homeResponse->assertSee('Sản Phẩm Nổi Bật');
    $homeResponse->assertSee('Sober Armchair Nordic Edition');
    $homeResponse->assertSee('Đồ Nội Thất');
    $homeResponse->assertSee('Sản Phẩm Mới');
    $homeResponse->assertSee('Miễn Phí Vận Chuyển');
    $homeResponse->assertSee('Newsletter');

    // 2. Visitor visits product detail page
    $featuredProduct->update(['status' => 'published']);
    $detailResponse = $this->get(route('products.show', $featuredProduct->slug));
    $detailResponse->assertOk();
    $detailResponse->assertSee('Sober Armchair Nordic Edition');
    $detailResponse->assertSee('3.200.000₫');

    // 3. Visitor adds product to cart (quantity 2)
    Livewire::test(AddToCartButton::class, ['product' => $featuredProduct])
        ->set('quantity', 2)
        ->call('addToCart')
        ->assertDispatched('toast')
        ->assertDispatched('cart-updated')
        ->assertDispatched('open-cart');

    // 4. Cart drawer displays item and subtotal = 6.400.000₫
    Livewire::test(CartDrawer::class)
        ->assertSee('Sober Armchair Nordic Edition')
        ->assertSee('6.400.000₫');

    // 5. Visitor navigates to checkout
    $checkoutPage = $this->get(route('checkout.index'));
    $checkoutPage->assertOk();
    $checkoutPage->assertSee('Sober Armchair Nordic Edition');
    $checkoutPage->assertSee('6.400.000₫');

    // 6. Visitor completes checkout with COD
    $postCheckout = $this->post(route('checkout.store'), [
        'customer_name' => 'Nguyen Van An',
        'phone' => '0909123456',
        'email' => 'an.nguyen@example.com',
        'address' => '123 Nguyen Thi Minh Khai, District 1, HCMC',
        'payment_method' => 'cod',
    ]);

    $postCheckout->assertSessionHasNoErrors();
    $postCheckout->assertRedirect();

    // 7. Verify order created in database with 5% combo discount (2 items) and cart session cleared
    $order = Order::latest('id')->first();
    expect($order)->not->toBeNull()
        ->and($order->customer_name)->toBe('Nguyen Van An')
        ->and($order->phone)->toBe('0909123456')
        ->and((float) $order->subtotal)->toBe(6400000.0)
        ->and((float) $order->discount_amount)->toBe(320000.0)
        ->and((float) $order->total_amount)->toBe(6080000.0);

    expect(Session::get('cart'))->toBeNull();
});

/*
|--------------------------------------------------------------------------
| SCENARIO 2: Catalog Discovery, Category Filter & Sorting Multi-Item Purchase
|--------------------------------------------------------------------------
| A shopper navigates to catalog, filters by category, sorts by price,
| adds multiple items with variants to cart, modifies quantity in drawer,
| and completes checkout.
*/

it('executes scenario 2: catalog browsing category filtering and multi-item cart purchase', function () {
    $categoryLiving = Category::create(['name' => 'Living Room', 'slug' => 'living-room']);
    $categoryOffice = Category::create(['name' => 'Home Office', 'slug' => 'home-office']);

    $tableProduct = Product::create([
        'name' => 'Scandinavian Oak Coffee Table',
        'slug' => 'scandinavian-oak-coffee-table',
        'price' => 1500000,
        'stock' => 10,
        'status' => 'published',
        'category_id' => $categoryLiving->id,
    ]);

    $chairProduct = Product::create([
        'name' => 'Ergonomic Desk Chair',
        'slug' => 'ergonomic-desk-chair',
        'price' => 2500000,
        'stock' => 15,
        'status' => 'published',
        'category_id' => $categoryOffice->id,
    ]);

    $chairVariant = ProductVariant::create([
        'product_id' => $chairProduct->id,
        'name' => 'Black Leather',
        'sku' => 'EDC-BLK',
        'price' => 2800000,
        'stock' => 5,
    ]);

    // 1. Shopper browses Living Room category sorted by price_asc
    $catalogResponse = $this->get(route('products.index', [
        'category' => 'living-room',
        'sort' => 'price_asc',
    ]));
    $catalogResponse->assertOk();
    $catalogResponse->assertSee('Scandinavian Oak Coffee Table');
    $catalogResponse->assertDontSee('Ergonomic Desk Chair');

    // 2. Add table to cart
    Livewire::test(AddToCartButton::class, ['product' => $tableProduct])
        ->set('quantity', 1)
        ->call('addToCart');

    // 3. Add desk chair with variant to cart
    Livewire::test(AddToCartButton::class, ['product' => $chairProduct])
        ->set('variantId', $chairVariant->id)
        ->set('quantity', 1)
        ->call('addToCart');

    // 4. Cart drawer contains both items
    Livewire::test(CartDrawer::class)
        ->assertSee('Scandinavian Oak Coffee Table')
        ->assertSee('Ergonomic Desk Chair')
        ->assertSee('Black Leather');

    // 5. Shopper updates table quantity to 2 directly in drawer
    Livewire::test(CartDrawer::class)
        ->call('updateQuantity', $tableProduct->id, null, 2);

    // 6. Subtotal is (1.500.000 * 2) + (2.800.000 * 1) = 5.800.000₫
    Livewire::test(CartDrawer::class)
        ->assertSee('5.800.000₫');

    // 7. Complete checkout
    $checkoutResponse = $this->post(route('checkout.store'), [
        'customer_name' => 'Tran Thi Binh',
        'phone' => '0987654321',
        'address' => '456 Tran Hung Dao, District 5, HCMC',
        'payment_method' => 'cod',
    ]);

    $checkoutResponse->assertSessionHasNoErrors();
    $checkoutResponse->assertRedirect();

    // 8. Order is stored with 5% combo discount on 3 items
    $order = Order::latest('id')->first();
    expect((float) $order->subtotal)->toBe(5800000.0)
        ->and((float) $order->discount_amount)->toBe(290000.0)
        ->and((float) $order->total_amount)->toBe(5510000.0);
});

/*
|--------------------------------------------------------------------------
| SCENARIO 3: Customer Registration, Authentication, Wishlist & Order History
|--------------------------------------------------------------------------
| A returning customer logs in, header updates to authenticated state,
| customer saves items to wishlist, places an order, and inspects their
| order history page.
*/

it('executes scenario 3: registered customer wishlist curation and order history pipeline', function () {
    $customer = Customer::create([
        'name' => 'Hoang Long',
        'email' => 'hoanglong@example.com',
        'password' => bcrypt('secret123'),
        'phone' => '0918273645',
        'status' => 'published',
    ]);

    $product1 = Product::create([
        'name' => 'Minimalist Wooden Bench',
        'slug' => 'minimalist-wooden-bench',
        'price' => 1800000,
        'stock' => 8,
        'status' => 'published',
        'is_featured' => true,
    ]);

    $product2 = Product::create([
        'name' => 'Ceramic Table Lamp',
        'slug' => 'ceramic-table-lamp',
        'price' => 750000,
        'stock' => 12,
        'status' => 'published',
        'is_featured' => false,
    ]);

    // 1. Customer authenticates and header shows customer account links
    $homeAuth = $this->actingAs($customer, 'customer')->get(route('home'));
    $homeAuth->assertOk();
    $homeAuth->assertSee(route('account.orders'), false);
    $homeAuth->assertSee(route('account.wishlist'), false);
    $homeAuth->assertDontSee(route('account.login'), false);

    // 2. Customer adds product1 to wishlist
    Livewire::actingAs($customer, 'customer')
        ->test(WishlistButton::class, ['product' => $product1])
        ->call('toggleWishlist');

    $this->assertDatabaseHas('wishlists', [
        'customer_id' => $customer->id,
        'product_id' => $product1->id,
    ]);

    // 3. Wishlist page displays saved item
    Livewire::actingAs($customer, 'customer')
        ->test(WishlistPage::class)
        ->assertSee('Minimalist Wooden Bench');

    // 4. Customer places an order
    Session::put('cart', [
        "{$product1->id}_0" => [
            'product_id' => $product1->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ],
    ]);

    $orderResponse = $this->actingAs($customer, 'customer')->post(route('checkout.store'), [
        'customer_name' => $customer->name,
        'phone' => $customer->phone,
        'email' => $customer->email,
        'address' => '789 Vo Van Kiet, Q1',
        'payment_method' => 'cod',
    ]);

    $orderResponse->assertSessionHasNoErrors();
    $orderResponse->assertRedirect();

    $order = Order::latest('id')->first();

    // 5. Customer views order history in /account/orders
    $ordersPage = $this->actingAs($customer, 'customer')->get(route('account.orders'));
    $ordersPage->assertOk();
    $ordersPage->assertSee($order->order_number);
});

/*
|--------------------------------------------------------------------------
| SCENARIO 4: Footer Brand Engagement, Newsletter & Order Tracking
|--------------------------------------------------------------------------
| A user engages with the 4-layer footer, subscribes to newsletter,
| accesses customer service order lookup, and verifies order progress.
*/

it('executes scenario 4: footer brand engagement newsletter subscription and order tracking', function () {
    // 1. Visitor views footer on homepage
    $response = $this->get(route('home'));
    $response->assertOk();
    $response->assertSee('Newsletter');
    $response->assertSee('VỀ MYSHOP');
    $response->assertSee('LIÊN KẾT NHANH');
    $response->assertSee('Facebook');
    $response->assertSee('Instagram');

    // 2. Visitor subscribes email
    $subResponse = $this->from(route('home'))->post(route('newsletter.subscribe'), [
        'email' => 'journey_subscriber@example.com',
    ]);
    $subResponse->assertRedirect(route('home'));

    $this->assertDatabaseHas('newsletter_subscribers', [
        'email' => 'journey_subscriber@example.com',
    ]);

    // 3. Pre-create an active order to track
    $order = Order::create([
        'order_number' => 'ORD-JOURNEY-2026',
        'customer_name' => 'Pham Minh Duc',
        'phone' => '0977112233',
        'address' => '12 Dong Khoi, Q1, HCMC',
        'subtotal' => 4500000,
        'total_amount' => 4500000,
        'payment_method' => 'cod',
        'status' => 'pending',
    ]);

    $order->items()->create([
        'product_name' => 'Modular Lounge Chair',
        'price_at_purchase' => 4500000,
        'quantity' => 1,
    ]);

    // 4. Visitor accesses order tracking from footer link
    $trackIndex = $this->get(route('track-order.index'));
    $trackIndex->assertOk();

    // 5. Visitor queries order number
    $trackPost = $this->post(route('track-order.track'), [
        'order_number' => 'ORD-JOURNEY-2026',
        'contact_info' => '0977112233',
    ]);
    $trackPost->assertRedirect(route('track-order.index', ['order_number' => 'ORD-JOURNEY-2026', 'contact_info' => '0977112233']));

    // 6. Tracking page reveals full order details and line items
    $trackResult = $this->get(route('track-order.index', ['order_number' => 'ORD-JOURNEY-2026', 'contact_info' => '0977112233']));
    $trackResult->assertOk();
    $trackResult->assertSee('ORD-JOURNEY-2026');
    $trackResult->assertSee('Modular Lounge Chair');
    $trackResult->assertSee('4.500.000₫');
    $trackResult->assertSee('Chờ xác nhận');
});

/*
|--------------------------------------------------------------------------
| SCENARIO 5: Mobile-First User Experience & Quick Checkout Flow
|--------------------------------------------------------------------------
| A mobile shopper interacts with responsive header hamburger drawer,
| browses new arrivals section, adds an item to cart, and completes
| checkout on mobile.
*/

it('executes scenario 5: mobile-first user experience slide drawer navigation and cart checkout', function () {
    $newProduct = Product::create([
        'name' => 'Mobile Minimalist Desk',
        'slug' => 'mobile-minimalist-desk',
        'price' => 2900000,
        'stock' => 10,
        'status' => 'published',
        'is_featured' => false,
    ]);

    // 1. Mobile shopper inspects homepage responsive structures
    $homeResponse = $this->get(route('home'));
    $homeResponse->assertOk();

    // Verify mobile hamburger menu trigger and drawer dialog
    $homeResponse->assertSee('md:hidden', false);
    $homeResponse->assertSee('mobileMenuOpen = true', false);
    $homeResponse->assertSee('role="dialog"', false);
    $homeResponse->assertSee('aria-label="Menu điều hướng"', false);

    // 2. Mobile shopper views New Arrivals section
    $homeResponse->assertSee('Sản Phẩm Mới');
    $homeResponse->assertSee('Mobile Minimalist Desk');

    // 3. Shopper adds product to cart via Livewire
    Livewire::test(AddToCartButton::class, ['product' => $newProduct])
        ->set('quantity', 1)
        ->call('addToCart')
        ->assertDispatched('toast')
        ->assertDispatched('cart-updated')
        ->assertDispatched('open-cart');

    // 4. Header cart count updates to 1
    Livewire::test(CartCount::class)
        ->assertSet('count', 1)
        ->assertSee('1');

    // 5. Cart drawer opens with product details and subtotal
    Livewire::test(CartDrawer::class)
        ->assertSee('Mobile Minimalist Desk')
        ->assertSee('2.900.000₫')
        ->assertSee('Tiến Hành Thanh Toán');

    // 6. Mobile shopper proceeds through checkout
    $checkoutResponse = $this->post(route('checkout.store'), [
        'customer_name' => 'Mobile Shopper Le',
        'phone' => '0938123456',
        'address' => '99 Nguyen Hue, District 1, HCMC',
        'payment_method' => 'cod',
    ]);

    $checkoutResponse->assertSessionHasNoErrors();
    $checkoutResponse->assertRedirect();

    $order = Order::latest('id')->first();
    expect($order->customer_name)->toBe('Mobile Shopper Le')
        ->and((float) $order->total_amount)->toBe(2900000.0);
});
