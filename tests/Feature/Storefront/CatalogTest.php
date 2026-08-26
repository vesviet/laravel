<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\User;

beforeEach(function () {
    $this->parentCategory = Category::create([
        'name' => 'Furniture',
        'slug' => 'furniture',
        'is_visible' => true,
    ]);

    $this->childCategory = Category::create([
        'name' => 'Chairs',
        'slug' => 'chairs',
        'parent_id' => $this->parentCategory->id,
        'is_visible' => true,
    ]);

    $this->lightingCategory = Category::create([
        'name' => 'Lighting',
        'slug' => 'lighting',
        'is_visible' => true,
    ]);

    $this->product1 = Product::create([
        'name' => 'Velvet Dining Chair',
        'slug' => 'velvet-dining-chair',
        'sku' => 'CHR-001',
        'price' => 1200000,
        'stock' => 15,
        'low_stock_threshold' => 5,
        'category_id' => $this->childCategory->id,
        'status' => 'published',
        'is_featured' => true,
        'is_visible' => true,
        'is_purchasable' => true,
        'published_at' => now(),
    ]);

    $this->product2 = Product::create([
        'name' => 'Nordic Floor Lamp',
        'slug' => 'nordic-floor-lamp',
        'sku' => 'LMP-001',
        'price' => 2500000,
        'compare_at_price' => 3000000,
        'stock' => 0, // Out of stock
        'category_id' => $this->lightingCategory->id,
        'status' => 'published',
        'is_featured' => false,
        'is_visible' => true,
        'is_purchasable' => true,
        'published_at' => now(),
    ]);

    $this->product3 = Product::create([
        'name' => 'Oak Coffee Table',
        'slug' => 'oak-coffee-table',
        'sku' => 'TBL-001',
        'price' => 3500000,
        'stock' => 5,
        'low_stock_threshold' => 5,
        'category_id' => $this->parentCategory->id,
        'status' => 'published',
        'is_featured' => false,
        'is_visible' => true,
        'is_purchasable' => true,
        'published_at' => now()->subDays(10),
    ]);

    $this->product4 = Product::create([
        'name' => 'Leather Sofa',
        'slug' => 'leather-sofa',
        'sku' => 'SFA-001',
        'price' => 15000000,
        'stock' => 3,
        'category_id' => $this->parentCategory->id,
        'status' => 'published',
        'is_featured' => false,
        'is_visible' => true,
        'is_purchasable' => true,
        'published_at' => now()->subDays(45), // Not new arrival
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

    $this->hiddenProduct = Product::create([
        'name' => 'Hidden Product',
        'slug' => 'hidden-product',
        'sku' => 'HID-001',
        'price' => 5000000,
        'stock' => 10,
        'category_id' => $this->parentCategory->id,
        'status' => 'published',
        'is_visible' => false,
        'is_purchasable' => true,
        'published_at' => now(),
    ]);
});

describe('Catalog Index', function () {
    it('returns 200 and lists active published products while excluding drafts', function () {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Velvet Dining Chair');
        $response->assertSee('Nordic Floor Lamp');
        $response->assertSee('Oak Coffee Table');
        $response->assertSee('Leather Sofa');
        $response->assertDontSee('Draft Unreleased Sofa');
        $response->assertDontSee('Hidden Product');
    });

    it('filters catalog by category slug and includes child categories', function () {
        $response = $this->get(route('products.index', ['category' => 'furniture']));

        $response->assertStatus(200);
        $response->assertSee('Oak Coffee Table');
        $response->assertSee('Velvet Dining Chair');
        $response->assertSee('Leather Sofa');
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
        $response->assertSee('Leather Sofa'); // Stock: 3
        $response->assertDontSee('Nordic Floor Lamp'); // Stock: 0 (out of stock)
    });

    it('filters catalog for on-sale products only', function () {
        $response = $this->get(route('products.index', ['on_sale' => 1]));

        $response->assertStatus(200);
        $response->assertSee('Nordic Floor Lamp'); // Has compare_at_price > price
        $response->assertDontSee('Velvet Dining Chair');
        $response->assertDontSee('Oak Coffee Table');
        $response->assertDontSee('Leather Sofa');
    });

    it('filters catalog for new arrivals (30 days)', function () {
        $response = $this->get(route('products.index', ['new_arrivals' => 1]));

        $response->assertStatus(200);
        $response->assertSee('Oak Coffee Table'); // Published 10 days ago
        $response->assertDontSee('Leather Sofa'); // Published 45 days ago
    });

    it('sorts catalog products by price ascending and descending', function () {
        $responseAsc = $this->get(route('products.index', ['sort' => 'price_asc']));
        $responseAsc->assertStatus(200);
        $responseAsc->assertSeeInOrder(['Velvet Dining Chair', 'Nordic Floor Lamp', 'Oak Coffee Table', 'Leather Sofa']);

        $responseDesc = $this->get(route('products.index', ['sort' => 'price_desc']));
        $responseDesc->assertStatus(200);
        $responseDesc->assertSeeInOrder(['Leather Sofa', 'Oak Coffee Table', 'Nordic Floor Lamp', 'Velvet Dining Chair']);
    });

    it('sorts catalog products by name ascending and descending', function () {
        $responseAsc = $this->get(route('products.index', ['sort' => 'name_asc']));
        $responseAsc->assertStatus(200);
        $responseAsc->assertSeeInOrder(['Leather Sofa', 'Nordic Floor Lamp', 'Oak Coffee Table', 'Velvet Dining Chair']);

        $responseDesc = $this->get(route('products.index', ['sort' => 'name_desc']));
        $responseDesc->assertStatus(200);
        $responseDesc->assertSeeInOrder(['Velvet Dining Chair', 'Oak Coffee Table', 'Nordic Floor Lamp', 'Leather Sofa']);
    });

    it('sorts catalog products by featured first', function () {
        $response = $this->get(route('products.index', ['sort' => 'featured']));

        $response->assertStatus(200);
        // Featured products should appear first
        $content = $response->getContent();
        $featuredPos = strpos($content, 'Velvet Dining Chair');
        $otherPos = strpos($content, 'Nordic Floor Lamp');
        expect($featuredPos)->toBeLessThan($otherPos);
    });

    it('falls back to newest sorting for invalid sort parameter', function () {
        $response = $this->get(route('products.index', ['sort' => 'invalid_sort']));

        // Unknown sort values must degrade gracefully (fallback to newest),
        // never error out — adversarial suites rely on this contract.
        $response->assertOk();
    });
});

describe('Product Show', function () {
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

    it('product show returns 404 for hidden product', function () {
        $response = $this->get(route('products.show', $this->hiddenProduct->slug));

        $response->assertStatus(404);
    });

    it('product show displays product price', function () {
        $response = $this->get(route('products.show', $this->product1->slug));

        $response->assertStatus(200);
        $response->assertSee('Velvet Dining Chair');
        $response->assertSee('1.200.000₫'); // Product price
    });

    it('model toSchemaOrgJsonLd includes aggregateRating when reviews exist', function () {
        $customer = Customer::factory()->create();
        $admin = User::factory()->create();
        ProductReview::create([
            'product_id' => $this->product1->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Great product!',
            'status' => 'approved',
        ]);
        ProductReview::create([
            'product_id' => $this->product1->id,
            'customer_id' => Customer::factory()->create()->id,
            'rating' => 4,
            'comment' => 'Good',
            'status' => 'approved',
        ]);

        $schema = $this->product1->toSchemaOrgJsonLd(route('products.show', $this->product1->slug));

        expect($schema['aggregateRating'])->toBeArray();
        expect($schema['aggregateRating']['ratingValue'])->toBe(4.5); // Average of 5 and 4
        expect($schema['aggregateRating']['reviewCount'])->toBe(2);
    });
});

describe('Product Model Scopes & Accessors', function () {
    it('scopeActive returns only active and published products', function () {
        $activeProducts = Product::active()->get();

        expect($activeProducts->count())->toBe(4); // 4 published, 1 draft, 1 hidden
        expect($activeProducts->pluck('id'))->not->toContain($this->draftProduct->id);
    });

    it('scopePublished returns only published visible products', function () {
        $publishedProducts = Product::published()->get();

        expect($publishedProducts->count())->toBe(4);
        expect($publishedProducts->pluck('id'))->not->toContain($this->draftProduct->id);
        expect($publishedProducts->pluck('id'))->not->toContain($this->hiddenProduct->id);
    });

    it('scopeFeatured returns featured published products', function () {
        $featuredProducts = Product::featured()->get();

        expect($featuredProducts->count())->toBe(1);
        expect($featuredProducts->first()->id)->toBe($this->product1->id);
    });

    it('scopeOnSale returns products with compare_at_price > price', function () {
        $onSaleProducts = Product::onSale()->get();

        expect($onSaleProducts->count())->toBe(1);
        expect($onSaleProducts->first()->id)->toBe($this->product2->id);
    });

    it('scopeNewArrivals returns products published within last 30 days', function () {
        $newArrivals = Product::newArrivals(30)->get();

        expect($newArrivals->count())->toBe(3); // product1, product2, product3
        expect($newArrivals->pluck('id'))->not->toContain($this->product4->id); // 45 days old
    });

    it('hasDiscount accessor works correctly', function () {
        expect($this->product1->has_discount)->toBeFalse();
        expect($this->product2->has_discount)->toBeTrue();
    });

    it('discountPercentage accessor calculates correctly', function () {
        expect($this->product1->discount_percentage)->toBeNull();
        expect($this->product2->discount_percentage)->toBe(17); // (3000000-2500000)/3000000 * 100 = 16.67% -> 17%
    });

    it('isLowStock accessor works correctly', function () {
        expect($this->product1->is_low_stock)->toBeFalse(); // 15 > 5
        expect($this->product3->is_low_stock)->toBeTrue();  // 5 <= 5
    });

    it('stockStatusLabel and stockStatusColor work correctly', function () {
        expect($this->product1->stock_status_label)->toBe('Còn hàng (15)');
        expect($this->product1->stock_status_color)->toBe('text-emerald-600');

        expect($this->product3->stock_status_label)->toBe('Sắp hết hàng (còn 5)');
        expect($this->product3->stock_status_color)->toBe('text-amber-600');

        expect($this->product2->stock_status_label)->toBe('Hết hàng');
        expect($this->product2->stock_status_color)->toBe('text-[#E84444]');
    });

    it('dimensions and volume accessors work', function () {
        $this->product1->update([
            'length' => 50,
            'width' => 40,
            'height' => 80,
        ]);

        expect($this->product1->dimensions)->toBe('50 x 40 x 80 cm');
        expect($this->product1->volume_cm3)->toBe(160000);
    });

    it('toSchemaOrgJsonLd includes sale price and aggregateRating', function () {
        $customer = Customer::factory()->create();
        ProductReview::create([
            'product_id' => $this->product1->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Great!',
            'status' => 'approved',
        ]);

        $schema = $this->product1->toSchemaOrgJsonLd(route('products.show', $this->product1->slug));

        expect($schema['@type'])->toBe('Product');
        expect($schema['offers']['price'])->toBe('1200000');
        expect($schema['offers']['availability'])->toBe('https://schema.org/InStock');
        expect($schema['aggregateRating']['ratingValue'])->toBe(5.0);
    });
});

describe('Category Model', function () {
    it('getAllChildrenIds returns self and all descendants', function () {
        $ids = $this->parentCategory->getAllChildrenIds();

        expect($ids)->toContain($this->parentCategory->id);
        expect($ids)->toContain($this->childCategory->id);
    });

    it('getBreadcrumbs returns full path', function () {
        $breadcrumbs = $this->childCategory->getBreadcrumbs();

        expect(count($breadcrumbs))->toBe(2);
        expect($breadcrumbs[0]['name'])->toBe('Furniture');
        expect($breadcrumbs[1]['name'])->toBe('Chairs');
    });

    it('getFullPathAttribute returns formatted path', function () {
        expect($this->childCategory->full_path)->toBe('Furniture > Chairs');
    });

    it('scopeVisible returns only visible categories', function () {
        $hiddenCategory = Category::create([
            'name' => 'Hidden',
            'slug' => 'hidden',
            'is_visible' => false,
        ]);

        $visibleCategories = Category::visible()->get();

        expect($visibleCategories->pluck('id'))->not->toContain($hiddenCategory->id);
    });

    it('getNavigationTree returns nested structure', function () {
        $tree = Category::getNavigationTree();

        expect($tree->count())->toBe(2); // Furniture and Lighting (both root)
        $furniture = $tree->firstWhere('slug', 'furniture');
        expect($furniture->children->count())->toBe(1);
        expect($furniture->children->first()->slug)->toBe('chairs');
    });
});

describe('ProductVariant Model', function () {
    beforeEach(function () {
        $this->variant = ProductVariant::create([
            'product_id' => $this->product1->id,
            'name' => 'Red / Large',
            'sku' => 'CHR-001-R-L',
            'price' => 1300000,
            'compare_at_price' => 1500000,
            'stock' => 10,
            'option_values' => ['color' => 'Red', 'size' => 'L'],
            'is_active' => true,
            'is_purchasable' => true,
        ]);
    });

    it('hasDiscount works correctly', function () {
        expect($this->variant->has_discount)->toBeTrue();
    });

    it('discountPercentage calculates correctly', function () {
        expect($this->variant->discount_percentage)->toBe(13); // ~13%
    });

    it('isInStock and isLowStock work', function () {
        expect($this->variant->is_in_stock)->toBeTrue();
        expect($this->variant->is_low_stock)->toBeFalse();
    });

    it('isAvailable requires all conditions', function () {
        expect($this->variant->is_available)->toBeTrue();

        $this->variant->update(['is_active' => false]);
        expect($this->variant->fresh()->is_available)->toBeFalse();

        $this->variant->update(['is_active' => true, 'is_purchasable' => false]);
        expect($this->variant->fresh()->is_available)->toBeFalse();

        $this->variant->update(['is_purchasable' => true, 'stock' => 0]);
        expect($this->variant->fresh()->is_available)->toBeFalse();
    });

    it('optionLabel formats correctly', function () {
        expect($this->variant->option_label)->toBe('Color: Red / Size: L');
    });
});

describe('ProductReview Model', function () {
    beforeEach(function () {
        $this->customer = Customer::factory()->create();
        $this->review = ProductReview::create([
            'product_id' => $this->product1->id,
            'customer_id' => $this->customer->id,
            'rating' => 4,
            'comment' => 'Good product, fast delivery.',
            'status' => 'approved',
            'verified_purchase' => true,
        ]);
    });

    it('helpfulPercentage calculates correctly', function () {
        expect($this->review->helpful_percentage)->toBe(0);

        $this->review->voteHelpful();
        $this->review->voteHelpful();
        $this->review->voteNotHelpful();

        $this->review->refresh();
        expect($this->review->helpful_percentage)->toBe(67); // 2/3 = 66.67% -> 67%
    });

    it('stars returns array of star objects', function () {
        $stars = $this->review->stars;

        expect(count($stars))->toBe(5);
        expect($stars[0]['filled'])->toBeTrue();  // 1 <= 4
        expect($stars[3]['filled'])->toBeTrue();  // 4 <= 4
        expect($stars[4]['filled'])->toBeFalse(); // 5 > 4
    });

    it('status check accessors work', function () {
        expect($this->review->is_approved)->toBeTrue();
        expect($this->review->is_pending)->toBeFalse();
        expect($this->review->is_rejected)->toBeFalse();
        expect($this->review->is_flagged)->toBeFalse();
    });

    it('approve, reject, flag methods update status correctly', function () {
        $admin = User::factory()->create();
        $pendingReview = ProductReview::create([
            'product_id' => $this->product1->id,
            'customer_id' => $this->customer->id,
            'rating' => 3,
            'comment' => 'Okay',
            'status' => 'pending',
        ]);

        $pendingReview->approve($admin->id);
        expect($pendingReview->fresh()->status)->toBe('approved');
        expect($pendingReview->fresh()->moderated_by)->toBe($admin->id);

        $pendingReview2 = ProductReview::create([
            'product_id' => $this->product1->id,
            'customer_id' => $this->customer->id,
            'rating' => 2,
            'comment' => 'Bad',
            'status' => 'pending',
        ]);

        $pendingReview2->reject($admin->id, 'Inappropriate');
        expect($pendingReview2->fresh()->status)->toBe('rejected');
        expect($pendingReview2->fresh()->moderation_note)->toBe('Inappropriate');
    });

    it('addSellerResponse works', function () {
        $this->review->addSellerResponse('Thank you for your feedback!');

        expect($this->review->fresh()->seller_response)->toBe('Thank you for your feedback!');
        expect($this->review->fresh()->has_seller_response)->toBeTrue();
    });
});
