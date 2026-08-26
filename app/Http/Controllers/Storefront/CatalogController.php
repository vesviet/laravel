<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    protected const PER_PAGE = 12;

    protected const MAX_PER_PAGE = 48;

    /**
     * Display the product catalog with search, multi-faceted filtering, and sorting.
     */
    public function index(Request $request)
    {
        // Validate and sanitize input.
        // sort / q / search are sanitized manually below so unknown or
        // oversized values fall back gracefully instead of erroring.
        $validated = $request->validate([
            'sort' => 'sometimes|nullable|string',
            'category' => 'nullable|string|max:255',
            'q' => 'nullable|string',
            'search' => 'nullable|string',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'in_stock' => 'sometimes|boolean',
            'on_sale' => 'sometimes|boolean',
            'new_arrivals' => 'sometimes|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'attributes' => 'nullable|array',
            'per_page' => 'sometimes|integer|min:1|max:'.self::MAX_PER_PAGE,
            'page' => 'sometimes|integer|min:1',
        ]);

        // Input processing
        $allowedSorts = [
            'newest', 'oldest', 'price_asc', 'price_desc', 'name_asc',
            'name_desc', 'featured', 'best_selling', 'top_rated', 'on_sale',
        ];
        $rawSort = $validated['sort'] ?? null;
        $sort = in_array($rawSort, $allowedSorts, true) ? $rawSort : 'newest';

        // Clamp oversized search terms to keep queries bounded.
        $rawSearch = ($validated['q'] ?? null) ?? ($validated['search'] ?? null);
        if (is_string($rawSearch)) {
            $rawSearch = mb_substr(trim($rawSearch), 0, 255);
        }
        $categorySlug = $validated['category'] ?? null;
        $searchKeyword = $rawSearch !== null && $rawSearch !== '' ? $rawSearch : null;
        $minPrice = isset($validated['min_price']) ? (float) $validated['min_price'] : null;
        $maxPrice = isset($validated['max_price']) ? (float) $validated['max_price'] : null;
        $inStockOnly = $validated['in_stock'] ?? false;
        $onSaleOnly = $validated['on_sale'] ?? false;
        $newArrivalsOnly = $validated['new_arrivals'] ?? false;
        $tags = $validated['tags'] ?? [];
        $attributes = $validated['attributes'] ?? [];
        $perPage = min((int) ($validated['per_page'] ?? self::PER_PAGE), self::MAX_PER_PAGE);

        $selectedCategory = null;
        $categoryIds = [];

        if ($categorySlug) {
            $selectedCategory = Category::where('slug', $categorySlug)
                ->where('is_visible', true)
                ->with('children')
                ->first();

            if ($selectedCategory) {
                $categoryIds = $selectedCategory->getAllChildrenIds();
            } else {
                $categoryIds = [-1]; // Non-existent category returns no products
            }
        }

        // Build query
        $query = Product::published()
            ->with(['category', 'variants' => function ($q) {
                $q->where('is_active', true)
                    ->where('is_purchasable', true)
                    ->orderBy('position');
            }])
            ->when(! empty($categoryIds), fn ($q) => $q->whereIn('category_id', $categoryIds))
            ->search($searchKeyword)
            ->priceRange($minPrice, $maxPrice)
            ->when($inStockOnly, fn ($q) => $q->inStock())
            ->when($onSaleOnly, fn ($q) => $q->onSale())
            ->when($newArrivalsOnly, fn ($q) => $q->newArrivals())
            ->when(! empty($tags), fn ($q) => $q->withTags($tags))
            ->when(! empty($attributes), fn ($q) => $q->withAttributes($attributes))
            ->sortedBy($sort);

        $products = $query->paginate($perPage)->withQueryString();

        // Get categories for filter sidebar (only visible with active products)
        $categories = Category::visible()
            ->withActiveProductsCount()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get available tags for faceted navigation
        $availableTags = $this->getAvailableTags($categoryIds);

        // Price range for slider
        $priceRangeQuery = Product::published()
            ->when(! empty($categoryIds), fn ($q) => $q->whereIn('category_id', $categoryIds));
        $priceRange = [
            'min' => (int) $priceRangeQuery->clone()->min('price') ?? 0,
            'max' => (int) $priceRangeQuery->clone()->max('price') ?? 0,
        ];

        // Calculate number of active filters
        $activeFiltersCount = 0;
        if (! empty($categorySlug)) {
            $activeFiltersCount++;
        }
        if (! empty($searchKeyword)) {
            $activeFiltersCount++;
        }
        if (! is_null($minPrice) || ! is_null($maxPrice)) {
            $activeFiltersCount++;
        }
        if ($inStockOnly) {
            $activeFiltersCount++;
        }
        if ($onSaleOnly) {
            $activeFiltersCount++;
        }
        if ($newArrivalsOnly) {
            $activeFiltersCount++;
        }
        if (! empty($tags)) {
            $activeFiltersCount++;
        }
        if (! empty($attributes)) {
            $activeFiltersCount++;
        }

        // Available sort options
        $sortOptions = [
            'newest' => 'Mới nhất',
            'featured' => 'Sản phẩm nổi bật',
            'price_asc' => 'Giá: Thấp đến cao',
            'price_desc' => 'Giá: Cao đến thấp',
            'name_asc' => 'Tên: A — Z',
            'name_desc' => 'Tên: Z — A',
            'on_sale' => 'Đang giảm giá',
            'top_rated' => 'Đánh giá cao nhất',
        ];

        return view('storefront.products.index', compact(
            'products',
            'categories',
            'availableTags',
            'priceRange',
            'selectedCategory',
            'sort',
            'sortOptions',
            'searchKeyword',
            'minPrice',
            'maxPrice',
            'inStockOnly',
            'onSaleOnly',
            'newArrivalsOnly',
            'tags',
            'attributes',
            'perPage',
            'activeFiltersCount',
        ));
    }

    /**
     * Display a single product with full gallery, variants, reviews, and related products.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->whereIn('status', ['active', 'published'])
            ->where('is_visible', true)
            ->with([
                'variants' => function ($q) {
                    $q->where('is_active', true)
                        ->where('is_purchasable', true)
                        ->orderBy('position');
                },
                'category',
                'reviews' => fn ($q) => $q->where('status', 'approved')->latest()->with('customer'),
            ])
            ->firstOrFail();

        // Get approved reviews with pagination
        $reviews = $product->reviews()
            ->where('status', 'approved')
            ->with('customer')
            ->latest()
            ->paginate(10, ['*'], 'review_page');

        $relatedProducts = $product->getRelatedProducts(4);
        $schemaJsonLd = $product->toSchemaOrgJsonLd(request()->url());

        return view('storefront.products.show', compact('product', 'relatedProducts', 'schemaJsonLd', 'reviews'));
    }

    /**
     * Get available tags for faceted navigation.
     */
    protected function getAvailableTags(array $categoryIds = []): array
    {
        $query = Product::published()
            ->whereNotNull('tags');

        if (! empty($categoryIds)) {
            $query->whereIn('category_id', $categoryIds);
        }

        $products = $query->select('tags')->get();

        $tagCounts = [];
        foreach ($products as $product) {
            if (! empty($product->tags) && is_array($product->tags) && count($product->tags) > 0) {
                foreach ($product->tags as $tag) {
                    $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
                }
            }
        }

        // Sort by count descending, then alphabetically
        arsort($tagCounts);

        return array_map(fn ($count, $tag) => ['tag' => $tag, 'count' => $count], $tagCounts, array_keys($tagCounts));
    }

    /**
     * Quick view endpoint for AJAX (returns product card HTML).
     */
    public function quickView(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->whereIn('status', ['active', 'published'])
            ->where('is_visible', true)
            ->with([
                'variants' => function ($q) {
                    $q->where('is_active', true)
                        ->where('is_purchasable', true)
                        ->orderBy('position');
                },
                'category',
            ])
            ->firstOrFail();

        return view('storefront.products._quick_view', compact('product'))->render();
    }
}
