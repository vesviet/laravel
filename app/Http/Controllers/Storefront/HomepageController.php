<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class HomepageController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::featured()
            ->with('category')
            ->take(8)
            ->get();

        $newArrivals = Product::active()
            ->latest()
            ->with('category')
            ->take(8)
            ->get();

        $categories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('storefront.home.index', compact(
            'featuredProducts',
            'newArrivals',
            'categories'
        ));
    }
}
