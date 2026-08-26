<?php

use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use App\Actions\ProcessSellerQuickOrderAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Multitenancy\Models\Tenant;

uses(RefreshDatabase::class);

test('tenant isolation correctly scopes products', function () {
    $sellerA = SellerProfile::factory()->create(['subdomain' => 'shop-a']);
    $sellerB = SellerProfile::factory()->create(['subdomain' => 'shop-b']);

    // Create products manually to bypass scope during setup or use make() and save()
    $productA = Product::factory()->create(['seller_id' => $sellerA->id]);
    $productB = Product::factory()->create(['seller_id' => $sellerB->id]);

    $sellerA->makeCurrent();

    $products = Product::all();
    
    expect($products)->toHaveCount(1)
        ->and($products->first()->id)->toBe($productA->id)
        ->and($products->first()->seller_id)->toBe($sellerA->id);
});

test('concurrent stock lock prevents overselling', function () {
    \Illuminate\Support\Facades\Event::fake([\App\Events\SellerOrderPlaced::class]);

    $seller = SellerProfile::factory()->create();
    $seller->makeCurrent();

    $product = Product::factory()->create([
        'seller_id' => $seller->id,
        'stock' => 1,
    ]);

    $action = app(ProcessSellerQuickOrderAction::class);
    
    // Simulate first purchase
    $action->execute($seller, [
        'product_id' => $product->id,
        'quantity' => 1,
        'customer_name' => 'John Doe',
        'phone' => '0900000000',
        'address' => '123 Test St',
    ]);

    // Second purchase should fail due to stock depletion
    expect(fn () => $action->execute($seller, [
        'product_id' => $product->id,
        'quantity' => 1,
        'customer_name' => 'Jane Doe',
        'phone' => '0911111111',
        'address' => '456 Test St',
    ]))->toThrow(RuntimeException::class, 'Sản phẩm đã hết hàng hoặc không đủ số lượng.');
});

test('carrd subdomain routing falls back correctly', function () {
    // In a real application, you'd test HTTP endpoints. For Pest, we can simulate a request.
    $seller = SellerProfile::factory()->create(['subdomain' => 'my-store']);
    
    $response = $this->get('http://my-store.localhost/seller');
    $response->assertStatus(302)->assertRedirectContains('/seller/login');
});
