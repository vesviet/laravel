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
        $query = Product::where('status', 'published')
            ->with('category')
            ->filterByCategory($request->query('category'));

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('storefront.products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'published')
            ->with(['variants', 'category'])
            ->firstOrFail();

        return view('storefront.products.show', compact('product'));
    }
}
