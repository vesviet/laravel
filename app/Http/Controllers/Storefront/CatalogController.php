<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the product catalog with search, multi-faceted filtering, and sorting.
     */
    public function index(Request $request)
    {
        $allowedSorts = ['newest', 'price_asc', 'price_desc', 'name_asc', 'name_desc', 'featured'];
        $sort = in_array($request->query('sort'), $allowedSorts) ? $request->query('sort') : 'newest';

        $categorySlug = $request->query('category');
        $searchKeyword = $request->query('q') ?? $request->query('search');
        $minPrice = $request->filled('min_price') ? (float) $request->query('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->query('max_price') : null;
        $inStockOnly = $request->boolean('in_stock');

        $selectedCategory = null;
        $categoryIds = [];

        if ($categorySlug) {
            $selectedCategory = Category::where('slug', $categorySlug)->with('children')->first();
            if ($selectedCategory) {
                $categoryIds = $selectedCategory->getAllChildrenIds();
            } else {
                $categoryIds = [-1]; // Non-existent category returns no products
            }
        }

        $query = Product::active()
            ->with(['category', 'variants'])
            ->when(!empty($categoryIds), fn ($q) => $q->whereIn('category_id', $categoryIds))
            ->search($searchKeyword)
            ->priceRange($minPrice, $maxPrice)
            ->when($inStockOnly, fn ($q) => $q->inStock())
            ->sortedBy($sort);

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::withActiveProductsCount()
            ->orderBy('name')
            ->get();

        // Calculate number of active filters
        $activeFiltersCount = 0;
        if (!empty($categorySlug)) $activeFiltersCount++;
        if (!empty($searchKeyword)) $activeFiltersCount++;
        if (!is_null($minPrice) || !is_null($maxPrice)) $activeFiltersCount++;
        if ($inStockOnly) $activeFiltersCount++;

        return view('storefront.products.index', compact(
            'products',
            'categories',
            'selectedCategory',
            'sort',
            'searchKeyword',
            'minPrice',
            'maxPrice',
            'inStockOnly',
            'activeFiltersCount'
        ));
    }

    /**
     * Display a single product with full gallery, variants, reviews, and related products.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->whereIn('status', ['active', 'published'])
            ->with([
                'variants',
                'category',
                'reviews' => fn ($q) => $q->where('status', 'approved')->latest(),
            ])
            ->firstOrFail();

        $relatedProducts = $product->getRelatedProducts(4);
        $schemaJsonLd = $product->toSchemaOrgJsonLd(request()->url());

        return view('storefront.products.show', compact('product', 'relatedProducts', 'schemaJsonLd'));
    }
}
