<?php

use App\Models\LandingPage;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;

beforeEach(function () {
    $this->category = PostCategory::create([
        'name' => 'Interior Design Guides',
        'slug' => 'interior-design-guides',
        'is_active' => true,
    ]);

    $this->product = Product::create([
        'name' => 'Nordic Floor Lamp',
        'slug' => 'nordic-floor-lamp',
        'sku' => 'LMP-CMS-01',
        'price' => 2500000,
        'stock' => 5,
        'status' => 'published',
    ]);

    $this->post = Post::create([
        'title' => 'Complete Guide to Scandinavian Living Rooms',
        'slug' => 'complete-guide-to-scandinavian-living-rooms',
        'excerpt' => 'Discover how to balance natural wood, minimalism, and warm lighting.',
        'body' => "<h2>1. Choose Natural Wood Furniture</h2>\n<p>Light oak and ash wood bring warmth to minimal spaces.</p>\n[product sku=\"LMP-CMS-01\"]\n<h2>2. Minimal Lighting</h2>\n<p>Soft lighting enhances coziness and comfort.</p>",
        'post_category_id' => $this->category->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
        'is_featured' => true,
    ]);

    $this->relatedPost = Post::create([
        'title' => 'Top 5 Lighting Ideas for Modern Apartments',
        'slug' => 'top-5-lighting-ideas-for-modern-apartments',
        'body' => '<h2>Lighting Concepts</h2><p>Pendant and floor lamps.</p>',
        'post_category_id' => $this->category->id,
        'status' => 'published',
        'published_at' => now()->subHours(2),
    ]);
});

it('searches published blog posts by keyword and resolves reading time', function () {
    expect($this->post->reading_time_minutes)->toBeGreaterThanOrEqual(1);

    $response = $this->get(route('blog.index', ['search' => 'Scandinavian']));
    $response->assertStatus(200);
    $response->assertSee('Complete Guide to Scandinavian Living Rooms');

    $responseNoMatch = $this->get(route('blog.index', ['search' => 'NonExistentKeywordXYZ']));
    $responseNoMatch->assertStatus(200);
    $responseNoMatch->assertDontSee('Complete Guide to Scandinavian Living Rooms');
});

it('renders article show with toc, contextual commerce embed and related posts', function () {
    $response = $this->get(route('blog.show', $this->post->slug));

    $response->assertStatus(200);
    $response->assertSee('Complete Guide to Scandinavian Living Rooms');
    $response->assertSee('Nordic Floor Lamp');
    $response->assertSee('2.500.000₫');
    $response->assertSee('Top 5 Lighting Ideas for Modern Apartments');
    $response->assertSee('https://schema.org');
});

it('renders static CMS page and falls back to active landing page', function () {
    $page = Page::create([
        'title' => 'Warranty and Return Policy',
        'slug' => 'warranty-and-return-policy',
        'body' => '<h2>30-Day Free Return</h2><p>We provide full manufacturer warranty for 24 months.</p>',
        'is_published' => true,
        'template' => 'policy',
    ]);

    $response = $this->get(route('page.show', $page->slug));
    $response->assertStatus(200);
    $response->assertSee('Warranty and Return Policy');
    $response->assertSee('30-Day Free Return');

    // Create a landing page
    $landingPage = LandingPage::create([
        'title' => 'Exclusive Summer Promo',
        'slug' => 'summer-promo-2026',
        'is_active' => true,
    ]);

    $responseLanding = $this->get(route('page.show', $landingPage->slug));
    $responseLanding->assertStatus(200);
    $responseLanding->assertSee('Exclusive Summer Promo');
});
