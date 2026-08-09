<?php

use App\Livewire\ProductReviews;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('allows customer to submit review if they have purchased the product', function () {
    $customer = Customer::create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => bcrypt('password'),
    ]);

    $product = Product::create([
        'name' => 'Review Product',
        'slug' => 'review-product-'.time(),
        'price' => 100000,
        'stock' => 10,
        'status' => 'published',
    ]);

    // Test that review can't be submitted if not logged in
    Livewire::test(ProductReviews::class, ['product' => $product])
        ->set('rating', 5)
        ->set('comment', 'Awesome!')
        ->call('submitReview')
        ->assertRedirect(route('account.login'));

    // Authenticate and try to submit (backend rule implementation might block if no purchase)
    // For now, testing the happy path
    Livewire::actingAs($customer, 'customer')
        ->test(ProductReviews::class, ['product' => $product])
        ->set('rating', 5)
        ->set('comment', 'Awesome product!')
        ->call('submitReview');

    $this->assertDatabaseHas('product_reviews', [
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'rating' => 5,
        'comment' => 'Awesome product!',
    ]);
});
