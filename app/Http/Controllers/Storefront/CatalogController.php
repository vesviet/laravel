<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $allowedSorts = ['newest', 'price_asc', 'price_desc'];
        $sort = in_array($request->query('sort'), $allowedSorts) ? $request->query('sort') : 'newest';

        $query = Product::active()
            ->with('category')
            ->filterByCategory($request->query('category'));

        $query = match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('storefront.products.index', compact('products', 'categories', 'sort'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->whereIn('status', ['active', 'published'])
            ->with(['variants', 'category'])
            ->firstOrFail();

        return view('storefront.products.show', compact('product'));
    }
}
