<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Services\ShortcodeService;

beforeEach(function () {
    $this->category = Category::create([
        'name' => 'Living Room',
        'slug' => 'living-room',
        'is_active' => true,
    ]);

    $this->product = Product::create([
        'name' => 'Nordic Floor Lamp',
        'slug' => 'nordic-floor-lamp',
        'sku' => 'LMP-001',
        'price' => 2500000,
        'stock' => 8,
        'category_id' => $this->category->id,
        'status' => 'published',
    ]);
});

it('parses [product id=...] and replaces with interactive embed card', function () {
    $content = "<p>Here is an amazing lamp for your room:</p>\n[product id={$this->product->id}]\n<p>Enjoy reading!</p>";

    $service = app(ShortcodeService::class);
    $parsed = $service->parse($content);

    expect($parsed)->toContain('Nordic Floor Lamp');
    expect($parsed)->toContain(route('products.show', $this->product->slug));
    expect($parsed)->toContain('2.500.000₫');
    expect($parsed)->not->toContain('[product id=');
});

it('parses [product sku=...] format correctly', function () {
    $content = "<p>Decorate your bedroom:</p>\n[product sku=\"LMP-001\"]\n<p>Great ambiance!</p>";

    $service = app(ShortcodeService::class);
    $parsed = $service->parse($content);

    expect($parsed)->toContain('Nordic Floor Lamp');
    expect($parsed)->not->toContain('[product sku=');
});

it('gracefully removes shortcode if product does not exist or is inactive', function () {
    $content = "<p>Check out our special piece:</p>\n[product id=99999]\n<p>End of article.</p>";

    $service = app(ShortcodeService::class);
    $parsed = $service->parse($content);

    expect($parsed)->not->toContain('[product id=99999]');
    expect($parsed)->toContain('Check out our special piece');
    expect($parsed)->toContain('End of article');
});

it('renders embed card inside blog article show view', function () {
    $post = Post::create([
        'title' => 'How to Style Your Living Space',
        'slug' => 'how-to-style-your-living-space',
        'body' => "<h2>Interior Lighting</h2>\n<p>Use modern lamps:</p>\n[product sku=\"LMP-001\"]\n<p>Conclusion.</p>",
        'status' => 'published',
        'published_at' => now()->subHour(),
    ]);

    $response = $this->get(route('blog.show', $post->slug));

    $response->assertStatus(200);
    $response->assertSee('Nordic Floor Lamp');
    $response->assertSee('2.500.000₫');
    $response->assertDontSee('[product sku="LMP-001"]');
});
