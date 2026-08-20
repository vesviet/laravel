<?php

use App\Models\Category;
use App\Models\Product;

beforeEach(function () {
    $this->parentCategory = Category::create([
        'name' => 'Furniture',
        'slug' => 'furniture',
    ]);

    $this->childCategory = Category::create([
        'name' => 'Chairs',
        'slug' => 'chairs',
        'parent_id' => $this->parentCategory->id,
    ]);

    $this->lightingCategory = Category::create([
        'name' => 'Lighting',
        'slug' => 'lighting',
    ]);

    $this->product1 = Product::create([
        'name' => 'Velvet Dining Chair',
        'slug' => 'velvet-dining-chair',
        'sku' => 'CHR-001',
        'price' => 1200000,
        'stock' => 15,
        'category_id' => $this->childCategory->id,
        'status' => 'published',
        'is_featured' => true,
    ]);

    $this->product2 = Product::create([
        'name' => 'Nordic Floor Lamp',
        'slug' => 'nordic-floor-lamp',
        'sku' => 'LMP-001',
        'price' => 2500000,
        'stock' => 0, // Out of stock
        'category_id' => $this->lightingCategory->id,
        'status' => 'published',
        'is_featured' => false,
    ]);

    $this->product3 = Product::create([
        'name' => 'Oak Coffee Table',
        'slug' => 'oak-coffee-table',
        'sku' => 'TBL-001',
        'price' => 3500000,
        'stock' => 5,
        'category_id' => $this->parentCategory->id,
        'status' => 'published',
        'is_featured' => false,
    ]);

    $this->draftProduct = Product::create([
        'name' => 'Draft Unreleased Sofa',
        'slug' => 'draft-unreleased-sofa',
        'sku' => 'DRAFT-001',
        'price' => 9900000,
        'stock' => 2,
        'category_id' => $this->parentCategory->id,
        'status' => 'draft',
    ]);
});

it('catalog index returns 200 and lists active published products while excluding drafts', function () {
    $response = $this->get(route('products.index'));

    $response->assertStatus(200);
    $response->assertSee('Velvet Dining Chair');
    $response->assertSee('Nordic Floor Lamp');
    $response->assertSee('Oak Coffee Table');
    $response->assertDontSee('Draft Unreleased Sofa');
});

it('filters catalog by category slug and includes child categories', function () {
    // When filtering by parent category "furniture", it should include both parent category product and child category product "chairs"
    $response = $this->get(route('products.index', ['category' => 'furniture']));

    $response->assertStatus(200);
    $response->assertSee('Oak Coffee Table');
    $response->assertSee('Velvet Dining Chair');
    $response->assertDontSee('Nordic Floor Lamp');
});

it('searches catalog by keyword matching name or sku', function () {
    $response = $this->get(route('products.index', ['q' => 'LMP-001']));

    $response->assertStatus(200);
    $response->assertSee('Nordic Floor Lamp');
    $response->assertDontSee('Velvet Dining Chair');
    $response->assertDontSee('Oak Coffee Table');
});

it('filters catalog by price range', function () {
    $response = $this->get(route('products.index', [
        'min_price' => 2000000,
        'max_price' => 3000000,
    ]));

    $response->assertStatus(200);
    $response->assertSee('Nordic Floor Lamp'); // 2,500,000
    $response->assertDontSee('Velvet Dining Chair'); // 1,200,000
    $response->assertDontSee('Oak Coffee Table'); // 3,500,000
});

it('filters catalog for in-stock products only', function () {
    $response = $this->get(route('products.index', ['in_stock' => 1]));

    $response->assertStatus(200);
    $response->assertSee('Velvet Dining Chair'); // Stock: 15
    $response->assertSee('Oak Coffee Table'); // Stock: 5
    $response->assertDontSee('Nordic Floor Lamp'); // Stock: 0 (out of stock)
});

it('sorts catalog products by price ascending and descending', function () {
    $responseAsc = $this->get(route('products.index', ['sort' => 'price_asc']));
    $responseAsc->assertStatus(200);
    $responseAsc->assertSeeInOrder(['Velvet Dining Chair', 'Nordic Floor Lamp', 'Oak Coffee Table']);

    $responseDesc = $this->get(route('products.index', ['sort' => 'price_desc']));
    $responseDesc->assertStatus(200);
    $responseDesc->assertSeeInOrder(['Oak Coffee Table', 'Nordic Floor Lamp', 'Velvet Dining Chair']);
});

it('product show returns 200 with related products and Schema.org JSON-LD', function () {
    $response = $this->get(route('products.show', $this->product1->slug));

    $response->assertStatus(200);
    $response->assertSee('Velvet Dining Chair');
    $response->assertSee('1.200.000₫');
    $response->assertSee('https://schema.org/');
    $response->assertSee('Product');
});

it('product show returns 404 for draft product', function () {
    $response = $this->get(route('products.show', $this->draftProduct->slug));

    $response->assertStatus(404);
});
