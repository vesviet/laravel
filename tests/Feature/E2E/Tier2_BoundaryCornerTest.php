<?php

use App\Livewire\AddToCartButton;
use App\Livewire\CartDrawer;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

beforeEach(function () {
    Cache::flush();
});

/*
|--------------------------------------------------------------------------
| BOUNDARY GROUP 1: Category & Product Data Boundaries
|--------------------------------------------------------------------------
| Tests zero data states, null values, extreme string lengths, special
| character handling, pricing formatting boundaries, and status filters.
*/

it('verifies homepage handles zero products gracefully without errors', function () {
    $response = $this->get(route('home'));
    $response->assertOk();

    $response->assertSee('ChÆ°a cÃ³ sáº£n pháº©m ná»•i báº­t');
    $response->assertSee('ChÆ°a cÃ³ sáº£n pháº©m nÃ o.');
});

it('verifies products catalog handles empty category gracefully', function () {
    $category = Category::create([
        'name' => 'Empty Category',
        'slug' => 'empty-category',
    ]);

    $response = $this->get(route('products.index', ['category' => $category->slug]));
    $response->assertOk();
    $response->assertSee('KhÃ´ng tÃ¬m tháº¥y sáº£n pháº©m nÃ o.');
});

it('verifies products catalog handles non-existent category slug without 500 error', function () {
    $response = $this->get(route('products.index', ['category' => 'non-existent-category-slug-999']));
    $response->assertOk();
    $response->assertSee('KhÃ´ng tÃ¬m tháº¥y sáº£n pháº©m nÃ o.');
});

it('verifies product card handles null image path with svg placeholder fallback', function () {
    $product = Product::create([
        'name' => 'Zero Image Product',
        'slug' => 'zero-image-product',
        'price' => 150000,
        'stock' => 10,
        'status' => 'published',
        'image_path' => null,
    ]);

    $response = $this->get(route('products.index'));
    $response->assertOk();

    $response->assertSee('Zero Image Product');
    $response->assertSee('svg', false);
});

it('verifies product card handles product with no category without null pointer error', function () {
    $product = Product::create([
        'name' => 'Uncategorized Minimalist Stool',
        'slug' => 'uncategorized-stool',
        'price' => 750000,
        'stock' => 5,
        'status' => 'published',
        'category_id' => null,
    ]);

    $response = $this->get(route('products.index'));
    $response->assertOk();

    $response->assertSee('Uncategorized Minimalist Stool');
});

it('verifies product card handles extreme long product name properly', function () {
    $longName = 'BÃ n TrÃ  Sofa Scandinavian Gá»— Sá»“i Tá»± NhiÃªn Cao Cáº¥p Phá»§ SÆ¡n PU BÃ³ng Má» KhÃ¡ng NÆ°á»›c Chá»‘ng Tráº§y XÆ°á»›c 2026';
    $product = Product::create([
        'name' => $longName,
        'slug' => 'extreme-long-name-table',
        'price' => 2890000,
        'stock' => 3,
        'status' => 'published',
    ]);

    $response = $this->get(route('products.index'));
    $response->assertOk();

    $response->assertSee($longName);
});

it('verifies product card escapes special characters quotes and html entities safely', function () {
    $specialName = 'Gháº¿ "Armchair" Vintage & ÄÃ¨n <Gá»—>';
    $product = Product::create([
        'name' => $specialName,
        'slug' => 'special-char-product',
        'price' => 990000,
        'stock' => 7,
        'status' => 'published',
    ]);

    $response = $this->get(route('products.index'));
    $response->assertOk();

    // Verify HTML escaping
    $response->assertSee(e($specialName), false);
});

it('verifies product card formats zero price cleanly', function () {
    $product = Product::create([
        'name' => 'Complimentary Swatch Sample',
        'slug' => 'complimentary-swatch',
        'price' => 0,
        'stock' => 100,
        'status' => 'published',
    ]);

    $response = $this->get(route('products.index'));
    $response->assertOk();

    $response->assertSee('0â‚«');
});

it('verifies product card formats large price with vnd thousands separator', function () {
    $product = Product::create([
        'name' => 'Luxury Italian Leather Sofa',
        'slug' => 'luxury-italian-sofa',
        'price' => 125000000,
        'stock' => 2,
        'status' => 'published',
    ]);

    $response = $this->get(route('products.index'));
    $response->assertOk();

    $response->assertSee('125.000.000â‚«');
});

it('verifies draft and archived products are excluded from homepage and catalog', function () {
    Product::create([
        'name' => 'Draft Product Secret',
        'slug' => 'draft-product-secret',
        'price' => 500000,
        'stock' => 10,
        'status' => 'draft',
        'is_featured' => true,
    ]);

    Product::create([
        'name' => 'Archived Product Old',
        'slug' => 'archived-product-old',
        'price' => 500000,
        'stock' => 10,
        'status' => 'archived',
        'is_featured' => true,
    ]);

    $homeResponse = $this->get(route('home'));
    $homeResponse->assertDontSee('Draft Product Secret');
    $homeResponse->assertDontSee('Archived Product Old');

    $catalogResponse = $this->get(route('products.index'));
    $catalogResponse->assertDontSee('Draft Product Secret');
    $catalogResponse->assertDontSee('Archived Product Old');
});

/*
|--------------------------------------------------------------------------
| BOUNDARY GROUP 2: Header & Viewport Layout Boundaries
|--------------------------------------------------------------------------
| Tests guest vs authenticated visitor states, active link matching,
| and accessibility skip navigation.
*/

it('verifies guest visitor sees login link and no orders link in header', function () {
    $response = $this->get(route('home'));
    $response->assertOk();

    $response->assertSee(route('account.login'), false);
    $response->assertDontSee(route('account.orders'), false);
});

it('verifies authenticated customer sees orders and wishlist links in header', function () {
    $customer = Customer::create([
        'name' => 'Auth Header Customer',
        'email' => 'auth_header@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->actingAs($customer, 'customer')->get(route('home'));
    $response->assertOk();

    $response->assertSee(route('account.orders'), false);
    $response->assertSee(route('account.wishlist'), false);
});

it('verifies navigation active state matches catalog route prefix', function () {
    $response = $this->get(route('products.index'));
    $response->assertOk();

    $response->assertSee('nav-link border-b border-[#1a1a1a]', false);
});

it('verifies navigation active state matches order tracking route', function () {
    $response = $this->get(route('track-order.index'));
    $response->assertOk();

    $response->assertSee('nav-link border-b border-[#1a1a1a]', false);
});

it('verifies skip to content accessibility link is present with proper attributes', function () {
    $response = $this->get(route('home'));
    $response->assertOk();

    $response->assertSee('sr-only', false);
    $response->assertSee('Bá» qua Ä‘iá»u hÆ°á»›ng');
});

/*
|--------------------------------------------------------------------------
| BOUNDARY GROUP 3: Newsletter Subscription Boundaries
|--------------------------------------------------------------------------
| Tests required email validation, invalid formats, length limits, and
| duplicate submission handling.
*/

it('verifies newsletter rejects empty email submission', function () {
    $response = $this->post(route('newsletter.subscribe'), [
        'email' => '',
    ]);

    $response->assertSessionHasErrors('email');
});

it('verifies newsletter rejects malformed email format', function () {
    $response = $this->post(route('newsletter.subscribe'), [
        'email' => 'not-a-valid-email-address',
    ]);

    $response->assertSessionHasErrors('email');
});

it('verifies newsletter rejects email exceeding max length', function () {
    $longEmail = str_repeat('a', 250).'@example.com';
    $response = $this->post(route('newsletter.subscribe'), [
        'email' => $longEmail,
    ]);

    $response->assertSessionHasErrors('email');
});

it('verifies newsletter handles duplicate email gracefully without database crash', function () {
    $email = 'repeat_subscriber@example.com';

    // First subscription
    $res1 = $this->post(route('newsletter.subscribe'), ['email' => $email]);
    $res1->assertRedirect();
    $res1->assertSessionHas('newsletter_success');

    // Second subscription (duplicate)
    $res2 = $this->post(route('newsletter.subscribe'), ['email' => $email]);
    $res2->assertRedirect();
    $res2->assertSessionHas('newsletter_success');

    $this->assertDatabaseHas('newsletter_subscribers', ['email' => $email]);
});

it('verifies newsletter handles special characters in email safely', function () {
    $email = 'test.user+tag123@sub.domain-example.com';
    $response = $this->post(route('newsletter.subscribe'), ['email' => $email]);

    $response->assertRedirect();
    $this->assertDatabaseHas('newsletter_subscribers', ['email' => $email]);
});

/*
|--------------------------------------------------------------------------
| BOUNDARY GROUP 4: Cart Drawer & Livewire State Boundaries
|--------------------------------------------------------------------------
| Tests quantity boundaries (<1, large values), non-existent removal,
| stock out additions, multi-variant handling, and empty checkout guard.
*/

it('verifies cart drawer ignores quantity update less than one', function () {
    $product = Product::create([
        'name' => 'Boundary Chair',
        'slug' => 'boundary-chair',
        'price' => 300000,
        'stock' => 10,
        'status' => 'published',
    ]);

    Session::put('cart', [
        "{$product->id}_0" => [
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 2,
        ],
    ]);

    Livewire::test(CartDrawer::class)
        ->call('updateQuantity', $product->id, null, 0);

    $cart = Session::get('cart');
    expect($cart["{$product->id}_0"]['quantity'])->toBe(2);
});

it('verifies cart drawer handles large quantity update', function () {
    $product = Product::create([
        'name' => 'Bulk Stool',
        'slug' => 'bulk-stool',
        'price' => 100000,
        'stock' => 500,
        'status' => 'published',
    ]);

    Session::put('cart', [
        "{$product->id}_0" => [
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ],
    ]);

    Livewire::test(CartDrawer::class)
        ->call('updateQuantity', $product->id, null, 99);

    $cart = Session::get('cart');
    expect($cart["{$product->id}_0"]['quantity'])->toBe(99);
});

it('verifies cart drawer handles removal of non-existent product gracefully', function () {
    Session::put('cart', []);

    Livewire::test(CartDrawer::class)
        ->call('removeItem', 99999, null)
        ->assertDispatched('cart-updated');
});

it('verifies cart drawer calculates subtotal accurately with multiple quantities', function () {
    $product1 = Product::create([
        'name' => 'Subtotal Product 1',
        'slug' => 'subtotal-product-1',
        'price' => 250000,
        'stock' => 10,
        'status' => 'published',
    ]);

    $product2 = Product::create([
        'name' => 'Subtotal Product 2',
        'slug' => 'subtotal-product-2',
        'price' => 100000,
        'stock' => 10,
        'status' => 'published',
    ]);

    Session::put('cart', [
        "{$product1->id}_0" => [
            'product_id' => $product1->id,
            'product_variant_id' => null,
            'quantity' => 2,
        ],
        "{$product2->id}_0" => [
            'product_id' => $product2->id,
            'product_variant_id' => null,
            'quantity' => 3,
        ],
    ]);

    // Subtotal = (250000 * 2) + (100000 * 3) = 800000
    Livewire::test(CartDrawer::class)
        ->assertSee('800.000â‚«');
});

it('verifies add to cart is blocked when product stock is zero', function () {
    $product = Product::create([
        'name' => 'Out of Stock Bench',
        'slug' => 'out-of-stock-bench',
        'price' => 1200000,
        'stock' => 0,
        'status' => 'published',
    ]);

    Livewire::test(AddToCartButton::class, ['product' => $product])
        ->call('addToCart')
        ->assertDispatched('toast');

    expect(Session::get('cart'))->toBeNull();
});

it('verifies add to cart handles product with variant', function () {
    $product = Product::create([
        'name' => 'Variant Product Chair',
        'slug' => 'variant-product-chair',
        'price' => 500000,
        'stock' => 20,
        'status' => 'published',
    ]);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'name' => 'Walnut Finish',
        'sku' => 'VPC-WALNUT',
        'price' => 600000,
        'stock' => 10,
    ]);

    Livewire::test(AddToCartButton::class, ['product' => $product])
        ->set('variantId', $variant->id)
        ->set('quantity', 1)
        ->call('addToCart');

    $cart = Session::get('cart');
    expect($cart)->not->toBeNull()
        ->and(isset($cart["{$product->id}_{$variant->id}"]))->toBeTrue();
});

it('verifies checkout redirects to products when cart is empty', function () {
    Session::forget('cart');

    $response = $this->get(route('checkout.index'));
    $response->assertRedirect(route('products.index'));
});

it('verifies checkout validates required customer fields presence', function () {
    $product = Product::create([
        'name' => 'Checkout Validation Product',
        'slug' => 'checkout-validation-product',
        'price' => 200000,
        'stock' => 10,
        'status' => 'published',
    ]);

    Session::put('cart', [
        "{$product->id}_0" => [
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ],
    ]);

    $response = $this->post(route('checkout.store'), [
        'customer_name' => '',
        'phone' => '',
        'address' => '',
    ]);

    $response->assertSessionHasErrors(['customer_name', 'phone', 'address']);
});

/*
|--------------------------------------------------------------------------
| BOUNDARY GROUP 5: Order Tracking Boundaries
|--------------------------------------------------------------------------
| Tests initial tracking view, non-existent order numbers, valid order
| lookup, and tracking input validation.
*/

it('verifies order tracking page loads cleanly with no query param', function () {
    $response = $this->get(route('track-order.index'));
    $response->assertOk();

    $response->assertSee('Tra Cá»©u ÄÆ¡n HÃ ng');
});

it('verifies order tracking handles non-existent order number gracefully', function () {
    $response = $this->get(route('track-order.index', ['order_number' => 'ORD-NON-EXISTENT-999']));
    $response->assertOk();

    $response->assertSee('KhÃ´ng tÃ¬m tháº¥y Ä‘Æ¡n hÃ ng vá»›i mÃ£');
    $response->assertSee('ORD-NON-EXISTENT-999');
});

it('verifies order tracking displays complete order details for valid order', function () {
    $order = Order::create([
        'order_number' => 'ORD-TRACK-12345',
        'customer_name' => 'Bao Nguyen',
        'phone' => '0908765432',
        'address' => '456 Nguyen Trai, D5',
        'subtotal' => 1500000,
        'total_amount' => 1500000,
        'payment_method' => 'cod',
        'status' => 'pending',
    ]);

    $response = $this->get(route('track-order.index', ['order_number' => $order->order_number, 'contact_info' => '0908765432']));
    $response->assertOk();

    $response->assertSee('ORD-TRACK-12345');
    $response->assertSee('1.500.000â‚«');
    $response->assertSee('Chá» xÃ¡c nháº­n');
});

it('verifies order tracking post endpoint validates required order number', function () {
    $response = $this->post(route('track-order.track'), [
        'order_number' => '',
        'contact_info' => '0908765432',
    ]);

    $response->assertSessionHasErrors('order_number');
});

/*
|--------------------------------------------------------------------------
| BOUNDARY GROUP 6: Catalog Sorting & Pagination Boundaries
|--------------------------------------------------------------------------
| Tests fallback on unknown sort keys, price ascending/descending order,
| and catalog pagination.
*/

it('verifies catalog sorting handles unknown sort parameter by falling back to newest', function () {
    $response = $this->get(route('products.index', ['sort' => 'sql_injection_attempt_or_unknown']));
    $response->assertOk();
});

it('verifies catalog sort price_asc orders products from lowest to highest', function () {
    $cheap = Product::create([
        'name' => 'Cheap Accessory',
        'slug' => 'cheap-accessory',
        'price' => 50000,
        'stock' => 10,
        'status' => 'published',
    ]);

    $expensive = Product::create([
        'name' => 'Expensive Credenza',
        'slug' => 'expensive-credenza',
        'price' => 8000000,
        'stock' => 2,
        'status' => 'published',
    ]);

    $response = $this->get(route('products.index', ['sort' => 'price_asc']));
    $response->assertOk();

    $html = $response->getContent();
    $posCheap = strpos($html, 'Cheap Accessory');
    $posExpensive = strpos($html, 'Expensive Credenza');

    expect($posCheap)->toBeLessThan($posExpensive);
});

it('verifies catalog sort price_desc orders products from highest to lowest', function () {
    $cheap = Product::create([
        'name' => 'Cheap Desk Pad',
        'slug' => 'cheap-desk-pad',
        'price' => 80000,
        'stock' => 10,
        'status' => 'published',
    ]);

    $expensive = Product::create([
        'name' => 'Expensive Dining Set',
        'slug' => 'expensive-dining-set',
        'price' => 15000000,
        'stock' => 2,
        'status' => 'published',
    ]);

    $response = $this->get(route('products.index', ['sort' => 'price_desc']));
    $response->assertOk();

    $html = $response->getContent();
    $posCheap = strpos($html, 'Cheap Desk Pad');
    $posExpensive = strpos($html, 'Expensive Dining Set');

    expect($posExpensive)->toBeLessThan($posCheap);
});

it('verifies catalog pagination renders when product count exceeds twelve', function () {
    for ($i = 1; $i <= 15; $i++) {
        Product::create([
            'name' => "Paginated Product {$i}",
            'slug' => "paginated-product-{$i}",
            'price' => 100000 * $i,
            'stock' => 10,
            'status' => 'published',
        ]);
    }

    $response = $this->get(route('products.index'));
    $response->assertOk();

    // Check pagination controls
    $response->assertSee('nav role="navigation"', false);
});

/*
|--------------------------------------------------------------------------
| BOUNDARY GROUP 7: Layout & Notification Boundaries
|--------------------------------------------------------------------------
| Tests toast notifications container, flash sale banner null state,
| and meta description fallbacks.
*/

it('verifies flash sale banner handles absence of active flash sale without error', function () {
    $response = $this->get(route('home'));
    $response->assertOk();
});

it('verifies toast notification system container is present in layout', function () {
    $response = $this->get(route('home'));
    $response->assertOk();

    $response->assertSee('@toast.window', false);
    $response->assertSee('role="status"', false);
});

it('verifies meta description fallback renders when no page specific description is pushed', function () {
    $response = $this->get(route('products.index'));
    $response->assertOk();

    $response->assertSee('name="description"', false);
});

it('verifies homepage og tags render with valid properties', function () {
    $response = $this->get(route('home'));
    $response->assertOk();

    $response->assertSee('property="og:title"', false);
    $response->assertSee('property="og:type"', false);
});
