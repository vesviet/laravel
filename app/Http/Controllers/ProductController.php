<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('category');

        if ($categorySlug = $request->query('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug->' . app()->getLocale(), $categorySlug));
        }

        $products = $query->orderBy('sort_order')->paginate(12);

        $categories = Category::productCategories()
            ->roots()
            ->orderBy('sort_order')
            ->get();

        return view('pages.products.index', compact('products', 'categories'));
    }

    public function show(string $slug)
    {
        $product = Product::where('slug->' . app()->getLocale(), $slug)
            ->active()
            ->with('category')
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('pages.products.show', compact('product', 'relatedProducts'));
    }
}
